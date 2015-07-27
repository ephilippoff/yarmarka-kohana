<!DOCTYPE html>
<html lang=ru>
<head>
<meta charset=utf-8>
<link rel="shortcut icon" href="<?=URL::base('http')?>images/favicon.ico" type="image/x-icon">
<!--<meta name="viewport" id="viewport" content="width=device-width,minimum-  scale=1.0,maximum-scale=10.0,initial-scale=1.0" />-->
<!--<meta content="width=device-width, initial-scale=1.0" name="viewport">-->
<meta name="viewport" id="viewport" content="width=device-width">
<title><?=Seo::get_title()?></title>
<?=Assets::factory('main')->css('old/cssadm.css', array('media' => 'screen, projection'))
	->css('old/iLight.css')
	->css('old/jquery.jscrollpane.css')
	->css('old/cusel.css')
	->css('old/chosen.css')
	->js('old/jquery.min.js')
	->js('http://html5shiv.googlecode.com/svn/trunk/html5.js', array('condition' => 'lte IE 8'))
	->js('http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js', array('condition' => 'lte IE 8'))
	->js('old/PIE.js', array('condition' => 'lte IE 10'))
	->js('old/jscrollpane.js')
	->js('old/underscore.js')
	->js('old/jquery.mousewheel.js')
	->js('old/cusel-min-2.5.js')
	->js('old/jquery.carouFredSel-6.2.0.js')
	->js('old/chosen.jquery.js')
	->js('old/jquery.menu-aim.js')
	->js('old/libs.js')		
	->js('old/jquery.treeview.js')		
	->js('old/js.js')
	->js('old/jquery.openxtag.min.js')
	->js('old/cartCounter.js')
	.$_assets
?>
</head>
<body class="adaptiveoff">
	<div class="popup-layer"></div>
	<?=View::factory('layouts/google_analytics')?>
	<div class="wrapfix">
		<?=View::factory('layouts/header')?>
		<div class="m_content">
			<?=$_content?>
		</div>
	</div>
	<div class="wrapfix footer-wrap">
		<?=View::factory('layouts/footer')?>
	</div>
</body>  
</html>
