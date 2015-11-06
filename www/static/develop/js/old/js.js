function css_browser_selector(u){var ua=u.toLowerCase(),is=function(t){return ua.indexOf(t)>-1},g='gecko',w='webkit',s='safari',o='opera',m='mobile',h=document.documentElement,b=[(!(/opera|webtv/i.test(ua))&&/msie\s(\d)/.test(ua))?('ie ie'+RegExp.$1):is('firefox/2')?g+' ff2':is('firefox/3.5')?g+' ff3 ff3_5':is('firefox/3.6')?g+' ff3 ff3_6':is('firefox/3')?g+' ff3':is('gecko/')?g:is('opera')?o+(/version\/(\d+)/.test(ua)?' '+o+RegExp.$1:(/opera(\s|\/)(\d+)/.test(ua)?' '+o+RegExp.$2:'')):is('konqueror')?'konqueror':is('blackberry')?m+' blackberry':is('android')?m+' android':is('chrome')?w+' chrome':is('iron')?w+' iron':is('applewebkit/')?w+' '+s+(/version\/(\d+)/.test(ua)?' '+s+RegExp.$1:''):is('mozilla/')?g:'',is('j2me')?m+' j2me':is('iphone')?m+' iphone':is('ipod')?m+' ipod':is('ipad')?m+' ipad':is('mac')?'mac':is('darwin')?'mac':is('webtv')?'webtv':is('win')?'win'+(is('windows nt 6.0')?' vista':''):is('freebsd')?'freebsd':(is('x11')||is('linux'))?'linux':'','js']; c = b.join(' '); h.className += ' '+c; return c;}; css_browser_selector(navigator.userAgent);

if(navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i)) {
$('#viewport').attr('content',' ');}

