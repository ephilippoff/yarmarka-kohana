<!DOCTYPE html>
<html lang=ru>
<head>
<meta charset=utf-8>
<title></title>

</head>

<body style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 14px; margin: 0; padding: 0;">

	<div class="email_cont" style="max-width: 800px; margin: 0 auto; padding: 0px 15px;">
		<div class="header" style="background: #ECF3F7; padding: 10px;">
			<div class="logo" style="display: inline-block; font-size: 11px; width: 50%;">
				<img src="http://yarmarka.biz/static/develop/production/images/logo.png" alt="Ярмарка" style="width: 160px; height: 31px;"><br>
				Сайт бесплатных объявлений
			</div>
			<div class="site" style="display: inline-block; width: 49%; text-align: right; color: #D44234; text-decoration: none; font-size: 24px;" align="right">
				yarmarka<span style="color: #5B6772;">.biz</span>
			</div>
		</div>

		<div class="add" style="text-align: right; margin-top: 20px;" align="right">
			<a href="http://yarmarka.biz/add" target="_blank" style="color: #fff; text-decoration: none; background: #D44234; padding: 5px 10px;">Подать объявление</a>
		</div>

		<div class="content" style="text-align: center; margin-top: 30px;" align="center">
			<div class="img" style="display: inline-block; width: 100%; max-width: 280px; text-align: center;" align="center">
				<img src="http://yarmarka.biz/static/develop/production/images/9_165.jpg">
		</div>
			<div class="text" style="text-align: left; line-height: 1.6; display: inline-block; width: 100%; max-width: 500px; margin-bottom: 15px;" align="left">
				<h2 style="font-size: 1.5em; display: block; text-align: center; margin-bottom: 15px;" align="center">Здравствуйте!</h2>
				На <a style="color: #d44234;" href="http://yarmarka.biz/">yarmarka.biz</a> произошли следующие действия с вашими объявлениями: <br><br>
				<?php foreach($actions_negative as $action) { ?>
					<p class="p3"><?php echo $action; ?></p>
				<?php } ?>
				<hr>
				<?php foreach($actions_positive as $action) { ?>
					<p class="p3"><?php echo $action; ?></p>
				<?php } ?>
				<div class="footer" style="text-align: right; font-size: 12px; margin: 10px 0;" align="right">С уважением, команда «Ярмарка-онлайн»</div>
				<span style="color: #6c6c6c; font-size: 9px;">Пожалуйста, не отвечайте на это письмо, т.к. указанный почтовый адрес используется только для рассылки уведомлений</span>
			</div>
		</div>
	</div>
</body>  
</html>