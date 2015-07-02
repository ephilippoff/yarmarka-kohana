<div class="eshop-cont order-cont  page-addobj flat-form-style w800" style="margin:auto;">
	<?
		$error = FALSE;
	?>
	<h1>Заказ № <?=$order->id?></h1>
	<table class="table">
		<tr>
			<th>#</th>
			<th>Наименование</th>
			<th>Количество</th>
			<th>Цена</th>
			<th>Сумма</th>
		</tr>
		<? $sum = 0; ?>
		<? $i = 1; ?>
		<? foreach ($orderItems as $item): ?>
			
			<tr>
				<td><?=$i?></td>
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
				<td><?=number_format(intval($item->price), 0, null, ' ')?> р.</td>
				<td><?=number_format(intval($item->price) * intval($item->quantity), 0, null, ' ')?> р.</td>
			</tr>
			<? number_format($sum += intval($item->price) * intval($item->quantity), 0, null, ' '); ?>
			<? $i++; ?>
		<? endforeach; ?>
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td align="right">Итого:</td>
			<td><?=number_format($sum)?> р.</td>
		</tr>
	</table>
	
	
	<div class="messages-cont mb20">
	
		<? if ($error): ?>
			<p>Некоторых товаров нет в наличии, сделайте корректировку заказа</p>
			<p>
				<a href="/cart">Вернуться к редактирвоанию заказа</a>
			</p>
			<? return; ?>
		<? endif;?>
		
		<? if ($state == "initial"):?>
			<p>Мы забронировали для вас данные товарные позиции. У вас есть 30 минут чтобы оплатить заказ.</p>
			<form action="/cart/pay" method="POST">
				<input type="hidden" name="id" value="<?=$order->id?>">
				<? if (in_array("with-shipping", $sale_types)):?>
					<hr>
					<p class="mb10">Внимательно заполните поля для доставки</p>
					<div class="ta-l mb10 field">
						<p><b>Город:</b></p>
						<select name="city" class="input fields">
							<? foreach ($cities as $key => $city): ?>
								<? 
									$selected = "";
									if  ($errors_post->city == $key) {
										$selected = "selected";
									} elseif ($user_city_id == $key) {
										$selected = "selected";
									}

								?>
								<option value="<?=$key?>" <?=$selected?>><?=$city?></option>
							<? endforeach; ?>
						</select>
					</div>
					<div class="ta-l mb10 field">
						<p><b>Адрес:</b></p>
						<input class="input fields" type="text" name="address" value="<?=$errors_post->address?>">
					</div>
					<div class="ta-l mb10 field">
						<p><b>Телефон:</b></p>
						<input class="input fields" type="text" name="phone" value="<?=$errors_post->phone?>">
					</div>
					<div class="ta-l mb10 field">
						<p><b>Комментарий:</b></p>
						<textarea class="fn-comment input comment-area" rows="5" name="comment">
						<? if ($errors_post->comment): ?>
							<?=$errors_post->comment?>
						<? else: ?>
							<?=$order->comment?>
						<? endif; ?>
						</textarea>
						<span class="hint">Например, укажите предпочитаемое время доставки</span>
					</div>
				<? endif; ?>
				
				<div class="fn-error errors-cont mb20 red">
					<? if ($errors): ?>
						<? $err_values = array_values($errors); ?>
						<?=join("<br>", $err_values);?>
					<? endif; ?>
				</div>

				<input type="submit" class="button submit mt20" value="Перейти к оплате">
			</form>
			<p class="mt20">
				<a href="/cart">Вернуться к корзине</a>
			</p>
		<? elseif ($state == "notPaid"):?>
			<p>Заказ в ожидании оплаты</p>
			<hr>
			<p class="mb10">Информация по заказу</p>
			<form action="/cart/pay" method="POST">
				<input type="hidden" name="id" value="<?=$order->id?>">
				<? if ($orderParams->address): ?>
					<div class="ta-l mb10 field">
						<p><b>Адрес:</b></p>
						<p><?=$orderParams->address?></p>
					</div>
				<? endif; ?>

				<? if ($orderParams->phone): ?>
					<div class="ta-l mb10 field">
						<p><b>Телефон:</b></p>
						<p><?=$orderParams->phone?></p>
					</div>
				<? endif; ?>

				<? if ($orderParams->comment): ?>
					<div class="ta-l mb10 field">
						<p><b>Комментарий:</b></p>
						<p><?=$orderParams->comment?></p>
					</div>
				<? endif; ?>

				<input type="submit" class="button submit mt20" value="Перейти к счету в платежной системе">

				
			</form>
		<? elseif ($state == "paid"):?>
			<p>Счет оплачен. В будущем вы можете вернуться к этой странице через <a href="/user/orders">историю заказов</a></p>
		<? elseif ($state == "cancelPayment"):?>
			<p>Заказ отменен. Истекло время оплаты, либо отмена инициирована пользователем</p>
		<? endif; ?>
	</div>
</div>
