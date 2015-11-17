<!DOCTYPE html>
<html lang=ru>
<head>
<meta charset=utf-8>
<link rel="shortcut icon" href="<?=URL::base('http')?>images/favicon.ico" type="image/x-icon">
<!--<meta name="viewport" id="viewport" content="width=device-width,minimum-  scale=1.0,maximum-scale=10.0,initial-scale=1.0" />-->
<!--<meta content="width=device-width, initial-scale=1.0" name="viewport">-->
<meta name="viewport" id="viewport" content="width=device-width">
<title><?=strip_tags($title)?></title>
<?=Assets::factory('main')->css('iLight.css')?>
<?=Assets::factory('main')->js('ean13.min.js')?>
<style type="text/css">
	body{font-family: Verdana, Geneva, sans-serif;font-size: 12px;color: #333;}
	.fl{float:left}.fr{float:right;}
	/*.row:before, .row:after {content: " ";display: table;}*/	
	.row{overflow: hidden}	
	.kupon-print-cont{width: 600px; margin: 0 auto;position:relative;}
	.text-style1{}
	.tt-u{text-transform: uppercase}
	.ta-c{text-align: center}
	.color-gray{color:gray}
	.text-cont{font-size: 10px;}
	.fs10{font-size: 10px}.fs16{font-size: 16px}.fs18{font-size: 18px}.fs20{font-size: 20px}.fs26{font-size: 26px}
	.fw-b{font-weight: bold}
	.w49p{width: 49%;}
	#ean {
		position:absolute;
		right:0;
	}
</style>
</head>
<body class="adaptiveoff">
	<div class="wrapfix">		
			<?=$_content?>
	  </div>
</body>  
</html>
