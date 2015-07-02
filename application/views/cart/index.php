<div class="eshop-cont cart-cont page-addobj w800" style="margin:auto;">
	<h1>Корзина</h1>
	<? if (count($cartTempItems) == 0): ?>
		<p class="msg">Корзина пуста</p>
		<? return; ?>
	<? endif;?>
	
	<table class="cart-table table">
		<tr>
			<th>#</th>
			<th>Наименование</th>
			<th>Количество</th>
			<th>Цена</th>
			<th>Сумма</th>
			<th></th>
		</tr>
		<? $i = 1; ?>
		<? foreach ($cartTempItems as $key => $item): ?>
			<tr data-id="<?=$item->id?>" data-type="object" data-object-id="<?=$item->object_id?>" data-price="<?=$item->price?>" data-quantity="<?=$item->quantity?>" class="fn-cartitem">
				<td><?=$i?></td>
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
				<td><?=number_format($item->price, 0, null, ' ')?>  р.</td>
				<td class="fn-itemsum itemsum"><?=number_format($item->price * $item->quantity, 0, null, ' ')?> р.</td>
				<td><a class="fn-delete">Удалить</a></td>
			</tr>
			<? $i++; ?>
		<? endforeach; ?>
		<tr>
			<td></td>
			<td></td>
			<td></td>		
			<td></td>
			<td class="fn-endsum"><?=number_format($sum)?> р.</td>
		</tr>
	</table>
	
	<? if ($user): ?>
		<div class="ta-l mb20">
			<input type="submit" class="fn-save button submit p20" value="Перейти к оформлению заказа">
		</div>
	<? else: ?>
		<div class="ta-l mb20">
			<a href="/user/login?return=cart">Вход</a>
			<a href="/user/registration?return=cart">Регистрация</a>
		</div>
	<? endif; ?>
		
	<div class="fn-error errors-cont mb20 red"></div>	
	
</div>