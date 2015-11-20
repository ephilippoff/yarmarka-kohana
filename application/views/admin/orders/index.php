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
			<input value="<?=strip_tags($id)?>" type="text" class="form-control" placeholder="По id" name="id">
			<input type="submit" class="btn btn-default" value="Искать">
			<a href="/<?=Request::current()->uri()?>">Сбросить</a>
		</div>
	</form>	
</div>	

<table class="table table-hover table-condensed articles">
<tr>
	<th>ID</th>
	<th>created</th>
	<th>user_id</th>
	<th>state</th>
	<th>sum</th>
	<th>comment</th>
	<th>params</th>
	<th>key</th>
	<th>payment_url</th>
	<th>payment_date</th>
	<th>cancel_date</th>
</tr>
<?php foreach ($list as $item) : ?>
<tr style="background-color: #ccc">
	<td><?=$item->id?></td>
	<td><?=$item->created?></td>
	<td><?=$item->user_id?></td>
	<td><?=$item->state?></td>
	<td><?=$item->sum?></td>
	<td><?=$item->comment?></td>
	<td><?=$item->params?></td>
	<td><?=$item->key?></td>
	<td style="word-break: break-all"><?=$item->payment_url?></td>
	<td><?=$item->payment_date?></td>
	<td><?=$item->cancel_date?></td>
</tr>

	<?php	
		$order_item = ORM::factory('Order_Item')
				->where('order_id', '=', $item->id)
				->find_all();
	?>

	<?php foreach ($order_item as $o_item) : ?>
		<tr>
			<td style="font-weight: bold">id</td>
			<td style="font-weight: bold">order_id</td>
			<td style="font-weight: bold">object_id</td>
			<td style="font-weight: bold">service_id</td>
			<td style="font-weight: bold" colspan="7">params</td>	
		</tr>
		<tr>
			<td><?=$o_item->id?></td>
			<td><?=$o_item->order_id?></td>
			<td><?=$o_item->object_id?></td>
			<td><?=$o_item->service_id?></td>
			<td colspan="7" style="word-break: break-all"><?=$o_item->params?></td>
		</tr>	
	<?php endforeach;?>

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