$(document).ready(function() {
$(window).resize(function() {/*$('.seach-bl input').val($(window).width());*/setTimeout(function(){tablogic();}, 100);tabheight();contminheight();mmenu();centerthis();getasidepos();ibannershow();ibannerhide(); contminheight2();
setTimeout(function() { contminheight(); }, 2500)
$('.popup-layer').height($(window).height());
$('.input-seach').css('padding-left', ($('.seach-bl .cusel').width()+16));
//$('.mbanner .info-block > img').css('margin-left', -$(this).width()/2);

		 mbanner_height();
});
$(window).load(function(){
	/*if($('#foo').size()>0){tabcarusel()}
	if($('.m-slider #li2 .scont').size()>0){mslider2()}
	if($('.m-slider #li1 .scont').size()>0){mslider1()}
	if($('.slyder.style1 .cont').size()>0){slyder()}
	if($('.slyder.style2 .cont').size()>0){slyder2()}
	if($('.slyder.style3 .cont').size()>0){slyder3()}
	if($('.slyder.st4 .cont').size()>0){slyder4()}*/
	
	 mbanner_height();
		
});

//btn preventdefauld
$('.btn-white').on('click', function(e){e.preventDefault();})

//1//crossbrowser html5 placeholder
 $('[placeholder]').focus(function() {var input = $(this);if (input.val() == input.attr('placeholder')) {input.val('');input.removeClass('placeholder');} }).blur(function() {var input = $(this);if (input.val() == '' || input.val() == input.attr('placeholder')) {input.addClass('placeholder');input.val(input.attr('placeholder'));}}).blur().parents('form').submit(function() {   $(this).find('[placeholder]').each(function() { var input = $(this);if (input.val() == input.attr('placeholder')) {input.val('');}})}); //end crossbrowser html5 placeholder
 
//2// jQuery(document).ready(function(){
var params = {
		changedEl: "select.cusel-plagin",
		visRows: 12,
		scrollArrows: true
	}
	cuSel(params);
//3//
$('.seach-bl .cusel').css({'max-width' : ($(this).find('.cuselText').width()+42) , 'min-width' : ($('.seach-bl .cusel').find('.cuselText').width()+42)});$('.input-seach').css('padding-left', ($('.seach-bl .cusel').width()+16));
$('.seach-bl .cusel').on('change', $(".cuselText"), function(){$(this).css({'max-width' : ($(this).find('.cuselText').width()+42) , 'min-width' : ($(this).find('.cuselText').width()+42)});$(this).css({'max-width' : ($(this).find('.cuselText').width()+42) , 'min-width' : ($(this).find('.cuselText').width()+42)});$(this).css({'max-width' : ($(this).find('.cuselText').width()+42) , 'min-width' : ($(this).find('.cuselText').width()+42)});$(this).css({'max-width' : ($(this).find('.cuselText').width()+42) , 'min-width' : ($(this).find('.cuselText').width()+42)});$('.input-seach').css('padding-left', ($('.seach-bl .cusel').width()+16))});

$('.seach-bl input').focus(function() {
$('.seach-bl').addClass('focus');
});
$('.seach-bl input').focusout(function() {
$('.seach-bl').removeClass('focus');
});

//4// content min-height
function contminheight(){
	$('.m_content').css('min-height', ($(window).height()-$('.m_header').height()-$('.m_footer').height()-15));
	
	/*$('.persomal_room').css('height', ($(window).height()-$('.m_header').height()-$('.m_footer').height()-15));*/
	if($('html').hasClass('webkit')){$('.persomal_room').css('min-height', ($(window).height()-$('.m_header').height()-$('.m_footer').height()-15));}else{
		$('.persomal_room').css('height', ($(window).height()-$('.m_header').height()-$('.m_footer').height()-15));
	}
	
	/*$('.p_room-menu').css('min-height', ($(window).height()-$('.m_header').height()-$('.m_footer').height()-15));*/
}contminheight();

function contminheight2(){
	var height1 = $('.p_room-inner>header').height(),
		height2 = $('.myadd').height(),
		height3 = $('.p_room-menu').height();
	
	if((height1+height2)>height3){$('.p_room-menu').css('min-height', (height1+height2+1))}
}
contminheight2();

var isiPad = navigator.userAgent.match(/iPad/i) != null;
if(isiPad == true){/*$("body").removeClass('adaptive');*/}else{$('#viewport').attr('content', 'width=device-width, initial-scale=1.0');}
//5// //which of resolution
	function isBreakPoint(bp) {
		var bps = [0, 640, 962];
		var w = $(window).width();
		var min, max;
		for (var i = 0, l = bps.length; i < l; i++) {
			if (bps[i] === bp) {
				min = bps[i-1] || 0;
				max = bps[i];
			break;
			}
		}
		return w > min && w <= max;
	}	//end resolution
	function mmenu(){
		if((isiPad != true)){
			if (!(isBreakPoint(962))&&(!( isBreakPoint(640) ))){$('.main-page-style').addClass('iLight-disable');}else{$('.main-page-style').removeClass('iLight-disable');}
		}
		if(!( isBreakPoint(640) )){$('.comment-bl .visible').addClass('visible640')}else{$('.comment-bl .visible').removeClass('visible640')}
	}
	mmenu();
	
	
	
	/*!!!!!!!!!!!!!!*/
	 if (( $.browser.msie ) || ($.browser.mozilla) || ($.browser.opera)) {}else{
	var mql = window.matchMedia("(orientation: portrait)");
	var i=0;	
	
	
	window.addEventListener("orientationchange", function() {
		if(mql.matches) {  
			// Portrait orientation
			if (  $(window).width()==980     ){				
				$('.main-page-style').removeClass('iLight-disable');
				setTimeout(function(){$('.main-page-style').removeClass('iLight-disable')}, 1000);
				
			}	
		} else {  
			if (  $(window).width()==980    ){
				$('.main-page-style').addClass('iLight-disable');
			}	
		}
	}, false);



		if(mql.matches) {  
			if (  $(window).width()==980     ){
				$('.main-page-style').removeClass('iLight-disable');
				setTimeout(function(){$('.main-page-style').removeClass('iLight-disable')}, 1000);
			}
		} else {  
			if (  $(window).width()==980    ){
				$('.main-page-style').addClass('iLight-disable');
			}
		}

	}
	
//6//

function centerthis(){
	$('.act-center-this').each(function(){$(this).width($(this).width());$(this).css('float', 'none');$(this).css('margin-left', -$(this).innerWidth()/2)})
}
centerthis();
	
//7//

//	if($('.person-menu-bl').size()>0){
//	
//		var topFix = $('.person-menu-bl').offset().top;
//	
//	   $(window).on('scroll', function(){
//	    
//	    
//		var bottomG = $('.personal-card .cont').height(),
//			bottomFix = $('.personal-card .person-menu-bl').height();
//	    
//	    var  skillsTop = $('.person-menu-bl').offset().top;
//
//	    if ((topFix - $(window).scrollTop()) <= -90) {
//	     $('.person-menu-bl').addClass('fixed').removeClass('start-position')
//	    }
//	    else{
//	     $('.person-menu-bl').removeClass('fixed').addClass('start-position')
//	    }
//	  	if($(window).width()>1043){
//		  	if ((bottomG <= (bottomFix + $(window).scrollTop() -topFix))) {
//		     $('.person-menu-bl').fadeOut();
//		    }
//		    else{
//		     $('.person-menu-bl').fadeIn();
//		    }
//	   	}
//	   });
//
//	}
/*8*/	
	
$('.smallimg-bl>a').on('click', function(e){e.preventDefault();
	var link = $(this).attr('href'),
		linkTop = $(link).offset().top-12;
		
		$('html, body').animate({scrollTop : linkTop}, 1000);
})	

function getasidepos(){var width=$(window).width()-$('.personal-card-bl').width();$('.personal-card .person-menu-bl').css('right', width/2+220);}getasidepos();
/*charactiristic & contacts*/

/*$('.topinfo > .contact-bl > .show-cont-bl').click(function(){
	var tdwidth=$('.charactiristic tr td:first-child').width();	
	setTimeout(function() { 
		var td2width=$('.topinfo .contact-bl-info tr td:first-child').width();		
		if(tdwidth>td2width){
			$('.topinfo .contact-bl-info tr td:first-child').animate({width:tdwidth},2000);
		} else {
			$('.charactiristic tr td:first-child').animate({width:td2width},2000);
		}		
	}, 100)	
})
*/	
	
//$('.person-menu-bl .show-cont-bl').click(function(){
//	setTimeout(function(){if(
//		$('.personal-card .cont').height()<=($('.personal-card .person-menu-bl').height()+$(window).scrollTop()-topFix)  
//	){$('.person-menu-bl').fadeOut('slow');
//	}}, 500);
//	
//	setTimeout(function() { ibannerhide(); }, 1000)
//});

/*.comment-bl > ul > li .li-cont iPad show btn*/
$('.comment-bl > ul > li .li-cont').click(function(){
	$('.comment-bl .btn-white').removeClass('show');
	$(this).find('.btn-white').addClass('show');
})	
	
/*hide banner*/
function ibannershow(){
	$('.personal-card .i-banner').each(function(){
		if(($(this).offset().top+$(this).height())<($('.personal-card > .cont').offset().top+$('.personal-card > .cont').height())){$(this).removeClass('hideme')}
	});	
}ibannershow();
function ibannerhide(){
	$('.personal-card .i-banner').each(function(){
		if(($(this).offset().top+$(this).height())>($('.personal-card > .cont').offset().top+$('.personal-card > .cont').height()-16)){$(this).addClass('hideme')}
	});	
}ibannerhide();

	
/*.comment-bl .btn-white click*/
$('.comment-bl .btn-white').click(function(){$('.personal-card .article > .question-bl').hide('slow');
	if($(this).closest('.li-cont').next('.answer-bl').hasClass('active')) {return false} else{$('.answer-bl').removeClass('active').hide();$(this).closest('.li-cont').next('.answer-bl').addClass('active').show('slow');}
});
/*.comment-bl .btn-grey click*/
$('.answer-bl .btn-grey').click(function(){$(this).closest('.answer-bl').hide('slow');$('.personal-card .article > .question-bl').show('slow');$(this).closest('.answer-bl').removeClass('active')})
	
/*.toggle-comment-bl*/
$('.comment-bl .more').click(function(){
	if($(this).closest('.comment-bl').hasClass('open')){
		$(this).closest('.comment-bl').find('li').not('.visible640').not('.visible640 li').hide('slow');;$(this).closest('.comment-bl').removeClass('open')
	}else{
		$(this).closest('.comment-bl').find('li').not('.first').show('slow');$(this).closest('.comment-bl').addClass('open')
	}
	$(this).closest('.comment-bl').find('.more span').toggleClass('toggle');
})		
//tab function
 $.fn.tabs = function(control){
  var element = $(this);
  control = $(control);
  
  element.find('a').on('click', function(e){
   e.preventDefault();
   var num = $(this).attr('href');
   
   element.find('a').removeClass('active');
   $(this).addClass('active');
   
   control.find('article').hide();
   $(num).show(); 
  });
  
  control.find('article').hide().filter(':first').show();

  return true;
 }; //end of tab function
	
$('.tab-nav').tabs('.tab-section');
$('.tab-nav2').tabs('.tab-section2');
$('.mini-tabs-nav').tabs('.mini-tabs-cont');
$('.tab-nav a').on('click', function(){tablogic();tabheight();tabheight();})


function tabcarusel(){$("#foo").carouFredSel({
	width : "100%",	auto : false,
	prev    : { button  : "#foo_prev", key     : "left"   },
	next    : { button  : "#foo_next", key     : "right"  },
	scroll	: 1,
	visible : { min : 5,  max : 10 }
}).parent().css('margin', '0 auto');}

/*slyder.style1*/
function slyder(){$(".slyder.style1 .cont").carouFredSel({
	width : "100%",	auto : false,
	prev  : { button  : ".slyder.style1 .left-arr",  key     : "left"   },
	next  : { button  : ".slyder.style1 .right-arr", key     : "right"  },
	scroll		: 1,
	visible     : { min : 1, max : 10 }
}).parent().css('margin', '0 auto');	}

function slyder2(){$(".slyder.style2 .cont").carouFredSel({
	width : "100%",	auto : false,
	prev  : { button  : ".slyder.style2 .left-arr",  key     : "left"   },
	next  : { button  : ".slyder.style2 .right-arr", key     : "right"  },
	scroll		: 1,
	visible     : { min : 1, max : 10 }
}).parent().css('margin', '0 auto');	}

function slyder3(){$(".slyder.style3 .cont").carouFredSel({
	width : "100%",	auto : false,
	prev  : { button  : ".slyder.style3 .left-arr",  key     : "left"   },
	next  : { button  : ".slyder.style3 .right-arr", key     : "right"  },
	scroll		: 1,
	items       : {
	       
	        visible     : {
	            min         : 2,
	            max         : 10
	        }
	    }
	/*visible     : { min : 2, max : 10 }*/
}).parent().css('margin', '0 auto');	}

function slyder4(){$(".slyder.st4 .cont").carouFredSel({
	width : "100%",	auto : false,
	prev  : { button  : ".slyder.st4 .left-arr",  key     : "left"   },
	next  : { button  : ".slyder.st4 .right-arr", key     : "right"  },
	scroll		: 1,
	visible     : { min : 1, max : 10 }
}).parent().css('margin', '0 auto');	}

function mslider1(){$(".m-slider #li1 .scont").carouFredSel({
	width : "100%",	auto : false,
	prev    : { button  : "#li1 .left-arr", key     : "left"   },
	next    : { button  : "#li1 .right-arr", key     : "right"  },
	scroll	: 1,
	visible : 1
}).parent().css('margin', '0 auto');}

function mslider2(){$(".m-slider #li2 .scont").carouFredSel({
	width : "100%",	auto : false,
	prev    : { button  : "#li2 .left-arr", key     : "left"   },
	next    : { button  : "#li2 .right-arr", key     : "right"  },
	scroll	: 1,
	visible : 1
}).parent().css('margin', '0 auto');}




//m-slider center
function mscenter(){
	var wwidth=$('.m-slider-bl').width()-200;

	

	$('.m-slider .cont').css('margin-left', (wwidth-$('.m-slider .cont').width())/2)
}
//mscenter();

$('.tab-nav2 a').click(function(){if($('.m-slider #li2 .scont').size()>0){mslider2()}})

//tab-hide
$('.tab-navigation .hide').on('click', function(e){e.preventDefault();$('.m_tabs').hide('slow');$(this).closest('.tab-content').find('.main-b').hide('slow');$(this).closest('.tab-content').find('.info-act').hide('slow');$(this).closest('.tab-content').find('.tab-navigation a').toggleClass('visible')})
	
//tab-show
$('.tab-navigation .show').on('click', function(e){e.preventDefault();$('.m_tabs').show('slow');$(this).closest('.tab-content').find('.main-b').show('slow');$(this).closest('.tab-content').find('.info-act').show('slow');$(this).closest('.tab-content').find('.tab-navigation a').toggleClass('visible')})

//select choosen
$(".iselect").chosen();
//select choosen
$(".iselect-ns").chosen({disable_search_threshold: 10});
//input focus
$('.inp-cont').on('focus', '.inp', function(){$(this).closest('.inp').addClass('focus')})
$('.inp-cont').on('blur', '.inp', function(){$(this).closest('.inp').removeClass('focus')})
//textarea focus
$('.input').on('focus', 'textarea', function(){$(this).closest('.textarea').addClass('focus')})
$('.input').on('blur', 'textarea', function(){$(this).closest('.textarea').removeClass('focus')})
//ie7 li z-index fix
var zindex=1000;
$('.ie7_li_z-index_fix li').each(function(){$(this).css('z-index', zindex--)})
var zindex=90;
$('.multiselect').each(function(){$(this).css('z-index', zindex--)})
var zindex=1000;
$('.add-ad .fl100').each(function(){$(this).css('z-index', zindex--)})

//toggle_parametr
$('.toggle_parametr').on('click', function(e){
        e.preventDefault();
	if($(this).closest('.islide-menu').hasClass('float-box')){$(this).closest('.islide-menu').css('position', 'static')}
	
	$('.big_filter #new-search-params').toggle('fast');$(this).toggleClass('hide');
	if($(this).hasClass('hide')){$(this).find('span.span').text('Больше параметров')}else{$(this).find('span.span').text('Меньше параметров')}})

//change / clear filter
$('.active-result').on('click', function(){$(this).closest('.chzn-container').addClass('change')});
$('.downfilter').on('click', function(e){e.preventDefault();$('.downfilter-ul').find('.chzn-container').removeClass('change')})
$('.upfilter').on('click', function(e){e.preventDefault();$('.upfilter-ul').find('.chzn-container').removeClass('change')})

//banner-bl more
$('.banner-bl .more').on('click', function(e){e.preventDefault();$(this).closest('.banner-bl').find('a').show();$(this).hide()});

//btn-close-big click
$('.btn-close-big').on('click', function(e){e.preventDefault();$('.big_filter').toggleClass('inactive');$(this).toggleClass('active');$('.result-info').toggleClass('active');$("td.toggle").toggleClass('fullsize');
$('.big_filter').toggleClass('hide320');$('.result-info header').toggleClass('hide320');$('.result-info header .small-pagination').toggleClass('hide320');$('.result-info header .block').toggleClass('hide320');
$('.result-info .content .block').toggleClass('hide320');$('.result-info .content .block').toggleClass('big'); $('.adaptive .result-info footer').toggleClass('hide320');$('.toggle-hide640').toggleClass('hide640');$('.toggle-640-fix').toggleClass('nopading');
})
//.add-block .img click
$('.add-block .img').click(function(){	$('.add-block .img').removeClass('active');	$(this).addClass('active');})


//banner-aside last ibanner margin
$('.banner-aside .i-banner:last-child').css('margin-bottom', 5);

//.m_menu hover-effect not like amazon
//$('.m_menu .cont .left .top li a').hover(function(){$('.m_menu .cont .right .section').hide();$(this).closest('.m_menu').find('.'+$(this).attr("href")).show();
//$('.m_menu .cont .left li a').removeClass('active');$(this).addClass('active');})

//.m_menu hover-effect like amazon	
	var $menu = $(".m_menu .cont .left .top");
	if ($menu.size()>0){$menu.menuAim({activate: activateSubmenu, deactivate: deactivateSubmenu});}
	function activateSubmenu(row) {
        var $row = $(row), submenuId = $row.data("submenuId"), $submenu = $("#" + submenuId);
        $('.m_menu .cont .right .section').hide();
        $submenu.css("display", "block");
        $row.find("a").addClass("maintainHover");
    }
    function deactivateSubmenu(row) {
        var $row = $(row), submenuId = $row.data("submenuId"), $submenu = $("#" + submenuId);
        $submenu.css("display", "none");
        $row.find("a").removeClass("maintainHover");
    }
//
//$('.seach-bl input').val($(window).width())



//.adaptive-switch
$('.adaptive-switch').on('click', function(){$('body').toggleClass('adaptive');$('body').toggleClass('adaptiveoff');
/*if($('#foo').size()>0){tabcarusel()}
if($('.m-slider #li2 .scont').size()>0){mslider2()}
if($('.m-slider #li1 .scont').size()>0){mslider1()}
if($('.slyder.style1 .cont').size()>0){slyder()}
if($('.slyder.style2 .cont').size()>0){slyder2()}
if($('.slyder.style3 .cont').size()>0){slyder3()}
if($('.slyder.st4 .cont').size()>0){slyder4()}
*/
})



var mobile_timer = false;
if(!(navigator.userAgent.match(/iPhone/i)) /*|| navigator.userAgent.match(/iPad/i)*/) {}

//Edit. Отключаем исходное поведение видов 
//$('.result-info .btn-sort').on('click', function(e){e.preventDefault();$('.result-info .content').hide();$('.result-info .btn-sort').removeClass('active');$(this).addClass('active');
//if($(this).hasClass('var1')){$(".result-info .content.style1").show()}else if($(this).hasClass('var2')){$(".result-info .content.style2").show()}else($(".result-info .content.style3").show())
//})

//ivents click out of element !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 $(document).on('click', function(e) {	
	
 	//universal classes -> click out of element close you iLight element
 	if($(".iLight").hasClass('iLihgt-disable')){return false}else{
	 	if(($('.iLight').hasClass('active'))){ 		
	  	  if ($(e.target).closest('.iLight').is('.iLight') || $(e.target).is('.iLight') ||   $(e.target).is('.iLight-nav')  )   {}
		  else{ $('.iLight-cont').not('.iLight-disable .iLight-cont').fadeOut();$('.iLight').removeClass('active')}
	 	} 
 	}

	if ($(e.target).closest('.fn-search-popup').length == 0 && $('.fn-search-popup').css('display') == 'block' && $('.fn-search-popup').html() != '') 
	{ 
		$('.fn-search-popup').hide();
	}

	$('.fn-kac-more').hide();	
 });
