$(document).ready(function() {

	$("body").on('click', '.deletePhoto', function(e) {
		e.preventDefault();
		var self       = $(this),
				id         = self.data('id'),
				model      = self.data('model');
				lang      = self.data('lang');
		$.ajax({
				url: '/ajax/deletePhotoLinks/'+ id,
				type: 'POST',
				async: false,
				dataType: 'json',
				data : { _token: $('meta[name="_token"]').attr('content'), model: model, lang: lang},
				success: function(data) {
					if (data.errors) {
						messageError(data.errors);
					} else {
						$("#link_photo-"+lang).empty();
					}
				}
		});
	});

	$('body').on('click', '.remove--link', function(e) {
		e.preventDefault();
		var id = $(this).attr('data-id');
		var section = $(this).attr('data-section');

		$.ajax({
			url: '/sections/' + section + '/links/' + id,
			type: 'DELETE',
			dataType: 'json',
			data : { _token: $('meta[name="_token"]').attr('content')},
			success: function(data) {
				if (data.success) {
					$("#links--item-" + id).remove();
					messageSuccess(data.success);
				} else {
					messageError(data.errors);
				}
			}
		});
	});

});


function uploadPhoto(lang, section_id, link_id) {
	$('#upload--photos--'+lang).uploadifive({
		'auto' : true,
		'removeCompleted' : true,
		'buttonText'	: 'Выберите Изображение',
		'height'	    : '100%',
		'width'			: '100%',
		'checkScript'	: '/ajax/check',
		'uploadScript'	: '/ajax/links-images',
		'fileType'		: 'image/*',
		'formData'		: {
			'_token'      : $('meta[name="_token"]').attr('content'),
			'section_id'  : section_id,
			'id'	  : link_id,
			'lang'	  : lang
		},
		'folder'		: '/uploads/tmps/',

		'onUploadComplete' : function( file, data ) {
			var $data = JSON.parse(data);
			if ($data.success) {
				var html =
				'<div class="card" id="mediaSortable_' + $data.file.id + '">'+
					'<div class="card-header text-right">'+
						'<a href="#" class="deleteMedia" data-id="' + $data.file.id + '"><i class="fa fa-trash-o"></i></a>'+
					'</div>'+
					'<div class="card-body p-0">'+
						'<img src="/image/resize/300/300/' + $data.file.url + '" style="width:100%">'+
					'</div>'+
				'</div>';
				$("#link_photo-"+lang).empty();
				$('#link_photo-'+lang).prepend(html);
			}

			if ($data.errors) {
				messageError($data.errors);
			}
		}
	});
}
