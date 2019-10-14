<?php namespace Avl\AdminLinks\Controllers\Admin;

use Darmen\Moderation\Services\ModerationService;
use App\Http\Controllers\Avl\AvlController;
use App\Models\{Sections, Langs, Rubrics};
use Avl\AdminLinks\Models\Links;
use Illuminate\Http\Request;
use App\Traits\MediaTrait;
use Cache;

class LinksController extends AvlController
{

	use MediaTrait;

	protected $langs = null;

	protected $section;

    /** @var ModerationService  */
    private $moderationService;

	public function __construct (Request $request, ModerationService $moderationService) {
		parent::__construct($request);

		$this->langs = Langs::get();

        $this->section = Sections::find($request->id) ?? null;

        $this->moderationService = $moderationService;

        view()->share([ 'section' => $this->section ]);
	}

	public function index()
	{
		$this->authorize('view', $this->section);

		$links = $this->section->links()->orderBy('published_at', 'DESC');

		return view('adminlinks::links.index', [
			'langs' => $this->langs,
			'links' => $links->paginate(20),
			'section' => $this->section,
            'rubrics' => array_add(toSelectTransform(Rubrics::select('id', 'title_ru')->where('section_id', $this->section->id)->get()->toArray()), 0, 'Ссылки без рубрики'),
			'id' => $this->section->id
		]);
	}

	public function create()
	{
		$this->authorize('create', $this->section);

		return view('adminlinks::links.create', [
			'langs' => $this->langs,
            'section' => $this->section,
            'rubrics' => $this->section->rubrics()->orderBy('published_at', 'DESC')->get(),
			'id' => $this->section->id
		]);
	}

	public function store(Request $request)
	{
		$this->authorize('create', $this->section);

		$post = $request->input();

		$this->validate(request(), [
			'button' => 'required|in:add,save,edit',
			'links_published_at' => 'required|date_format:"Y-m-d"',
			'links_published_time' => 'required|date_format:"H:i"',
			'links_class' => '',
			'links_title_ru' => '',
			'links_link_ru' => 'required',
			'links_description_ru' => '',
		]);

		$links = new Links;

		foreach ($this->langs as $lang) {
			$links->{'title_' . $lang->key}       = $post['links_title_' . $lang->key] ?? null;
			$links->{'link_' . $lang->key}        = $post['links_link_' . $lang->key] ?? null;
			$links->{'description_' . $lang->key} = $post['links_description_' . $lang->key] ?? null;

			// Очищаем файлы кеша
			if (Cache::has('col-links-' . $lang->key . '-' . $this->section->alias)) {
				Cache::forget('col-links-' . $lang->key . '-' . $this->section->alias);
			}
		}

		$links->published_at = $post['links_published_at'] . ' ' . $post['links_published_time'];
		$links->section_id = $this->section->id;
        if (isset($post['links_class'])) {
            $links->class = $post['links_class'];
        }

        if (isset($post['links_rubric_id']) && ($post['links_rubric_id'] > 0)) {
            $links->rubric_id = $post['links_rubric_id'];    // проставляему рубрику если ее выбрали
        }

		if ($links->save()) {

            $participant = participant();
            $moderationMessage = ' Отправлено на модерацию';

            if ($participant->isAutomaticPublishEnabled()) {
                $moderationMessage = ' Согласовано и опубликовано автоматически';
                $links->publish();
                $links->save();
            } else {
                $review = $this->moderationService->sendToReview(participant(), $links, true, [], $links->getAttributes());

                if ($participant->isAutomaticApprovalEnabled()) {
                    $moderationMessage = ' Согласовано автоматически';
                    $this->moderationService->approve($review, now(), $moderationMessage, true);
                    $newReview = $this->moderationService->sendToReview($review->getAuthor(), $links, true, [], $links->getAttributes());
                }
            }
            switch ($post['button']) {
                case 'save': { return redirect()->route('adminlinks::sections.links.create', ['id' => $this->section->id])->with(['success' => ['Сохранение прошло успешно!' . $moderationMessage]]); }
                case 'edit': { return redirect()->route('adminlinks::sections.links.edit', ['id' => $this->section->id, 'link' => $links->id])->with(['success' => ['Сохранение прошло успешно!'.$moderationMessage]]); }
                default: { return redirect()->route('adminlinks::sections.links.index', ['id' => $this->section->id])->with(['success' => ['Сохранение прошло успешно!'.$moderationMessage]]); }
            }
		}

		return redirect()->route('adminlinks::sections.links.create', ['id' => $this->section->id])->with(['errors' => ['Что-то пошло не так.']]);
	}