//show/hide iLight pop-block
$('.iLight-nav').on('click', function(){
  		if ($(this).closest('.iLight').hasClass('iLight-disable')){return false}else{
  		if  (	$(this).closest('.iLight').hasClass('active')	)
		{	
			$(this).closest('.iLight').find('.iLight-cont').not('.iLight-disable .iLight-cont').hide('slow');$(this).closest('.iLight').removeClass('active');
		}
  		else
		{
  				$('.iLight').removeClass('active');	$('.iLight-cont').not('.iLight-disable .iLight-cont').fadeOut();
  				$(this).closest('.iLight').find('.iLight-cont').show('slow');
  				$(this).closest('.iLight').addClass('active');
  		}
	}	
});
//filter tab logic (show more/hide)

function tablogic(){
$('.m_tabs-bl .main-b .ul .li').each(function(){$(this).show()});
$('.m_tabs-bl .main-b .ul .li').each(function(){
	var top=$(this).offset().top+$(this).height();//console.log('top' + top);
	var height=$(this).closest('.main-b').height();//console.log(height);
	var top_block=$(this).closest('.main-b').offset().top;//console.log(top_block);
	if(top>(top_block+height)){$(this).hide()}else{$(this).show()}
})
}
tablogic(); 
  

//.tab-navigation .more
	function tabheight(){
		var add=$('.m_tabs-bl article:visible .main-b .li:visible').size();
		$('.m_tabs-bl article:visible .main-b').height($('.m_tabs-bl article:visible .main-b .ul').height());
		$('.tab-navigation .more').click(function(e){e.preventDefault();
			if($(this).hasClass('full')){
				$(this).removeClass('full');$(this).text('Показать больше');$(this).closest('.m_poll').find('.main-b').height(173);tablogic(); 
			}else{
				$(this).addClass('full');$(this).text('Показать меньше');
				var mainb=$(this).closest('.m_poll').find('.main-b').height();
				var li_vis=$(this).closest('.m_poll').find('.main-b .li:visible').size();
				var li_hid=$(this).closest('.m_poll').find('.main-b .li:hidden').size();
				
				if(li_hid>0){$(this).closest('.m_poll').find('.main-b .li:hidden').each(function(e){
					$(this).show();
				})}
				
				var mainb_ul=$(this).closest('.m_poll').find('.ul').height();
				if(mainb_ul>mainb){$(this).closest('.m_poll').find('.main-b').height(mainb_ul+25)}
			}
		})
	}
	//tabheight();
	

	
