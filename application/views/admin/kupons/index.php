<table class="table table-hover table-condensed promo">
	<tr>
		<th>Id</th>
		<th>Код</th>
		<th>Объявление</th>		
		<th>Цена</th>		
		<th>№ счета</th>
		<th>Количество</th>
		<th>Номер</th>
		<th>Текст</th>
	</tr>
	<?php foreach ($kupons as $item) : ?>		
		<tr>			
			<td><a href="<?=Url::site('kupon/'.$item->id)?>"><?=$item->id?></a></td>
			<td><?=$item->code?></td>
			<td><a href="<?=Url::site('detail/'.$item->object_id)?>"><?=$item->object_title?> (<?=$item->object_id?>)</a></td>
			<td><?=$item->price?></td>
			<td><?=$item->invoice_id?></td>
			<td><?=$item->count?></td>
			<td><?=$item->number?></td>
			<td><?=$item->text?></td>
		</tr>
	<?php endforeach; ?>
</table>

<?php if ($pagination->total_pages > 1) : ?>
<div class="row">
	<div class="span10"><?=$pagination?></div>
	<div class="span2" style="padding-top: 55px;">
		<span class="text-info">Limit:</span>
		<?php foreach (array(50, 100, 150) as $l) : ?>
			<?php if ($l == $limit) : ?>
				<span class="badge badge-info"><?=$l?></span>
			<?php else : ?>
				<a href="#" class="btn-mini" onClick="add_to_query('limit', <?=$l?>)"><?=$l?></a>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>
