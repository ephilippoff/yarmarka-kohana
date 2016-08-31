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
			<div class="img" style="display: inline-block; width: 100%; max-width: 165px; text-align: center;" align="center">
				<img src="http://yarmarka.biz/static/develop/production/images/2_165.jpg">
		</div>
			<div class="text" style="text-align: left; line-height: 1.6; display: inline-block; width: 100%; max-width: 630px; margin-bottom: 15px;" align="left">
				<h2 style="font-size: 1.5em; display: block; text-align: center; margin-bottom: 15px;" align="center"><?=$title?></h2>
				<p style="padding:10px;">Количество приобретенных купонов: <?=count($kupons)?>.</p>
					<? if ($for_supplier == TRUE): ?>
						<a href="http://yarmarka.biz/detail/<?=$object_id?>"><?=$title?></a>
					<? else: ?>
						<p style="padding:10px 0;"><?=$title?>: 
							<? foreach ($kupons as $kupon) : ?>
								<span><a href="http://yarmarka.biz/kupon/print/<?=$kupon->id?>?key=<?=$key?>">№<?=Text::format_kupon_number(Model_Kupon::decrypt_number($kupon->number))?></a></span>,
							<? endforeach; ?>
						</p>
						<p style="padding:10px 0;">Вам необходимо предъявить эти номера, либо печатную версию купонов поставщику товара/услуги</p>
					<? endif;?>
					<? if ($for_supplier == TRUE): ?>
						<p style="padding:10px 0;">Контакты покупателя</p>
						<table>
							<tr><td>Email:</td><td><?=$delivery->email?></td></tr>
							<tr><td>Имя:</td><td><?=$delivery->name?></td></tr>
							<tr><td>Телефон:</td><td><?=$delivery->phone?></td></tr>
						</table>
						<p style="padding:10px;">Информация об акции</p>
						<table>
					<tr><td>Продано:</td><td><?=$sold_balance?></td></tr>
							<tr><td>Остаток:</td><td><?=$avail_balance?></td></tr>
						</table>
					<? else: ?>
						<p style="padding:10px;"><a href="http://yarmarka.biz/cart/order/<?=$order->id?>">Перейти к заказу</a></p>
					<? endif;?>
				<div class="footer" style="text-align: right; font-size: 12px; margin: 10px 0;" align="right">С уважением, команда «Ярмарка-онлайн»</div>
				<span style="color: #6c6c6c; font-size: 9px;">Пожалуйста, не отвечайте на это письмо, т.к. указанный почтовый адрес используется только для рассылки уведомлений</span>
			</div>
		</div>
	</div>

</body>
</html>