<?php
	//Параметры для uri сортировок и параметры для uri фильтра
	$params = $params_for_filter = array();
	//Запоминаем сортировку для фильтра
	if (!empty($sort_by) and !empty($sort))
	{
		$params_for_filter['sort_by'] = $sort_by; 
		$params_for_filter['sort'] = $sort;		
	}	
?>

<table class="table table-hover table-condensed promo">
	<tr>
		<th>
			Id<br>
			<?php if ($sort_by == 'id' and $sort == 'desc') : ?>
				<a class="sort" href="/<?=Request::current()->uri().URL::query(array_merge($params, array('sort_by' => 'id', 'sort' => 'asc')), false) ?>">(по возр.)</a>
			<?php else : ?>
				<a class="sort" href="/<?=Request::current()->uri().URL::query(array_merge($params, array('sort_by' => 'id', 'sort' => 'desc')), false) ?>">(по убыв.)</a>
			<?php endif;?>
		</th>
		<th>Заголовок</th>
		<th>ФИО</th>
		<th>Номер телефона</th>
		<th>
			Дата заявки<br>
			<?php if ($sort_by == 'date_created' and $sort == 'desc') : ?>
				<a class="sort" href="/<?=Request::current()->uri().URL::query(array_merge($params, array('sort_by' => 'date_created', 'sort' => 'asc')), false) ?>">(по возр.)</a>
			<?php else : ?>
				<a class="sort" href="/<?=Request::current()->uri().URL::query(array_merge($params, array('sort_by' => 'date_created', 'sort' => 'desc')), false) ?>">(по убыв.)</a>
			<?php endif;?>			
		</th>
		<th>
			Статус<br>
			<?php if ($sort_by == 'status' and $sort == 'desc') : ?>
				<a class="sort" href="/<?=Request::current()->uri().URL::query(array_merge($params, array('sort_by' => 'status', 'sort' => 'asc')), false) ?>">(по возр.)</a>
			<?php else : ?>
				<a class="sort" href="/<?=Request::current()->uri().URL::query(array_merge($params, array('sort_by' => 'status', 'sort' => 'desc')), false) ?>">(по убыв.)</a>
			<?php endif;?>				
		</th>
		<th>Коммент</th>
	</tr>
	<?php foreach ($kupon_requests as $element) : ?>		
		<tr>			
			<td><?=$element->id?></td>
			<td><?=$element->object_title?> (#<?=$element->object_id?>)</td>
			<td><?=$element->fio?></td>
			<td><?=$element->phone?></td>
			<td><?=$element->date_created?></td>
			<td><?=$element->status?></td>
			<td><?=$element->comment?></td>
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
