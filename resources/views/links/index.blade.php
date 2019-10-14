@extends('avl.default')

@section('js')
	<script src="{{ asset('vendor/adminlinks/js/links.js') }}" charset="utf-8"></script>
@endsection

@section('main')
	<div class="card">
		<div class="card-header">
			<i class="fa fa-align-justify"></i> {{ $section->name_ru }}
			@can('create', $section)
				<div class="card-actions">
					<a href="{{ route('adminlinks::sections.links.create', ['id' => $id]) }}" class="w-100 pl-4 pr-4 bg-primary text-white" title="Добавить"><i class="fa fa-plus"></i></a>
				</div>
			@endcan
		</div>
		<div class="card-body">
			@if ($links)
				@php $isAdmin = auth()->user()->isAdmin(); @endphp
				@php $participant = participant(); @endphp

				<table class="table table-bordered">
					<thead>
						<tr>
							@foreach($langs as $lang)
								<th class="text-center" style="width: 20px">{{ $lang->key }}</th>
							@endforeach
							<th class="text-center">Наименование ссылки</th>
							@if ($section->rubric == 1)<th class="text-center" style="width: 160px;">Рубрика</th>@endif
							<th class="text-center" style="width: 160px">Дата публикации</th>
							<th class="text-center align-middle" width="130">Оператор</th>
							<th class="text-center align-middle" width="130">Руководитель</th>
							<th class="text-center align-middle" width="130">Модератор</th>
							<th class="text-center" style="width: 100px;">Действие</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($links as $link)
							<tr class="position-relative" id="links--item-{{ $link->id }}">
								@foreach($langs as $lang)
									<td class="text-center">
										@if ($isAdmin || $participant->isModerator())
											<a class="change--status" href="#" data-id="{{ $link->id }}" data-model="Avl\AdminLinks\Models\Links" data-lang="{{$lang->key}}">
												<i class="fa @if ($link->{'good_' . $lang->key}){{ 'fa-eye' }}@else{{ 'fa-eye-slash' }}@endif"></i>
											</a>
										@else
											<i class="fa @if ($link->good){{ 'fa-eye' }}@else{{ 'fa-eye-slash' }}@endif"></i>
										@endif
									</td>
								@endforeach
								<td>{{ $link->title_ru }}<br/><span class="text-secondary">{{ $link->link_ru }}</span></td>
								@if ($section->rubric == 1)
									<td class="text-center">@if(!is_null($link->rubric))@if(!is_null($link->rubric->title_ru)){{ $link->rubric->title_ru }}@else{{ str_limit(strip_tags($link->rubric->description_ru), 70) }}@endif @endif</td>
								@endif
								<td>{{ $link->published_at }}</td>
								<td class="text-center">{{ $link->getAuthor() }}</td>
								<td class="text-center">{{ $link->getSupervisor() }}</td>
								<td class="text-center">{{ $link->getModerator() }}</td>
								<td class="text-right">
									<div class="btn-group" role="group">
										@can('view', $section) <a href="{{ route('adminlinks::sections.links.show', ['id' => $id, 'link_id' => $link->id]) }}" class="btn btn btn-outline-primary" title="Просмотр"><i class="fa fa-eye"></i></a> @endcan
										@if ($link->hasPermissionModel())
											@can('update', $section) <a href="{{ route('adminlinks::sections.links.edit', ['id' => $id, 'link_id' => $link->id]) }}" class="btn btn btn-outline-success" title="Изменить"><i class="fa fa-edit"></i></a> @endcan
										@endif
										@can('delete', $section) <a href="#" class="btn btn btn-outline-danger remove--record" title="Удалить"><i class="fa fa-trash"></i></a> @endcan
									</div>
									@can('delete', $section)
										<div class="remove-message">
											<span>Вы действительно желаете удалить запись?</span>
											<span class="remove--actions btn-group btn-group-sm">
												<button class="btn btn-outline-primary cancel"><i class="fa fa-times-circle"></i> Нет</button>
												<button class="btn btn-outline-danger remove--link" data-id="{{ $link->id }}" data-section="{{ $id }}"><i class="fa fa-trash"></i> Да</button>
											</span>
										</div>
									 @endcan
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>

				<div class="d-flex justify-content-end">
					{{ $links->links('vendor.pagination.bootstrap-4') }}
				</div>
			@endif
		</div>
	</div>
@endsection
