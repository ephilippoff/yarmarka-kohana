
jQuery(document).ready(function($) {

var current, size;  
 $('.lightbox35').click(function(e) {  

   
    e.preventDefault();								// prevent default click event
    var image_href = $(this).attr("href"); 			// grab href from clicked element    
    var slideNum = $('.lightbox35').index(this);	    // determine the index of clicked trigger

    // find out if #lightbox exists
    if ($('#slideshow').length > 0) {        
      // #lightbox exists
      $('#slideshow').fadeIn(300);
      // #lightbox does not exist - create and insert (runs 1st time only)
    } else {                                

// create HTML markup for lightbox window
      var lightbox =
          '<div id="slideshow" >' +
		  '<p title="close" id="lightbox35Close">X</p>' +
          '<div class="nav">' +
          '<a class="prev slide-nav" title="Preview" id="lightbox35Prev"><</a>' +
          '<a class="next slide-nav" title="Next;" id="lightbox35Next">></a>' +
          '</div>' +
          '</div>';
      
      //insert lightbox HTML into page
      $('body').append(lightbox);
      
      // fill lightbox with .lightbox35 hrefs in #imageSet
      $('#imageSet').find('.lightbox35').each(function() {
        var $href = $(this).attr('href');
        $('#slideshow').append(
          '<img src="' + $href + '">'
        );
      });
      
    }
    
    // setting size based on number of objects in slideshow
    size = $('#slideshow img').length;
    
    // hide all slide, then show the selected slide
    $('#slideshow img').hide();
    $('#slideshow img:eq(' + slideNum + ')').show();
    
    // set current to selected slide
    current = slideNum;
  });
  


//enter_13  //esc_27 //shift_16 //ctrl_17 //alt;  
$(document).keyup(function(e) {
  if ( e.keyCode == 27 || e.keyCode == 16 || e.keyCode == 17 ||   e.keyCode == 18 || e.keyCode == 46)   
  {$('#slideshow').remove(); }   
});


  
//Click anywhere on the page to get rid of lightbox window
// using .on() instead of .live(). more modern, and fixes event bubbling issues

$('body').on('click', '#slideshow', function() { 
 dest = current + 1;
    if (dest > size - 1) { dest = 0; }
     // fadeOut curent slide, FadeIn next/prev slide
    $('#slideshow img:eq(' + current + ')').fadeOut(200);
    $('#slideshow img:eq(' + dest + ')').fadeIn(200);
    
    // update current slide
    current = dest;
});


$('body').on('click', '#lightbox35Close', function() { 
$('#slideshow').remove();});
   
 	
 
  // show/hide navigation when hovering over #slideshow
$('body').on(
	{ 	mouseenter: function() {    $('.nav').fadeIn(300);    }, 
		mouseleave: function() {    $('.nav').fadeOut(300);   }
	},'#slideshow');
  
  // navigation prev/next
  $('body').on('click', '.slide-nav', function(e) {    
    // prevent default click event, and prevent event bubbling to prevent lightbox from closing
    e.preventDefault();
    e.stopPropagation();
    
    var $this = $(this);
    var dest;
    

    if ($this.hasClass('prev')) {
      dest = current - 1;
      if (dest < 0) {  dest = size - 1; }
	  
    } else {

    dest = current + 1;
    if (dest > size - 1) {
        dest = 0;
      }
    }
    
    // fadeOut curent slide, FadeIn next/prev slide
    $('#slideshow img:eq(' + current + ')').fadeOut(200);
    $('#slideshow img:eq(' + dest + ')').fadeIn(200);
    
    // update current slide
    current = dest;
  });


function changeImg(current, dest){
	// fadeOut curent slide, FadeIn next/prev slide
    $('#slideshow img:eq(' + current + ')').fadeOut();
    $('#slideshow img:eq(' + dest + ')').fadeIn();
}

  
  $(document.documentElement).keyup(function (event) {  
    var $this = $(this);
    var dest;
    
  if (event.keyCode == 37 ) {
          dest = current - 1;
      if (dest < 0) {
        dest = size - 1;
      }
	  
changeImg(current, dest);
 // update current slide
 current = dest;	
   } 
  
  
  
  else if (event.keyCode == 39) {
      dest = current + 1;
      if (dest > size - 1) {
        dest = 0;
      }
	  
changeImg(current, dest);
 // update current slide
 current = dest;	  
  }
  
  });


  
});



	function fsheh(){
	$('#slideshow').remove();
	}
	
	$('#lightbox35Close').click(function() {
	alert(12);
	$('#slideshow').remove();
	});
	
	
function appendStyle(styles) {
  var css = document.createElement('style');  css.type = 'text/css';
  if (css.styleSheet) css.styleSheet.cssText = styles;
  else css.appendChild(document.createTextNode(styles));  
  var reshi= document.getElementsByTagName("head")[0];if(!reshi){appendHEAD();alert(reshi+"1");}
  document.getElementsByTagName("head")[0].appendChild(css);
}


function appendHEAD(){ var hedA= document.createElement('head'); document.appendChild(hedA);}
var styles = '#slideshow { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, .8);z-index:5;}'+
'#slideshow  p { font-size:20px; position:fixed; z-index:999; cursor:pointer; padding:5px 10px; border:1px solid #999; right:5px; top:5px; text-align:  right; color: #fff; background: rgba(100, 100, 100, .5); border-radius:3px;}'+
'#slideshow img { position: absolute; top: 5%; left: 0px; right: 0px; bottom:5%; align:center; max-width:90%; max-height:90%; margin:auto;}'+
'.prev, .next {  z-index:999; background: rgba(100, 100, 100, .5); cursor:pointer; position: absolute; top: 45%; border:1px solid #999; padding: 6px ; color: #fff; text-decoration: none; position: absolute;font-size:30px;text-shadow:1px 1px 0px #444;}'+
'.next { right: 7px; border-radius:2px 10px 10px 2px; } .prev { left: 7px;  border-radius:10px 2px 2px 10px; }'+
'.thumb{width: auto;height: 100px;margin:5px;}';

window.onload = function() { appendStyle(styles) };
