$( document ).ready(function() {
   
    //скрипт для выпдающего меню - todo переделать этот ужас
    $('.iLight-nav').on('click', function(){
            if ($(this).closest('.iLight').hasClass('iLight-disable')){return false}else{
            if  (   $(this).closest('.iLight').hasClass('active')   )
            {   
                $(this).closest('.iLight').find('.iLight-cont').not('.iLight-disable .iLight-cont').hide('slow');$(this).closest('.iLight').removeClass('active');
            }
            else
            {
                    $('.iLight').removeClass('active'); $('.iLight-cont').not('.iLight-disable .iLight-cont').fadeOut();
                    $(this).closest('.iLight').find('.iLight-cont').show('slow');
                    $(this).closest('.iLight').addClass('active');
            }
        }   
    });
	contminheight();
    
});

$(window).resize(function() {contminheight()});


function contminheight(){
	$('.m_content').css('min-height', ($(window).height()-$('.m_header').height()-$('.m_footer').height()-15));
}