//.checkbox .seemore
$('.checkbox .seemore').on('click', function(e){e.preventDefault();$(this).closest('.checkbox').find('label').removeClass('dn');$(this).hide()})	

//smallcont toggle
$('.smallcont .toggle').on('click', function(e){e.preventDefault($(this).closest('.smallcont').find('.hide').toggle('slow'));$(this).toggleClass('a')
	if($(this).hasClass('a')){$(this).find('span').text('Больше параметров')}else{$(this).find('span').text('Меньше параметров')}
})
//inp-add-cont a click
$('.inp-add-cont .chzn-single').live('click', function(e){e.preventDefault();$(this).closest('.select').toggleClass('chzn-container-active')})
$('.inp-add-cont .label .select-cont li').live('click', function(e){
	$(this).closest('.select').find('.text').text($(this).text());
	$(this).closest('.select').removeClass('chzn-container-active');
	var thisis=this.id;
	var contact_type = $(this).parents('.inp-cont-bl').find('input[data-id=contact_type]');
	var contact = $(this).parents('.inp-cont-bl').find('input[data-id=contact]');

	contact_type.val($(this).data('id'));
	var format = $(this).data('format');
	if (format != ''){
		jQuery(function($) {
			$.mask.definitions['~']='[+-]';
			contact.unmask();
			contact.mask(format);
		}); 
	} else {
		contact.unmask();
	}
	contact.focus();
	$(this).closest('.select').find('.chzn-single>.ico').removeClass().addClass(thisis).addClass('ico');
	$(this).closest('.inp-cont-bl ').find('.findme').removeClass().addClass(thisis).addClass('ico').addClass('findme');
	
})
//!!!!!!!!!!!!!!!!!!! MAIN PAGE !!!!!!!!!!!!!!!!!!!
$('.main-page-cont .empty').height($('.m_menu.main-page-style .cont').height());
//$('.m_menu.main-page-style .cont').height($('.m_menu.main-page-style .cont').height());

