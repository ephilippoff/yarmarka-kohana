<?php $status = array('1' => 'Непогашен', '2' => 'Погашен', '3' => 'Возврат') ?>

<div class="control-group only2" >		
	<form action="" class="navbar-form navbar-left" role="search">
		<div class="form-group">			
			<input value="<?=strip_tags($s)?>" type="text" class="form-control" placeholder="По номерам и тексту" name="s">
			<input type="submit" class="btn btn-default" value="Искать">
			<a href="/<?=Request::current()->uri()?>">Сбросить</a>
		</div>
	</form>	
</div>

<table class="table table-hover table-condensed promo">
	<tr>
		<th>Id</th>
		<th>Код</th>
		<th>Дата создания</th>
		<th>Объявление</th>		
		<th>Цена</th>
		<th>Цена(номинал)</th>
		<th>№ счета</th>
		<th>Количество</th>
		<th>Номер</th>
		<th>Текст</th>
		<th>Статус</th>
		<th></th>
	</tr>
	<?php foreach ($kupons as $item) : ?>		
		<tr>			
			<td><a href="<?=Url::site('kupon/'.$item->id)?>"><?=$item->id?></a></td>
			<td><?=$item->code?></td>
			<td><?=$item->date_created?></td>
			<td><a href="<?=URL::prep_url(Kohana::$config->load('common.main_domain')).Url::site('detail/'.$item->object_id)?>"><?=$item->object_title?> (<?=$item->object_id?>)</a></td>
			<td><?=$item->price?>р.</td>
			<td><?=$item->oldprice?>р.</td>
			<td><?=$item->invoice_id?></td>
			<td><?=$item->count?></td>
			<td><?=$item->number?></td>
			<td><?=$item->text?></td>
			<td><?=$status[$item->status]?></td>
			<td>
				<a href="<?=Url::site('khbackend/kupons/edit/'.$item->id)?>" class="icon-pencil"></a>
			</td>			
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
