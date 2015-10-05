<?php $status = array('1' => 'Непогашен', '2' => 'Погашен', '3' => 'Возврат') ?>

<table class="table table-hover table-condensed promo">
	<tr>
		<th>Id</th>
		<th>Заголовок</th>
		<th>Дата создания</th>
		<th>Баланс</th>		
		<th>Куплено</th>
	</tr>
	<?php foreach ($kupon_objects as $item) : ?>		
		<tr style="background-color: #8ae234">			
			<td><?=$item->id?></td>
			<td><a href="<?=Url::site('detail/'.$item->id)?>"><?=strip_tags($item->title)?></a></td>
			<td><?=$item->date_created?></td>
			<td><?=$item->balance?></td>
			<td><?=isset($kupon_objects_sum[$item->id]->sum) ? $kupon_objects_sum[$item->id]->sum : '' ?></td>
		</tr>
		
		<?php if (isset($kupon_details[$item->id])) : ?>
			<tr>
				<td colspan="5">
					<table class="table">
						<tr>
						<th>Id</th>
						<th>Код</th>
						<th>Дата создания</th>
						<th>Цена</th>
						<th>Цена(номинал)</th>
						<th>№ счета</th>
						<th>Количество</th>
						<th>Номер</th>
						<th>Текст</th>
						<th>Статус</th>		
						</tr>
						<?php foreach ($kupon_details[$item->id] as $detail) : ?>
								<tr>
									<td><a href="<?=Url::site('kupon/'.$detail->id)?>"><?=$detail->id?></a></td>
									<td><?=$detail->code?></td>
									<td><?=$detail->date_created?></td>
									<td><?=$detail->price?>р.</td>
									<td><?=$detail->oldprice?>р.</td>
									<td><?=$detail->invoice_id?></td>
									<td><?=$detail->count?></td>
									<td><?=$detail->number?></td>
									<td><?=$detail->text?></td>
									<td><?=$status[$detail->status]?></td>		
								</tr>
						<?php endforeach; ?>
								
					</table>
				</td>
			</tr>

		<?php endif; ?>
		
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