/*.choose-your-region .col a*/
/* Edit. Отключаем */
//$('.choose-your-region .col a').on("click", function(e){e.preventDefault();	$(this).closest('.region').find('.thref').text($(this).text());$(this).closest('.choose-your-region').fadeOut();$(this).closest('.iLight').removeClass('active')})

//islide-menu
/*$(".islide-menu").find('li').not('li li').not('li.no-li-slide').addClass('first-level');
$(".islide-menu").find('.first-level ul').not('.first-level ul ul').addClass('second-level-ul');

$(".islide-menu .first-level>a:not(.clickable)").click(function(e){e.preventDefault();
	$(this).closest('.first-level').find('.second-level-ul').slideToggle();
	if($(this).closest('.first-level').hasClass('active')){
		//$(this).closest('.first-level').removeClass('active');	
	}else{
		//$(this).closest('.first-level').addClass('active');	
	}contminheight();float_menu();
	setTimeout(function(){
		contminheight();
	}, 100)
	
});*/
//$('.second-level-ul a:not(.clickable)').click(function(e){
//	e.preventDefault();
//});
//.msg-bl .more
$('.msg-bl .more').click(function(e){e.preventDefault();
	$(this).closest('.msg-bl').find('.msg-hide').slideDown('slow');$(this).hide('slow');/*height3();*/
})
function height3(){
	var height=$('p_room-menu').height();
	var hedeheight=0,
	elheight=0,
	newheight=0;
	$('.msg-hide').each(function(){
		hedeheight=hedeheight+$(this).height();
		
	})
}

