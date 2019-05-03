<?php namespace Avl\AdminLinks\Controllers\Admin\Ajax;

use App\Http\Controllers\Avl\AvlController;
	use App\Models\{Sections, Media, Langs};
	use Illuminate\Support\Facades\Storage;
	use Avl\AdminLinks\Models\Links;
	use Illuminate\Http\Request;
	use App\Traits\MediaTrait;
	use Illuminate\Http\File;
	use Carbon\Carbon;
	use Image;

class MediaController extends AvlController
{
		use MediaTrait;

		/**
		 * Загрузка изображений
		 * @param  Request $request
		 * @return JSON
		 */
		public function linksImages (Request $request)
		{
			if ($request->Filedata->getSize() < config('adminlinks.max_file_size')) {

				if (in_array(strtolower($request->Filedata->extension()), config('adminlinks.valid_image_types'))) {

					$links = Links::where('section_id', $request->input('section_id'))->find($request->input('id'));

					if ($links) {

							// Если изображение было загружено ранее то сначала удаляем его
							$media = $links->media('image')->whereLang($request->input('lang'))->first();
							if ($media) { $this->deleteMedia ($media->id, $request); }

							$picture = new Media;
							$picture->model = 'Avl\AdminLinks\Models\Links';
							$picture->model_id = $links->id;
							$picture->type = 'image';
							$picture->sind = 1;
							$picture->lang = $request->input('lang');
							$picture->title_ru = $request->Filedata->getClientOriginalName();
							$picture->published_at = Carbon::now();

							if ($picture) {

								/* Загружаем файл и получаем путь */
								$path = $request->Filedata->store(config('adminlinks.path_to_image'));

								$img = Image::make(Storage::get($path));
								$img->resize(1000, 1000, function ($constraint) {
									$constraint->aspectRatio();
									$constraint->upsize();
								})->stream();

								Storage::put($path, $img);

								$picture->url = $path;

								if ($picture->save()) {
									return [
										'success' => true,
										'file' => Media::find($picture->id)->toArray(),
										'storage' => env('STORAGE_URL')
									];
								}

								$picture->delete();
							}
					}

					return ['errors' => ['Ошибка загрузки, обратитесь к администратору.']];
				}

				return ['errors' => ['Ошибка загрузки, формат изображения не допустим для загрузки.']];
			}

			return ['errors' => ['Размер фотографии не более <b>12-и</b> мегабайт.']];
		}

}
