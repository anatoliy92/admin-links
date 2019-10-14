@extends('avl.default')

@section('css')
	<link rel="stylesheet" href="/avl/js/jquery-ui/jquery-ui.min.css">
	<link rel="stylesheet" href="/avl/js/uploadifive/uploadifive.css">
	<link rel="stylesheet" href="/avl/js/jquery-ui/timepicker/jquery.ui.timepicker.css">
@endsection

@section('main')
	<div class="card">
		<div class="card-header">
			<i class="fa fa-align-justify"></i> Редактирование: {{$link->title_ru}}
			<div class="card-actions">
				<a href="{{ route('adminlinks::sections.links.index', ['id' => $id]) }}" class="btn btn-default pl-3 pr-3" style="width: 70px;" title="Назад"><i class="fa fa-arrow-left"></i></a>
				<button type="submit" form="submit" name="button" value="save" class="btn btn-success pl-3 pr-3" style="width: 70px;" title="Сохранить"><i class="fa fa-floppy-o"></i></button>
			</div>
		</div>
		<div class="card-body">
			<form action="{{ route('adminlinks::sections.links.update', ['id' => $id, 'links' => $link->id]) }}" method="post" id="submit">
				{!! csrf_field(); !!}
				{{ method_field('PUT') }}

				<div class="row">
					<div class="col-4">
						<div class="form-group">
							{{ Form::label(null, 'Дата публикации') }}
							{{ Form::text('links_published_at', date('Y-m-d', strtotime($link->published_at)), ['class' => 'form-control datepicker']) }}
						</div>
					</div>
					<div class="col-4">
						<div class="form-group">
							{{ Form::label(null, 'Время публикации') }}
							{{ Form::text('links_published_time', date('H:i', strtotime($link->published_at)), ['class' => 'form-control timepicker']) }}
						</div>
					</div>
					@if (auth()->user()->isAdmin() || participant()->isModerator())
						<div class="col-4">
							<div class="form-group">
								{{ Form::label(null, 'Класс') }}
								{{ Form::text('links_class', $link->class, ['class' => 'form-control']) }}
							</div>
						</div>
					@endif

					@if ($section->rubric == 1)
						<div class="col-12">
							<div class="form-group">
								<label>Рубрика</label>
								<select class="form-control" name="links_rubric_id">
									<option value="0">---</option>
									@if (!is_null($rubrics))
										@foreach ($rubrics as $rubric)
											<option value="{{ $rubric->id }}" @if(old('links_rubric_id') == $rubric->id){{ 'selected' }}@elseif($link->rubric_id == $rubric->id){{ 'selected' }}@endif>{{ !is_null($rubric->title_ru) ? $rubric->title_ru : str_limit(strip_tags($rubric->description_ru), 100) }}</option>
										@endforeach
									@endif
								</select>
							</div>
						</div>
					@endif
				</div>

				<ul class="nav nav-tabs" role="tablist">
					@foreach($langs as $lang)
						<li class="nav-item">
							<a id="tabClick" class="nav-link @if($lang->key == 'ru') active show @endif" href="#title_{{ $lang->key }}" data-lang="{{$lang->key}}" data-toggle="tab">
								{{ $lang->name }}
							</a>
						</li>
					@endforeach
				</ul>
				<div class="tab-content">
					@foreach ($langs as $lang)
						<div class="tab-pane @if($lang->key == "ru") active show @endif"  id="title_{{ $lang->key }}" role="tabpanel">
							<div class="row">
								<div class="col-7">
									<div class="form-group">
										{{ Form::label(null, 'Наименование') }}
										{{ Form::text('links_title_' . $lang->key, $link->{'title_' . $lang->key} ?? null, ['class' => 'form-control']) }}
									</div>
								</div>

								<div class="col-5">
									<div class="form-group">
										{{ Form::label(null, 'Ссылка') }}
										{{ Form::text('links_link_' . $lang->key, $link->{'link_' . $lang->key} ?? null, ['class' => 'form-control']) }}
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-9">
									{{ Form::textarea('links_description_' . $lang->key, $link->{'description_' . $lang->key} ?? '', ['class' => 'tinymce']) }}
								</div>

								<div class="col-3">
									<div class="form-group">
										<div class="block--file-upload">
											<input id="upload--photos--{{ $lang->key }}" data-lang="{{ $lang->key }}" name="upload" type="file" />
											<script type="text/javascript">
												$(document).ready(function() {
													uploadPhoto('<?=$lang->key;?>', '<?=$id;?>', '<?=$link->id;?>');
												});
											</script>
										</div>
										<div class="row">
											<div id="link_photo-{{ $lang->key }}" class="col-lg-12">
												@php $image = $link->media('image')->whereLang($lang->key)->first(); @endphp
												@if ($image)
													<div class="card" id="mediaSortable_{{ $image->id }}">
														<div class="card-header text-right">
															<a href="#" class="deleteMedia" data-id="{{ $image->id }}"><i class="fa fa-trash-o"></i></a>
														</div>
														<div class="card-body p-0">
															<img src="/image/resize/300/300/{{ $image->url }}" style="width:100%">
														</div>
													</div>
												@endif
											</div>
										</div>
									</div>

								</div>
							</div>
						</div>
					@endforeach
				</div></br>
			</form>
		</div>
	</div>
@endsection

@section('js')
	<script src="/avl/js/jquery-ui/jquery-ui.min.js" charset="utf-8"></script>
	<script src="/avl/js/uploadifive/jquery.uploadifive.min.js" charset="utf-8"></script>

	<script src="{{ asset('vendor/adminlinks/js/links.js') }}" charset="utf-8"></script>
	<script src="/avl/js/tinymce/tinymce.min.js" charset="utf-8"></script>

	<script src="/avl/js/jquery-ui/timepicker/jquery.ui.timepicker.js" charset="utf-8"></script>
@endsection