$('.myadd > .cont .col2 .title:not(.clickable), .myadd .toggle').click(function(e){
	e.preventDefault();
	$(this).closest('.li').find('.hide-cont').slideToggle('slow');
	$(this).closest('.li').find('.show-cont').slideToggle('slow');
	$(this).closest('.li').toggleClass('active');
	return false;
});

$('.cabinet .myadd > .cont .panel-toggle span').click(function(e){
	e.preventDefault();
	$(this).closest('.li').find('.hide-cont').slideToggle('slow');
	$(this).closest('.li').find('.show-cont').slideToggle('slow');
	$(this).closest('.li').toggleClass('active');
	if ($(this).text() == 'Показать комментарии') $(this).text('Скрыть комментарии')
	else $(this).text('Показать комментарии');
	return false;
});

/**/
$('.iinput-bl').each(function(){
	var iwidth=0;
	$(this).find('.myinfo').each(function(){
		//console.log($(this).width())
		if($(this).width()>iwidth){iwidth=$(this).width();}
		
	})
	$(this).find('.myinfo').width(iwidth);
})

$('.popup-layer').height($(window).height());

$('.popup .close').click(function(){
	$(this).closest('.popup').fadeOut();
	$(this).closest('.popup').find('.fn-popup-error').hide();
	$(this).closest('.popup').find('.fn-login').toggleClass('error');
	$('.popup-layer').fadeOut();
});

$('.fn-close').click(function(){
	$(this).closest('.fn-window').fadeOut();
});

$('.popup .cancel').click(function(){
	$(this).closest('.popup').fadeOut();
	$('.popup-layer').fadeOut();
});
$('.popup-layer').click(function(){
	$('.popup').fadeOut();
	$('.popup-layer').fadeOut();
});

/**/

$('.informator .toggle').click(function(e){e.preventDefault();$(this).closest('.informator').find('.cont').slideToggle();$(this).closest('.informator').find('.title>span').slideToggle();})

$('.filials-bl .visible-bl .btn-reduct').click(function(e){e.preventDefault();
	$('.filials-bl .visible-bl').show();$('.filials-bl .reduct-bl').hide();
	$(this).closest('.visible-bl').hide();$(this).closest('.article').find('.reduct-bl').show();
	$(this).closest('.article').find('.info-bl').delay('2000').height($(this).closest('.article').height()-12)
})
$('.show-map').click(function(e){e.preventDefault();
	$(this).find('span').toggleClass('show');
	$(this).closest('.article').find('.map').slideToggle();
})

function float_menu(){
	
		
	$(window).on('scroll', function(){
	
		if(height_cont>0 && height_cont>height_box){
			var height_cont = $('.float-content').height(),
			height_box = $('.float-box').height();
			var box_top = $('.float-box').offset().top,
			content_top = $('.float-content').offset().top,
			window_scroll = $(window).scrollTop(),
			box = $('.float-box'),
			window_height = $(window).height(),
			fixheight;
			if(window_scroll>content_top){box.css('position', 'absolute')}else{box.css({'position':'static', 'top':'0'})}
			if(window_height>height_box){fixheight=window_height;console.log('smallbox')}else{fixheight=height_box;console.log('bigbox')}
			if((window_scroll+window_height)>(content_top+height_box)){box.css('top', (window_scroll+window_height)-(content_top+fixheight))}
			
			if((window_scroll+window_height)>(content_top+height_cont)){box.css('top', (height_cont)-(fixheight))}

		}
		
	});
}float_menu();

//$('#send_password').click(function(e){
//	e.preventDefault();
//
//	var contact = $(this).closest('#auth_window').find('input[name=login]').val();
//	var obj = this;
//
//	$('#send_password_msg').text('Отправка...');
//	$.getJSON('/ajax/send_password', {contact:contact}, function(json){
//		if (json.code == 200) {
//			if (json.contact_type_id == 1) {
//				$('#send_password_msg').text('SMS отправлено');
//			} else if (json.contact_type_id == 5) {
//				$('#send_password_msg').text('Пароль отправлен');
//			} else {
//				// неизвестный тип контакта
//			}
//		} else if (json.code == 404) {
//			$(obj).show();
//			$('#send_password_msg').text('Email или номер телефона не найден');
//		} else if (json.code == 303 || json.code == 304) {
//			$('#send_password_msg').text(json.msg);
//		}
//		$('.fn-popup-error').show();
//		$('.fn-login').toggleClass('error');
//	});
//
//	return false;
//});


//Объект окна авторизации
if (typeof AuthWindow == 'function') {
	var a = new AuthWindow();
}

if ($(".innerPage-leftAside #navigation").length) {
	$(".innerPage-leftAside #navigation").treeview({persist: "location", collapsed: true, unique: true});
}


//Callback функция заполнения шаблона KAC
var timer = null;
function cb_kac_load(e){
	
	if (e.type == 'mouseenter') {		 	 
		timer = setTimeout(function(){
			var kac_elem = $(e.delegateTarget);
			var id = kac_elem.data('num');
			
			$.post('/ajax/get_unit',{id:id},function(json){
				if (json.code == 200){
					$('.fn-kac-title').html(json.data.title);
					$('.fn-kac-descr').html(json.data.description);
					$('.fn-kac-more-link').attr('href', '/' + json.data.userpage);
					
					if (json.data.kac_vacancies_link != null)
						$('.fn-kac-vac-link').attr('href', json.data.kac_vacancies_link).show();
					else 
						$('.fn-kac-vac-link').attr('href', '').hide();
					
					$('.fn-address').html(json.data.address);
					
					if (json.data.web != null)
					{
						$('.fn-web').attr('href', json.data.web).html(json.data.web).show();
						$('.fn-web-line').show();
					}
					else 
					{
						$('.fn-web').attr('href', '').html('').hide();	
						$('.fn-web-line').hide();
					}
					
					$('.fn-contacts').html(json.data.contacts);
					
					$('.fn-kac-objects-link').attr('href', json.data.kac_objects_link);
					
					$('.fn-kac-top .fn-kac-left').remove();					
					if (json.data.file != null)						
						$('.fn-kac-top .fn-kac-right').before('<div class="left fn-kac-left"><img src="'+ json.data.file +'"></div>');
				
					$('.fn-kac-more').css('top', kac_elem.position().top).show();
				}
			}, 'json');

			
		}, 300);
	 } else {
		//$('.fn-kac-more').hide();
		clearTimeout(timer);
	 }
}

