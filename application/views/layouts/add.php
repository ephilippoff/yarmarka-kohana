<!DOCTYPE html>
<html lang=ru>
<head>
<meta charset=utf-8>
<link rel="shortcut icon" href="<?=URL::base('http')?>images/favicon.ico" type="image/x-icon">
<!--<meta name="viewport" id="viewport" content="width=device-width,minimum-  scale=1.0,maximum-scale=10.0,initial-scale=1.0" />-->
<!--<meta content="width=device-width, initial-scale=1.0" name="viewport">-->
<meta name="viewport" id="viewport" content="width=device-width">
<title><?=Seo::get_title()?></title>
<?=Assets::factory('main')
	->css('bootstrap.min.css', array('media' => 'screen, projection'))
	->css('iLight.css')		
	->css('css.css', array('media' => 'screen, projection'))
	->css('add.css', array('media' => 'screen, projection'))
	->js('old/jquery.min.js')
//	->js('http://html5shiv.googlecode.com/svn/trunk/html5.js', array('condition' => 'lte IE 8'))
//	->js('http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js', array('condition' => 'lte IE 8'))
//	->js('old/PIE.js', array('condition' => 'lte IE 10'))
	->js('old/header.js')
	->js('old/minified/underscore-min.js')
	->js('old/minified/backbone-min.js')
	->js('http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU')
	->js('old/ajaxupload.js')
//	->js('old/jquery.inputmask.js')
	->js('old/jquery.maskedinput.min.js')	
	->js('old/jquery-ui.min.js')	
	.$_assets;
	
	echo Assets::factory('main')->js($jspath);
?>
</head>
<body class="adaptive page-add-obj">
	<div class="popup-layer"></div>
	<?=View::factory('layouts/google_analytics')?>
	<div class="wrapfix add">
			<?=View::factory('layouts/header1')?>
			<div class="m_content">
			<?=$_content?>
	        </div>
	  </div>
      <div class="wrapfix footer-wrap add">
	  <?=View::factory('layouts/footer')?>
      </div>
	
<?php	
	$token = Userecho::get_sso_token(Auth::instance()->get_user());
?>
<!--noindex-->
<script type='text/javascript'>

var _ues = {
host:'feedback.yarmarka.biz',
forum:'18983',
lang:'ru',
tab_corner_radius:5,
tab_font_size:20,
tab_image_hash:'0JLQvtC%2F0YDQvtGB0YssINC40LTQtdC4LCDRgtC10YXQv9C%2B0LTQtNC10YDQttC60LA%3D',
tab_chat_hash:'0J7QvdC70LDQudC9INC%2F0L7QvNC%2B0YnRjA%3D%3D',
tab_alignment:'right',
tab_text_color:'#FFFFFF',
tab_text_shadow_color:'#00000055',
tab_bg_color:'#57A957',
tab_hover_color:'#F45C5C'
<?php if ($token) : ?>, params:{sso_token:'<?=$token?>'}<?php endif; ?>
};

(function() {
    var _ue = document.createElement('script'); _ue.type = 'text/javascript'; _ue.async = true;
    _ue.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.userecho.com/js/widget-1.4.gz.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(_ue, s);
  })();

</script>
<!--/noindex-->	
	
</body>  
</html>
