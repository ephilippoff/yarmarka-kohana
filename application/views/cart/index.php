<? if (count($cartTempItems) == 0): ?>
	Корзина пуста
	<? return; ?>
<? endif;?>

<table>
	<tr>
		<th>#</th>
		<th>Наименование</th>
		<th>Количество</th>
		<th>Цена</th>
		<th>Сумма</th>
		<th></th>
	</tr>
	<? foreach ($cartTempItems as $key => $item): ?>
		<tr data-id="<?=$item->id?>" data-type="object" data-object-id="<?=$item->object_id?>" data-price="<?=$item->price?>" data-quantity="<?=$item->quantity?>" class="fn-cartitem">
			<td><?=$item->id?></td>
			<td><?=$item->title?></td>
			<td>
				<input type="number" class="fn-quanitity" value="<?=$item->quantity?>" min="0" <? if ($item->balance>0) { ?> max="<?=$item->balance?>" <?} else {?> max="100" <?}?> >
				<? if ($item->balance < 0): ?>
					<span>(неограничено)</span>
				<? elseif ($item->balance == 0):?>
					<span style="color: red;">(нет в наличии)</span>
				<? elseif ($item->balance > 0):?>
					<span>(доступно <?=$item->balance?>)</span>
				<? endif; ?>
			</td>
			<td><?=$item->price?></td>
			<td class="fn-itemsum"><?=$item->price * $item->quantity?></td>
			<td><a class="fn-delete">Удалить</a></td>
		</tr>
	<? endforeach; ?>
	<tr>
		<td></td>
		<td></td>
		<td></td>		
		<td></td>
		<td class="fn-endsum"><?=$sum?></td>
	</tr>
</table>

<? if ($user): ?>
	<textarea class="fn-comment" id="" cols="30" rows="10" style="border:1px solid black;">
	<? if ($order): ?>
		<?=$order->comment?>
	<? endif; ?>
	</textarea>

	<input type="submit" class="fn-save" value="Перейти к оплате">

<? else: ?>
	<a href="/user/login?return=cart">Вход</a>
	<a href="/user/registration?return=cart">Регистрация</a>
<? endif; ?>

<div class="fn-error"></div>