function kac_position_click(){
		window.location = "/users/" + $(this).data('ul');
}

$('.fn-kac-position').on('mouseenter mouseleave', cb_kac_load);
$('.fn-kac-position').on('click', kac_position_click);
//$('.fn-kac-widget').on('mouseenter mouseleave', '.fn-kac-position', cb_kac_load);
//$(document.body).on('mouseenter mouseleave', '.fn-kac-position', cb_kac_load);

$('.fn-kac-close').click(function(){
	$('.kac-more').hide();
});

$('.fn-kac-show-more').click(function(e){

	var element = $(this).closest('.fn-kac-show-more');
	var boxelement = $(this).closest('.fn-kac-widget-cont');
	
	var category_id = element.data('catid');
	var limit		= element.data('limit');
	var offset      = element.data('offset');
	var business_type_id =  element.data('business-type');
	var city_id = boxelement.data('cityid');

	var element_for_del = $('.fn-kac-widget .fn-kac-show-more'+business_type_id);
	
	$.post('/ajax/get_units',{category_id: category_id, business_type_id: business_type_id, limit:limit, offset:offset, city_id : city_id},function(html){	
		if ($.trim(html) == '')	
				$(element_for_del).hide();
		else {
			$('.kac-widget .kac-widget-cont .fn-units-container'+business_type_id).append(html);
												
			$('.fn-kac-position').unbind('mouseenter mouseleave', cb_kac_load).on('mouseenter mouseleave', cb_kac_load);					
			$('.fn-kac-position').unbind('click', kac_position_click).on('click', kac_position_click);		
		}		
		$(element).data('offset', offset + limit);							
	})
});


})//end ready doc



//Мерцание элемента
//elem - элемент, к которому нужно применить эффект
//params - параметры(количество интервалов, временной интервал, css-класс)
function blink(elem, params)
{
	var i = 1;

	var timer = setInterval(function() {	  	  
		
		if ((i % 2 == 0))
			elem.addClass(params.blink_class);
			//elem.css('opacity', '0.5');
		else
			elem.removeClass(params.blink_class);  
			//elem.css('opacity', '1');
		
	  i++;
	  
	if (i > params.interval_count) 
	{
		clearInterval(timer);	  
		elem.removeClass(params.blink_class);  
		//elem.css('opacity', '1');
	}
  }, params.time_interval);	    
}

function mbanner_height(){
		if($('.mbanner').size()>0){
			var bheight = $('.mbanner .info-block > img').height(),
			lheight = $('.mbanner .my_logo').height()+40;	
			if(bheight>lheight){$('.mbanner .info-block').height(bheight);}else{$('.mbanner .info-block').height(lheight);}
		}
}

/* Дополнительные правки(по функционалу сайта) */

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
/* Установка placeholder для input
 * obj - jquery объект поля
 * text - текст placeholder
 */

function set_placeholder(obj)
{			
	var label = $('#'+obj.attr('id')+'_label');	
	if (obj.val() != '') label.hide();
	
	obj.focus(function(){		
		label.hide();
	});	
	
	obj.blur(function(){		
		if (obj.val() == '') label.show();
	});		
}

function close_hint(object){
	
}

//show hints on page who not disabled in cookie
function get_hints_by_page(contr){
	var hints_cookie = $.cookie('yarmarka_hints');
	var hints_obj = JSON.parse(hints_cookie);
	var hexists  = (hints_obj) ? true:false; 
	
	$.post('/ajax/ajax_get_hints_by_page',{controller: contr}, function(data){
		if (data){
			for (i=0;i<data.length;i++)
			{
				//data[i].identify ИД
				//data[i].html
				if (hexists){		
					var exist_hint_in_cookie = (hints_obj.array.indexOf(data[i].identify)>=0) ? true:false;
				} else {
					var exist_hint_in_cookie = false;
				}
				if (($('.'+data[i].identify).length>0) && (!exist_hint_in_cookie) && ($('.'+data[i].identify).is(":visible"))){
					
					var offset = $('.'+data[i].identify).offset();

					$('body').append('<div class="yarm_hints" id="hint'+i+'" style="position:absolute;z-index:9999;" >'+data[i].html+'</div>');
					$('#hint'+i).css('left',(offset.left+data[i].left*1)+'px').css('top',(offset.top+data[i].tops*1)+'px').css('width',(data[i].width*1)+'px');
				}
			}
		}
	},'json');

}

//disable hint
function add_hint_cookie(object,identify){
		var hints_cookie = $.cookie('yarmarka_hints');
		
		var hints_obj = JSON.parse(hints_cookie);
		if (hints_obj) { 
			if (hints_obj.array.indexOf(identify)<0){
				hints_obj.array.push(identify);
			}
		} else {
			hints_obj = {array:[identify]};
		}

		$.cookie('yarmarka_hints', JSON.stringify(hints_obj), {expires: 182, domain:'yarmarka.biz', path: '/'}); //TODO: Настройка фиксом(домен)

		
		object.closest("div.yarm_hints").remove();
}

function close_x(obj)
{	
	$(obj).closest('.yarm_hints').hide('slow');
}

