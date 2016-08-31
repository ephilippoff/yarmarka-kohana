<!DOCTYPE html>
<html lang="ru">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta charset="utf-8">
	<title></title>
</head>
<body align="center" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 14px; margin: 0; padding: 0;">

	<div class="email_cont" style="max-width: 800px; margin: 0 auto; padding: 0px 15px;">
		<div class="header" style="background: #ECF3F7; padding: 10px;">
			<div class="logo" style="display: inline-block; font-size: 11px; width: 50%;">
				<img src="http://yarmarka.biz/static/develop/production/images/logo.png" alt="Ярмарка" style="width: 160px; height: 31px;"><br>
				Сайт бесплатных объявлений
			</div>
			<div class="site" style="display: inline-block; width: 49%; text-align: right; font-size: 24px; color: #D44234;" align="right">
				yarmarka<span style="color: #5B6772;">.biz</span>
			</div>
		</div>

		<div class="add" style="text-align: right; margin-top: 20px;" align="right">
			<a href="http://yarmarka.biz/add" target="_blank" style="color: #fff; text-decoration: none; background: #D44234; padding: 5px 10px;">Подать объявление</a>
		</div>

		<div class="content" style="text-align: center; margin-top: 30px;" align="center">
			<div class="img" style="display: block; width: 100%; text-align: center;" align="center">
				<img src="http://yarmarka.biz/static/develop/production/images/2_165.jpg">
			</div>
			<div class="text" style="text-align: left; line-height: 1.6; display: inline-block; width: 100%; max-width: 630px; margin-bottom: 15px;" align="left">
				<h2 style="font-size: 1.5em; display: block; text-align: center; margin-bottom: 15px;" align="center">Оплачена услуга на сайте!</h2>
				№ Заказа <?=$order->id?><br>
				Дата оплаты <?=date('d.m.Y H:i:s', strtotime($order->payment_date))?><br>
				<?php foreach ($orderItems as $orderItem) : ?>
				<table cellspacing="0" style="font-size: 13px; border-collapse: collapse; width: 100%; text-align: right; margin: 15px auto 0; border: 1px solid #ccc;">
					<tr>
						<td style="color: #616161; text-align: left;" align="left">Услуга</td>
						<td>
							<a href="http://yarmarka.biz/detail/<?=$orderItem->object->id?>" style="color: #D44234;"><?=$orderItem->title?></a>
						</td>
					</tr>
					<tr>
						<td style="color: #616161; text-align: left;" align="left">Параметры</td>
						<td><?=$orderItem->service->description?></td>
					</tr>
					<tr>
						<td style="color: #616161; text-align: left;" align="left">Цена</td>
						<td><?=number_format($orderItem->service->price)?> р.</td>
					</tr>
					<tr>
						<td style="color: #616161; text-align: left;" align="left">Сумма</td>
						<td><?=number_format($orderItem->service->price_total)?> р.</td>
					</tr>
				</table>
			<?php endforeach; ?><hr>
			<div>
				Сумма счета: <?=number_format($order->sum)?> р.
			</div>
			<br><br><div style="text-align: center;" align="center">
			<?=$object->full_text?> <br>
			E-mail: <?=$user->email?> <br>
			Контакты объявления: <br>  
			<?  foreach ($contacts->getprepared_all() as $contact): ?>
			<?=$contact->contact_clear?>,
		<? endforeach; ?>
	</div>
	<span style="color: #6c6c6c; font-size: 9px;">Пожалуйста, не отвечайте на это письмо, т.к. указанный почтовый адрес используется только для рассылки уведомлений</span>
</div>
</div>
</div>	

</body>
</html>
