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
	->js('jScrollPane.js')
	->js('jquery.mousewheel.js')
	->js('cusel-min-2.5.js')
	->js('jquery.carouFredSel-6.2.0.js')
	->js('chosen.jquery.js')
	->js('jquery.menu-aim.js')
	->js('js.js')
	->js('jquery.openxtag.min.js')
	.$_assets;
	?>
<!--[if lte IE 8]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>				
	<![endif]-->
</head>
<body class="adaptiveoff page-about-user">
	<div class="popup-layer"></div>
	<div class="popup addphoto-popup" style="width: 615px">
		<div class="popup-cont">
			<div class="close"></div>
			<header>
				<h2>Выберите фоновое изображение</h2>
			</header>
			<div class="cont">
				<p>Наша коллекция</p>
				
				<table id="samples">
					<tr>
						<td><div class="img"><img src="/images/userpage/banner1_small.jpg" title="сэмпл 1" data-img="banner1.jpg" /></div></td>
						<td><div class="img"><img src="/images/userpage/banner2_small.jpg" title="сэмпл 2" data-img="banner2.jpg" /></div></td>
						<td><div class="img"><img src="/images/userpage/banner3_small.jpg" title="сэмпл 3" data-img="banner3.jpg" /></div></td>
						<td><div class="img"><img src="/images/userpage/banner4_small.jpg" title="сэмпл 4" data-img="banner4.jpg" /></div></td>
						<td><div class="img"><img src="/images/userpage/banner5_small.jpg" title="сэмпл 5" data-img="banner5.jpg" /></div></td>
					</tr>
					<tr>
						<td><div class="img"><img src="/images/userpage/banner6_small.jpg" title="сэмпл 1" data-img="banner6.jpg" /></div></td>
						<td><div class="img"><img src="/images/userpage/banner7_small.jpg" title="сэмпл 2" data-img="banner7.jpg" /></div></td>
						<td><div class="img"><img src="/images/userpage/banner8_small.jpg" title="сэмпл 3" data-img="banner8.jpg" /></div></td>
						<td><div class="img"><img src="/images/userpage/banner9_small.jpg" title="сэмпл 4" data-img="banner9.jpg" /></div></td>
						<td><div class="img"><img src="/images/userpage/banner10_small.jpg" title="сэмпл 5" data-img="banner10.jpg" /></div></td>
					</tr>					
				</table>
				<p>Загрузите свое  изображение</p>
				<label for="banner_input">
					<div class="input-file btn-funcmenu">
						<input type="file" class="" id="banner_input" name="banner_input" />Выбрать файл
					</div>
				</label>
				<br />
				<span class="error" style="dispaly:none"></span>
				<br/><br/>
			</div>
		</div>
	</div>
	<div class="wrapfix">
		<?=View::factory('layouts/header', array('style' => 'display:none'))?>
		<div class="m_content">
			<?=$_content?>
		</div>
	</div>
	<div class="wrapfix footer-wrap">
		<?=View::factory('layouts/footer')?>
	</div>
</body>  
</html>