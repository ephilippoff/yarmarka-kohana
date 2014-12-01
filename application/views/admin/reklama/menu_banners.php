<?php
	$main_cities = array(1 => 'Тюменская область', 1919 => 'Тюмень', 1947 => 'Нефтеюганск', 1948 => 'Нижневартовск', 1979 => 'Сургут');
	$cities = '';
	//Параметры для uri сортировок и параметры для uri фильтра
	$params = $params_for_filter = array();
	//Запоминаем сортировку для фильтра
	if (!empty($sort_by) and !empty($sort))
	{
		$params_for_filter['sort_by'] = $sort_by; 
		$params_for_filter['sort'] = $sort;		
	}
		
//	if (!$only_active) $params_for_filter['only_active'] = '';
//	else $params['only_active'] = '';		
?>

<!--<div class="control-group only2" >		
	<label for="only_active" class="control-label">
		<input id="only_active" type="checkbox" class="input-small" placeholder="" name="only_active" <?php //if ($only_active) : ?> checked <?php //endif; ?> onclick=" window.location='/<?//=Request::current()->uri().URL::query($params_for_filter, false)?>' ">
		Только активные	
	</label>					
</div>	-->

<a href="/khbackend/reklama/add_menu_banner" style="margin-bottom: 20px;display: inline-block;">Добавить баннер</a>

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
		<th>Рубрика</th>
		<th>Города</th>		
		<th>Баннер</th>		
		<th>X</th>
		<th>Y</th>
		<th>
			Дата окончания<br>
			<?php if ($sort_by == 'date_expired' and $sort == 'desc') : ?>
				<a class="sort" href="/<?=Request::current()->uri().URL::query(array_merge($params, array('sort_by' => 'date_expired', 'sort' => 'asc')), false) ?>">(по возр.)</a>
			<?php else : ?>
				<a class="sort" href="/<?=Request::current()->uri().URL::query(array_merge($params, array('sort_by' => 'date_expired', 'sort' => 'desc')), false) ?>">(по убыв.)</a>
			<?php endif;?>				
		</th>
		<th>Визиты</th>
		<th>Ссылка</th>
		<th></th>
	</tr>
	<?php foreach ($banners_list as $ads_element) : ?>		
		<?php 		
			//Вытаскиваем названия городов
			$cities = '';
			foreach (explode(',', trim($ads_element->cities,'{}')) as $code)
				if (isset($main_cities[$code])) 
					$cities .= $main_cities[$code].', ';		
				
		?>
	
		<tr>			
			<td><?=$ads_element->id?></td>
			<td><?=$ads_element->category->title?></td>
			<td><?=trim($cities,', ')?></td>
			<td class="td-banner">
				<div style="position: relative;">
					<?php if (is_file(DOCROOT.'uploads/banners/menu/'.$ads_element->image)) : ?>
							<img src="<?='/uploads/banners/menu/'.$ads_element->image?>" />							
					<?php endif;?>
				</div>
			</td>
			<td><?=$ads_element->x?></td>
			<td><?=$ads_element->y?></td>
			<td><?=$ads_element->date_expired?></td>
			<td><?=$ads_element->visits?></td>
			<td><a href="http://<?=$ads_element->href?>"><?=$ads_element->href?></a></td>
			<td>				
				<a href="<?=Url::site('khbackend/reklama/edit_menu_banner/'.$ads_element->id)?>" class="icon-pencil"></a>
				<a href="<?=Url::site('khbackend/reklama/delete_menu_banner/'.$ads_element->id)?>" class="icon-trash delete_article"></a>
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