	public function edit(Request $request)
	{
		$this->authorize('update', $this->section);

		$link = $this->section->links()->findOrFail($request->link);

        if ($this->moderationService->isModelUnderReview($link)) {
            return redirect()->back()->withErrors([
                sprintf("Действие невозможно, так как %s в процессе модерации", $link->getModerationModelName())
            ]);
        }

		return view('adminlinks::links.edit', [
			'section' => $this->section,
			'id' => $this->section->id,
            'rubrics' => $this->section->rubrics()->orderBy('published_at', 'DESC')->get(),
			'langs' => $this->langs,
			'link' => $link,
		]);
	}

	public function update(Request $request)
	{
		$this->authorize('update', $this->section);

		$data = $request->input();
		$this->validate(request(), [
			'button' => 'required|in:add,save',
			'links_published_at' => 'required|date_format:"Y-m-d"',
			'links_published_time' => 'required|date_format:"H:i"',
			'links_class' => '',
			'links_title_ru' => '',
			'links_link_ru' => 'required',
			'links_description_ru' => ''
		]);

		$links = $this->section->links()->findOrFail($request->link);

		foreach ($this->langs as $lang) {
			$links->{'title_' . $lang->key}       = $data['links_title_' . $lang->key] ?? null;
			$links->{'link_' . $lang->key}        = $data['links_link_' . $lang->key] ?? null;
			$links->{'description_' . $lang->key} = $data['links_description_' . $lang->key] ?? null;

			// Очищаем файлы кеша
			if (Cache::has('col-links-' . $lang->key . '-' . $this->section->alias)) {
				Cache::forget('col-links-' . $lang->key . '-' . $this->section->alias);
			}
		}
        if (isset($data['links_rubric_id']) && ($data['links_rubric_id'] > 0)) {
            $links->rubric_id = $data['links_rubric_id'];
        } else {
            $links->rubric_id = null;
        }

		$links->published_at = $data['links_published_at'] . ' ' . $data['links_published_time'];
		if (isset($data['links_class'])) {
            $links->class = $data['links_class'];
        }

        /* Moderation */
            $participant = participant();
            if ($participant->isAutomaticPublishEnabled()) {
                $links->publish();
                $links->save();
                return redirect()->route('adminlinks::sections.links.index', ['id' => $this->section->id])->with(['success' => ['Объект был опубликован автоматически.']]);
            }

            $originalAttributes = $links->getOriginal();
            $review = $this->moderationService->sendToReview(participant(), $links, true, $originalAttributes, $links->getDirty());

            if ($participant->isAutomaticApprovalEnabled()) {
                $this->moderationService->approve($review, now(), 'Согласовано автоматически', true);
                $newReview = $this->moderationService->sendToReview($review->getAuthor(), $links, true, $originalAttributes, $links->getDirty());

                return redirect()->route('adminlinks::sections.links.index', ['id' => $this->section->id ])->with(['success' => ['Объект был согласован автоматически.']]);
            }
        /* Moderation */

        return redirect()->route('adminlinks::sections.links.index', ['id' => $this->section->id])->with(['success' => ['Объект направлен на модерацию.']]);
	}

	public function show(Request $request)
	{
		$this->authorize('view', $this->section);

		$link = $this->section->links()->findOrFail($request->link);

		return view('adminlinks::links.show', [
			'section' => $this->section,
			'id' => $this->section->id,
			'langs' => $this->langs,
			'link' => $link,
		]);
	}

	public function destroy (Request $request)
	{
		$this->authorize('delete', $this->section);

		$link = $this->section->links()->findOrFail($request->link);

		if ($link) {
            if ($this->moderationService->isModelUnderReview($link)) {
                return [
                    'errors' => sprintf("Действие невозможно, так как %s в процессе модерации", $link->getModerationModelName())
                ];
            }

			// Получаем все картинки к записи и удаляем сначала их
			$images = $link->media('image')->get();
			if ($images) {
				foreach ($images as $image) {
					$this->deleteMedia ($image->id, $request);
				}
			}

			if ($link->delete()) { return ['success' => ['Ссылка удалена!']]; }
		}
		return ['errors' => ['Ошибка при удалении']];
	}

}
