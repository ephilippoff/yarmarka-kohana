<!DOCTYPE html>
<html lang=ru>
<head>
<meta charset=utf-8>
<link rel="shortcut icon" href="<?=URL::base('http')?>images/favicon.ico" type="image/x-icon">
<!--<meta name="viewport" id="viewport" content="width=device-width,minimum-  scale=1.0,maximum-scale=10.0,initial-scale=1.0" />-->
<!--<meta content="width=device-width, initial-scale=1.0" name="viewport">-->
<meta name="viewport" id="viewport" content="width=device-width">
<title><?=Seo::get_title()?></title>
<?=Assets::factory('main')->css('cssadm.css', array('media' => 'screen, projection'))
	->css('iLight.css')
	->css('jquery.jscrollpane.css')
	->css('cusel.css')
	->css('chosen.css')
	->js('jquery.min.js')
	->js('http://html5shiv.googlecode.com/svn/trunk/html5.js', array('condition' => 'lte IE 8'))
	->js('http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js', array('condition' => 'lte IE 8'))
	->js('PIE.js', array('condition' => 'lte IE 10'))
	->js('jscrollpane.js')
	->js('underscore.js')
	->js('jquery.mousewheel.js')
	->js('cusel-min-2.5.js')
	->js('jquery.carouFredSel-6.2.0.js')
	->js('chosen.jquery.js')
	->js('jquery.menu-aim.js')
	->js('libs.js')		
	->js('jquery.treeview.js')		
	->js('js.js')
	->js('jquery.openxtag.min.js')
	.$_assets
?>
</head>
<body class="adaptiveoff page-myads">
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
	
	<div style="display: none;" class="fn-stat-container fn-window stat-container"><span class="fn-close close"></span><div class="fn-inner inner"></div></div>
	
	<?php if (in_array(Auth::instance()->get_user()->role, array(1, 9))) : ?>
		<script type="text/javascript">
			$('.fn-stat').click(function() {
				renderObjectStat(this, $(this).data('id'), 'fn-stat-container');
			});	
		</script>
	<?php endif; ?>	
	
</body>  
</html>
