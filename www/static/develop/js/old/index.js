$(document).ready(function(){
	/* auth block */
	if ($('#reg_captcha').length) {
		$('#reg_captcha').html(get_captcha('reg_form'));
	}

	$('#show_login_btn').click(function(){
		$('#registration_block').hide();
		$('#login_block').show();
		return false;
	});

	$('#show_registration_btn').click(function(){
		$('#registration_block').show();
		$('#login_block').hide();
		return false;
	});

	$('#hide_reg_errors').click(function(){
		$('#reg_errors').hide();
		$('#registration_form').show();
		$('#reg_errors').find('p.error').html('');
		return false;
	});

	$('#registration_form').submit(function(e){
		e.preventDefault();

		$.post($(this).attr('action'), $(this).serialize(), function(json){
			if (json.error) {
				$('#reg_errors').show();
				$('#registration_form').hide();
				$('#reg_errors').find('p.error').html(json.error);
				if (json.code > 500) {
					$('#reg_errors').find('#hide_reg_errors').hide();
				} else {
					$('#reg_errors').find('#hide_reg_errors').show();
				}
			} else {
				$('#registration_block').hide();
				$('#reg_success').show();
			}
		}, 'json')

		return false;
	});

});

$(window).load(function(){
   $('.str_wrap').liMarquee();
}); //$(window).load