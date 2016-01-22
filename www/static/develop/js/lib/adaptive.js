$(document).ready(function(){
	var html = $('html'),
		x = window.innerWidth; // Определяем разрешение экрана
	
	if (x <=767) {
		html.addClass('mobile');
	}else if (x >767 && x <= 991) {
		html.addClass('tablet');
	}else html.addClass('desktop');

});

function mobile(action){
	if ($('html').hasClass('mobile')) {
		action();
	}
}

function tablet(action){
	if ($('html').hasClass('tablet')) {
		action();
	}
}

function desktop(action){
	if ($('html').hasClass('desktop')) {
		action();
	}
}


$(document).ready(function(){
	desktop(function(){
		$(this).css('background', '#333');
	});
});