@extends('avl.default')

@section('main')
	<div class="card">
		<div class="card-header">
			<i class="fa fa-align-justify"></i> Просмотр : {{ $link->title_ru }}
			<div class="card-actions">
				<a href="{{ route('adminlinks::sections.links.index', ['id' => $id]) }}" class="btn btn-default pl-3 pr-3" style="width: 70px;" title="Назад"><i class="fa fa-arrow-left"></i></a>
			</div>
		</div>
		<div class="card-body">

			<div class="row">
				<div class="col-4">
					<div class="form-group">
						{{ Form::label(null, 'Дата публикации') }}
						{{ Form::text(null, date('Y-m-d', strtotime($link->published_at)), ['class' => 'form-control bg-light', 'disabled' => true]) }}
					</div>
				</div>
				<div class="col-4">
					<div class="form-group">
						{{ Form::label(null, 'Время публикации') }}
						{{ Form::text(null, date('H:i', strtotime($link->published_at)), ['class' => 'form-control bg-light', 'disabled' => true]) }}
					</div>
				</div>
				<div class="col-4">
					<div class="form-group">
						{{ Form::label(null, 'Класс') }}
						{{ Form::text(null, $link->class, ['class' => 'form-control bg-light', 'disabled' => true]) }}
					</div>
				</div>
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
								<div class="col-1">
									<div class="form-group">
										{{ Form::label('links_good_' . $lang->key, 'Вкл') }} <br/>
										<label class="switch switch-3d switch-primary">
											{{ Form::checkbox('links_good_' . $lang->key, true, $link->{'good_' . $lang->key} ?? false, ['class' => 'switch-input', 'disabled' => true]) }}
											<span class="switch-label"></span>
											<span class="switch-handle"></span>
										</label>
									</div>
								</div>

								<div class="col-6">
									<div class="form-group">
										{{ Form::label(null, 'Наименование') }}
										{{ Form::text('links_title_' . $lang->key, $link->{'title_' . $lang->key} ?? null, ['class' => 'form-control bg-light', 'disabled' => true]) }}
									</div>
								</div>

								<div class="col-5">
									<div class="form-group">
										{{ Form::label(null, 'Ссылка') }}
										{{ Form::text('links_link_' . $lang->key, $link->{'link_' . $lang->key} ?? null, ['class' => 'form-control bg-light', 'disabled' => true]) }}
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-9">
									{{ Form::textarea('links_description_' . $lang->key, $link->{'description_' . $lang->key} ?? '', ['class' => 'tinymce', 'disabled' => true]) }}
								</div>

								<div class="col-3">
									@php $image = $link->media('image')->whereLang($lang->key)->first(); @endphp
									@if ($image)
										<div class="card">
											<div class="card-body p-0">
												<img src="{{ env('STORAGE_URL') . $image->url }}" style="width:100%">
											</div>
										</div>
									@endif
								</div>
							</div>


						</div>
					@endforeach
				</div>

		</div>
	</div>
@endsection

@section('js')
	<script src="/avl/js/jquery-ui/jquery-ui.min.js" charset="utf-8"></script>
	<script src="/avl/js/tinymce/tinymce.min.js" charset="utf-8"></script>
@endsection
