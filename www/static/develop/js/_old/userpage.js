$(document).ready(function() {
//	$('.mbanner .nav').click(function(e){e.preventDefault();$(this).toggleClass('toggle');
//		$('.m_header').toggle();
//		$('.input-seach').css('padding-left', ($('.seach-bl .cusel').width()+16));
//
//	});

	$('.bg-mbanner-redact div .del').click(function(e){e.preventDefault();
		$.getJSON('/ajax/delete_userpage_image', function(json){
			$('#main_image').hide();
			$('.mbanner .info-block').css('height', 'auto');
			$('.mbanner .info-block').addClass('no-photo');
		});
	});

	$('.bg-mbanner-redact .reduct').click(function(e){e.preventDefault();
		$('.addphoto-popup').fadeIn();
		$('.popup-layer').fadeIn();
	});

	$('#samples img').click(function(){
		var filename = $(this).data('img');
		$.getJSON('/ajax/save_userpage_image', {filename:filename}, function(json){
			$('#main_image').attr('src', json.filepath);
			$('#main_image').show();
			$('.addphoto-popup').fadeOut();
			$('.popup-layer').fadeOut();
		});
	});

	$('#banner_input').live('change', function(e){
		$('.addphoto-popup .error').hide();
		$.ajaxFileUpload({
			url:'/user/upload_userpage_banner', 
			secureuri:false,
			fileElementId:'banner_input',
			dataType: 'json',
			success: function (json, status) {
				if (json.code == 200) {
					$('.addphoto-popup').fadeOut();
					$('.popup-layer').fadeOut();
					$('#main_image').attr('src', json.filepath);
					$('#main_image').show();
				} else if (json.error) {
					$('.addphoto-popup .error').text(json.error).show();
				}
			},
			error: function (data, status, e) {
				console.log(data.responseText);
			}
		});
	});
});