<?
	$error = FALSE;
?>
<h1>Заказ № <?=$order->id?></h1>
<table>
	<tr>
		<th>#</th>
		<th>Наименование</th>
		<th>Количество</th>
		<th>Цена</th>
		<th>Сумма</th>
	</tr>
	<? $sum = 0; ?>
	<? foreach ($orderItems as $item): ?>
		
		<tr>
			<td><?=$item->id?></td>
			<td><?=$item->title?></td>
			<td>
				<?=intval($item->quantity)?>
				<? $balance = $getBalance($item->object_id); ?>
				<? if ($state == "initial" AND $item->object_id 
							AND $balance >= 0 AND $balance < intval($item->quantity)):?>
					<span>(Извините, осталось только <?=$balance?>)</span>
					<? $error = TRUE; ?>
				<? endif;?>
			</td>
			<td><?=intval($item->price)?></td>
			<td><?=intval($item->price) * intval($item->quantity)?></td>
		</tr>
		<? $sum += intval($item->price) * intval($item->quantity); ?>
	<? endforeach; ?>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td><?=$sum?></td>
	</tr>
</table>


<p>Ваш комментарий к заказу: <br>	<?=$order->comment?></p>

<? if ($error): ?>
	<a href="/cart">Вернуться к редактирвоанию заказа</a>
	<p>Некоторых товаров нет в наличии, сделайте корректировку заказа</p>
	<? return; ?>
<? endif;?>

<? if ($state == "initial"):?>

	<a href="/cart">Вернуться к редактирвоанию заказа</a>
	<p>Проверьте заказ. Перейдите к оплате для того чтобы его забронировать и оплатить</p>
	<form action="/cart/pay" method="POST">
		<input type="hidden" name="id" value="<?=$order->id?>">
		<input type="submit" value="Перейти к оплате">
	</form>

<? elseif ($state == "notPaid"):?>
	<p>Счет в ожидании оплаты</p>
	<form action="/cart/pay" method="POST">
		<input type="hidden" name="id" value="<?=$order->id?>">
		<input type="submit" value="Перейти к оплате">
	</form>
<? elseif ($state == "paid"):?>
	<p>Счет оплачен. В будущем вы можете вернуться к этой странице через <a href="/user/orders">историю заказов</a></p>
<? elseif ($state == "cancelPayment"):?>
	<p>Оплата счета отменена</p>
<? endif; ?>

<div class="fn-error"></div>