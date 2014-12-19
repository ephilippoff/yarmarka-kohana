<!DOCTYPE html>
<html lang=ru>
<head>
<meta charset=utf-8>
<link rel="shortcut icon" href="<?=URL::base('http')?>images/favicon.ico" type="image/x-icon">
<!--<meta name="viewport" id="viewport" content="width=device-width,minimum-  scale=1.0,maximum-scale=10.0,initial-scale=1.0" />-->
<!--<meta content="width=device-width, initial-scale=1.0" name="viewport">-->
<meta name="viewport" id="viewport" content="width=device-width">
<title>Регистрация/авторизация</title>
<?=Assets::factory('main')->css('css.css', array('media' => 'screen, projection'))
	->css('iLight.css')
	->js('jquery.min.js')
	->js('http://html5shiv.googlecode.com/svn/trunk/html5.js', array('condition' => 'lte IE 8'))
	->js('http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js', array('condition' => 'lte IE 8'))
	->js('PIE.js', array('condition' => 'lte IE 10'))
	->js('header.js')
	->js('minified/underscore-min.js')
	->js('minified/backbone-min.js')
//	->js('http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU')
	->js('ajaxupload.js')
	->js('jquery.inputmask.js')
	.$_assets
?>
</head>
<body class="adaptive page-addobj page-auth">
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
</body>  
</html>
