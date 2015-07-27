$(function(){
    // load captcha
    if ($('#reg_captcha').length || $('#forgot_captcha').length) {
        //$('#reg_captcha').html(get_captcha('reg_form'));
        $('#forgot_captcha').html(get_captcha('forgot_form'));
    }

    // reload captcha button
    $('#reg_form .captcha_reload').click(function(){
        $('#reg_captcha').html(get_captcha('reg_form'));
        return false;
    });
// reload captcha button
    $('#forgot_form .captcha_reload').click(function(){
        $('#forgot_captcha').html(get_captcha('forgot_form'));
        return false;
    });
});


function get_captcha(form_id) {
    var img;
    $.ajax('/ajax/captcha_reload',
        {
            type: 'POST',
            data: {form_id:form_id,half:true},
            success: function(data) {
                img = data;
            },
            async: false
        }
    );

    return img;
}