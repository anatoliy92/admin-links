<?php namespace Avl\AdminLinks\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Avl\AvlController;
use App\Models\{Sections, Langs};
use Avl\AdminLinks\Models\Links;
use App\Traits\MediaTrait;
use Cache;

class LinksController extends AvlController
{

	use MediaTrait;

	protected $langs = null;

	public function __construct (Request $request) {
		parent::__construct($request);

		$this->langs = Langs::get();
	}

	public function index($id)
	{
		$section = Sections::whereId($id)->firstOrFail();

		$this->authorize('view', $section);

		$links = $section->links()->orderBy('published_at', 'DESC');

		return view('adminlinks::links.index', [
			'langs' => $this->langs,
			'links' => $links->paginate(20),
			'section' => $section,
			'id' => $id
		]);
	}

	public function create($id)
	{
		$this->authorize('create', Sections::findOrFail($id));

		return view('adminlinks::links.create', [
			'langs' => $this->langs,
			'id' => $id
		]);
	}

	public function store(Request $request, $id)
	{
		$this->authorize('create', Sections::findOrFail($id));

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
			$links->{'good_' . $lang->key}        = $post['links_good_' . $lang->key] ?? false;
			$links->{'title_' . $lang->key}       = $post['links_title_' . $lang->key] ?? null;
			$links->{'link_' . $lang->key}        = $post['links_link_' . $lang->key] ?? null;
			$links->{'description_' . $lang->key} = $post['links_description_' . $lang->key] ?? null;
		}

		$links->published_at = $post['links_published_at'] . ' ' . $post['links_published_time'];
		$links->class = $post['links_class'];
		$links->section_id = $id;

		if ($links->save()) {
			if ($post['button'] == 'save') {
				return redirect()->route('adminlinks::sections.links.create', ['id' => $id])->with(['success' => ['Сохранение прошло успешно!']]);
			}
			if ($post['button'] == 'edit') {
				return redirect()->route('adminlinks::sections.links.edit', ['id' => $id, 'link' => $links->id])->with(['success' => ['Сохранение прошло успешно!']]);
			}
			return redirect()->route('adminlinks::sections.links.index', ['id' => $id])->with(['success' => ['Сохранение прошло успешно!']]);
		}

		return redirect()->route('adminlinks::sections.links.create', ['id' => $id])->with(['errors' => ['Что-то пошло не так.']]);
	}

	public function edit($id, $link_id)
	{
		$section = Sections::whereId($id)->firstOrFail();

		$this->authorize('update', $section);

		$link = $section->links()->findOrFail($link_id);

		return view('adminlinks::links.edit', [
			'section' => $section,
			'id' => $id,
			'langs' => $this->langs,
			'link' => $link,
		]);
	}

	public function update(Request $request, $id, $link_id)
	{
		$section = Sections::whereId($id)->firstOrFail();

		$this->authorize('update', $section);

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

		$links = $section->links()->findOrFail($link_id);

		foreach ($this->langs as $lang) {
			$links->{'good_' . $lang->key}        = $data['links_good_' . $lang->key] ?? false;
			$links->{'title_' . $lang->key}       = $data['links_title_' . $lang->key] ?? null;
			$links->{'link_' . $lang->key}        = $data['links_link_' . $lang->key] ?? null;
			$links->{'description_' . $lang->key} = $data['links_description_' . $lang->key] ?? null;

			// Очищаем файлы кеша
			if (Cache::has('col-links-' . $lang->key . '-' . $section->area_id . '-' . $section->alias)) {
				Cache::forget('col-links-' . $lang->key . '-' . $section->area_id . '-' . $section->alias);
			}
		}

		$links->published_at = $data['links_published_at'] . ' ' . $data['links_published_time'];
		$links->class = $data['links_class'];

		if ($links->save()) {
			return redirect()->route('adminlinks::sections.links.index', ['id' => $id])->with(['success' => ['Сохранение прошло успешно!']]);
		}
		return redirect()->back()->with(['errors' => ['Что-то пошло не так.']]);
	}

	public function show($id, $link_id)
	{
		$section = Sections::whereId($id)->firstOrFail();

		$this->authorize('view', $section);

		$link = $section->links()->findOrFail($link_id);

		return view('adminlinks::links.show', [
			'section' => $section,
			'id' => $id,
			'langs' => $this->langs,
			'link' => $link,
		]);
	}

	public function destroy ($id, $link_id, Request $request)
	{
		$section = Sections::whereId($id)->firstOrFail();

		$this->authorize('delete', $section);

		$link = $section->links()->findOrFail($link_id);
		if ($link) {

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
