<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Content-Style-Type" content="text/css">
	<title></title>
	<meta name="Generator" content="Cocoa HTML Writer">
	<meta name="CocoaVersion" content="1138.47">
	<style type="text/css">
		p.p1 {margin: 0.0px 0.0px 17.0px 0.0px; font: 13.0px Verdana}
		p.p2 {margin: 0.0px 0.0px 17.0px 0.0px; font: 13.0px Verdana; color: #0000ee}
		p.p3 {margin: 0.0px 0.0px 13.0px 0.0px; font: 13.0px Verdana}
		p.p4 {margin: 0.0px 0.0px 13.0px 0.0px; font: 16.0px Verdana}
		p.p5 {margin: 0.0px 0.0px 13.0px 0.0px; font: 13.0px Verdana; background-color: #ffffff}
		p.p6 {margin: 0.0px 0.0px 28.0px 0.0px; font: 13.0px Verdana}
		span.s1 {color: #0003ff}
		span.s2 {text-decoration: underline}
		span.s3 {text-decoration: underline ; color: #0000ff}
		table.t1 {border-collapse: collapse}
		td.td1 {width: 186.0px; padding: 0.0px 7.0px 0.0px 7.0px}
		td.td2 {width: 507.0px; padding: 0.0px 7.0px 0.0px 7.0px}
		td.td3 {width: 707.0px; padding: 0.0px 7.0px 0.0px 7.0px}
	</style>
</head>
<body>
	<table cellspacing="0" cellpadding="0" class="t1">
		<tbody>
			<tr>
				<td valign="top" class="td1">
					<p class="p2"><img src="<?php echo CI::base('http') ?>images/subscribe.png"></p>
				</td>
				<td valign="top" class="td2">
					<p class="p1"> </p>
					<p class="p2"><span class="s2"><a href="<?php echo CI::base('http') ?>">Газета бесплатных объявлений «Ярмарка»</a></span></p>
				</td>
			</tr>
			<tr>
				<td colspan="2" valign="middle" class="td3">
					<p class="p3"> </p>
					<p class="p4">Здравствуйте, <?=$UserName; ?>!</p>
					<p class="p3">Объявления были сняты с публикации, т.к. телефон <?=$phone?> был заблокирован модератором:</p>
					<?php foreach ($objects as $object) : ?>
					<p class="p3"><a href="<?=$object->get_url()?>"><?=$object->title?></a></p>
					<?php endforeach ?>
					<p class="p3">С уважением,<br />
						<br />
						команда «Ярмарка-онлайн»</p>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="p6"> </p>
		<p class="p3"> </p>
</body>
</html>