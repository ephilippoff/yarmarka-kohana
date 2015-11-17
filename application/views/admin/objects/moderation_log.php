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

<div class="control-group only2" >		

	
	<form action="" class="navbar-form navbar-left" role="search">
		<div class="form-group">			
			<input value="<?=strip_tags($object_id)?>" type="text" class="form-control" placeholder="По object id" name="object_id">
			<input type="submit" class="btn btn-default" value="Искать">
			<a href="/<?=Request::current()->uri()?>">Сбросить</a>
		</div>
	</form>	
</div>	

<table class="table table-hover table-condensed articles">
<tr>
	<th>ID</th>
	<th>Модератор</th>
	<th>Дата</th>
	<th>Описание</th>
	<th>ID объявления</th>
</tr>
<?php foreach ($list as $item) : ?>
<tr>
	<td><?=$item->id?></td>
	<td><?=$item->user_fullname?>(<?=$item->user_email?>)</td>
	<td><?=$item->createdon?></td>
	<td><?=$item->description?></td>
	<td><?=$item->object_id?></td>
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