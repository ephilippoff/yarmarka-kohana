<!DOCTYPE html>
<html lang=ru>
<head>
<meta charset=utf-8>
<link rel="shortcut icon" href="<?=URL::base('http')?>images/favicon.ico" type="image/x-icon">
<!--<meta name="viewport" id="viewport" content="width=device-width,minimum-  scale=1.0,maximum-scale=10.0,initial-scale=1.0" />-->
<!--<meta content="width=device-width, initial-scale=1.0" name="viewport">-->
<meta name="viewport" id="viewport" content="width=device-width">
<meta name="description" content="<?=Seo::get_description()?>">
<?php if (isset($is_news_page)) : ?>
	<meta name="robots" content="noindex" />
<?php endif;?>	
<title><?=htmlspecialchars($data->object['title'])?></title>
<?=Assets::factory('main')->css('css.css', array('media' => 'screen, projection'))
	->css('iLight.css')
	->css('jquery.jscrollpane.css')
	->css('cusel.css')
	->css('chosen.css')
	->js('jquery.min.js')
	->js('http://html5shiv.googlecode.com/svn/trunk/html5.js', array('condition' => 'lte IE 8'))
	->js('http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js', array('condition' => 'lte IE 8'))
	->js('PIE.js', array('condition' => 'lte IE 10'))
	->js('jscrollpane.js')
	->js('jquery.mousewheel.js')
	->js('cusel-min-2.5.js')
	->js('jquery.carouFredSel-6.2.0.js')
	->js('chosen.jquery.js')
	->js('jquery.menu-aim.js')
	->js('libs.js')		
	->js('jquery.treeview.js')		
	->js('js.js')
	->js('jquery.openxtag.min.js')
	->js('minified/underscore-min.js')		
	.$_assets
?>
</head>
<body class="adaptiveoff">
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