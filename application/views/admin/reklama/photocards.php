<?php
	//Параметры для uri сортировок и параметры для uri фильтра
	$params = $params_for_filter = array();
	//Запоминаем сортировку для фильтра
	if (!empty($sort_by) and !empty($sort))
	{
		$params_for_filter['sort_by'] = $sort_by; 
		$params_for_filter['sort'] = $sort;		
	}
		
	if (!$only_active) $params_for_filter['only_active'] = '';
	else $params['only_active'] = '';		
?>

<script type="text/javascript" charset="utf-8">

	$(document).ready(function() {		
		
		$('.fn-start').click(function(event){
			
			event.preventDefault();			
			
			var id = $(this).data("id");
			
			$.post("/ajax/update_photocard", { id:id, active: 1}, function(data){
				
				$('.fn-status'+id).html("Активна");
				$('.fn-td-de'+id).html(data.date_expiration);
				
			}, 'json');			
		})
						
	})	
	
</script>

<div class="control-group only2" >		
	<label for="only_active" class="control-label">
		<input id="only_active" type="checkbox" class="input-small" placeholder="" name="only_active" <?php if ($only_active) : ?> checked <?php endif; ?> onclick=" window.location='/<?=Request::current()->uri().URL::query($params_for_filter, false)?>' ">
		Только оплаченные	
	</label>					
</div>	

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
		<th>Фото</th>
		<th>Заголовок</th>
		<th>Рубрика</th>
		<th>Город</th>
		<th>Дата заявки</th>
		<th>Дней заказано</th>
		<th>
			Дата окончания<br>
			<?php if ($sort_by == 'date_expiration' and $sort == 'desc') : ?>
				<a class="sort" href="/<?=Request::current()->uri().URL::query(array_merge($params, array('sort_by' => 'date_expiration', 'sort' => 'asc')), false) ?>">(по возр.)</a>
			<?php else : ?>
				<a class="sort" href="/<?=Request::current()->uri().URL::query(array_merge($params, array('sort_by' => 'date_expiration', 'sort' => 'desc')), false) ?>">(по убыв.)</a>
			<?php endif;?>				
		</th>
		<th>Активность</th>
		<th>Оплата</th>
	</tr>
	<?php foreach ($photocards_list as $ads_element) : ?>		
		<?php 			
			//Определяем позиции ссылок во времени: просрочка, не более трех дней до просрочки
			if (time() > strtotime($ads_element->date_expiration))
				$class = 'color1';
			elseif (time() < strtotime($ads_element->date_expiration) and time() > strtotime($ads_element->date_expiration.'-3 days')) 
				$class = 'color2';
			else
				$class = '';
		?>
	
		<tr class="<?=$class?>">			
			<td><?=$ads_element->id?></td>			
			<td><img style="max-width: 150px; max-height: 113px;" src="<?=Uploads::get_file_path($ads_element->main_image_filename, '208x208')?>" /></td>
			<td><a target="_blank" href="<?'http://'.Kohana::$config->load('common.main_domain')?>/detail/<?=$ads_element->object_id?>"><?=htmlspecialchars($ads_element->object_title)?></a></td>
			<td><?=$ads_element->category_title?></td>
			<td><?=$ads_element->city_title?></td>
			<td><?=$ads_element->date_created?></td>
			<td><?=$ads_element->periods_count?></td>
			<td class="fn-td-de<?=$ads_element->id?>"><?=$ads_element->date_expiration?></td>
			<td class="fn-status<?=$ads_element->id?>"><?php if ($ads_element->active == 1) : ?> Активна <?php else :?> <a href=""  class="red fn-start" data-id="<?=$ads_element->id?>">Запустить</a> <?php endif;?></td>
			<td><?php if ($ads_element->invoice_id >= 1) : ?> Оплачена <?php else :?> Не оплачена <?php endif;?></td>
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