/*
function get_userpage_widgets(){

	$.post('/ajax/user_page_widgets_load', {}, function(data){
			for (i=0;i<data.length;i++)
			{
				$('body').append('<div class="yarm_widgets handle" id="widget'+i+'" style="position:absolute;z-index:9999;" >'+data[i].html+'</div>');
				$('#widget'+i).css('left',(data[i].left*1)+'px').css('top',(data[i].top*1)+'px').show();
				$('#widget'+i).bind('drag',function( event ){
									$( this ).css({
											top: event.offsetY,
											left: event.offsetX
											});
							});
				
			}
			
		}, 'json');
}*/

$(document).ready(function() {
	
	$('.fn-blink').click(function() {
		var elem = $('.'+$(this).data('src'));
		var params = {interval_count: 5, time_interval: 200, blink_class: 'blink'}
		blink(elem, params);
	});	
	
	$('.add-advert').click(function(){
		window.location.href = '/add';
	}); 
	
	$('.add-advert-f').click(function(){
		
		var id	  = parseInt($(this).data('id'));
		var count = parseInt($(this).data('count'));
		
		if (count == 0 && id != 0)
			window.location.href = '/add/' + id;
		else
			window.location.href = '/add';
	}); 
	
	$('.mbanner .navtoggle').click(function(e){e.preventDefault();$('.mbanner a.nav').toggleClass('toggle');
		$('.m_header').toggle();
		$('.input-seach').css('padding-left', ($('.seach-bl .cusel').width()+16));	
	});	


	$('.user_link_approve, .user_link_decline').click(function(){
		var obj = this;
		$.getJSON($(obj).data('href'), function(json){
			if (json.code == 200) {
				$(obj).parents('span').remove();
			}
		});
	});	
	
	if ($('body.page-search').length>0){
		get_hints_by_page('search');
	} else
	if ($('body.page-index').length>0){
		get_hints_by_page('index');
	} else
	if ($('body.page-add').length>0){
		get_hints_by_page('add');
	}
	//get_userpage_widgets();
	
	//disable message for user
	$('.fn-disable-user-message').click(function(){
		var value = (disable_message_key === undefined) ? 1 : disable_message_key;
		$.cookie('disable_message', value, {expires: 30, domain:'yarmarka.biz', path: '/'}); //TODO: Настройка фиксом(домен)		
		$('.fn-user-message-block').hide('slow');
	});	
	
	$('.fn-clean-cookie-s1').click(function(){		
		window.location.href = '/article/help';
	})		
	
	$('.fn-clean-cookie-s2').click(function(){
		$.removeCookie('user_id', { path: '/', value: '', domain: 'yarmarka.biz', expires: -1});
		$.removeCookie('yarmarka', { path: '/', value: '', domain: 'yarmarka.biz', expires: -1});
		$.removeCookie('session', { path: '/'});		
		
		window.location.href = 'http://yarmarka.biz';//TODO: Фиксированный домен
	})	
		
}); //$(document).ready(function()

$(window).load(function(){
    $('.slide-content-nav').click(function(e){e.preventDefault();
        $(this).closest('.slide-content-box').find('.slide-content-cont').slideToggle();
        $(this).toggleClass('act');
    });
});

function showDetails(obj, id, prefix)
{		
	id = isNaN(parseInt(id)) ? 0 : id;
	prefix = (prefix == 'p') ? prefix : 'd';
	
	if ($('#fn-more-tr'+id+prefix+' td .detail-content').html() == '')	
			$('#fn-more-tr'+id+prefix+' td .detail-content').load('/ajax/get_obj_detail', 
														{id: id},
														function(){ $('#fn-more-tr'+id+prefix+' td .loaded').html('') });		

	$('#fn-more-tr'+id+prefix).toggle();
	
	if ($('#fn-more-tr'+id+prefix).css('display') != 'none') $(obj).text('Скрыть ...')
	else $(obj).text('Подробнее ...');		
}

/* Функция отрисовки графика статистики объявления
	obj_src - элемент-инициатор события
	obj_id - id объявления, для которого нужна статистика
	canvas_cont - класс элемента-контейнера для отрисовки графика. Внутри обязательно должен находиться блок fn-inner

	Есть зависимость от библиотеки jquery.flot
*/
function renderObjectStat(obj_src, obj_id, canvas_cont)
{
	var obj_id = isNaN(parseInt(obj_id)) ? 0 : obj_id;

	if (!obj_id) return;

	var canvas_cont = "." + canvas_cont;		
	var canvas = $(canvas_cont).find('.fn-inner');

	var coords = $(obj_src).offset();		

	$.post("/ajax/ajax_get_obj_stat", { obj_id: obj_id}, function(data) {

		var visits = [];
		var contacts_show_count = [];

		for (i=0; i <= data.length - 1; i++)
		{
			visits.push([data[i].date, data[i].visits]);
			contacts_show_count.push([data[i].date, data[i].contacts_show_count]);
		}

		$(canvas_cont).show().offset({top: coords.top, left: coords.left - 347});	
		$.plot(canvas, [ visits, contacts_show_count ], { xaxis: { mode: "time" } });		
	}, 'json');		
}

function send_kp_promo(event_src, obj_id)
{
	$.post("/ajax/send_kp_promo", { obj_id: obj_id}, function(data) {
		if (data.code == 200)
			$(event_src).text('Отправлено');
		else
			$(event_src).text('Не отправлено');
		
		$(event_src).removeClass('span-link').attr('onclick', '');
		
	}, 'json');			
}

function aclick(event_src, id)
{
	$.post("/ajax/aclick", { id: id});	
	//$(event_src).attr('onclick', '');
}

//Создание эффекта fixed для элемента при скроллировании страницы
function scroll_fixed(elem)
{
	if (elem.length == 0) return;
	
	var topFix = elem.offset().top;		

    $(window).on('scroll', function(){	   
		if ((topFix - $(window).scrollTop()) <= 0) {			
			elem.addClass('fixed')
		}
		else {
			elem.removeClass('fixed')
		}
   });
}
