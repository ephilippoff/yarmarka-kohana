<?=HTML::script('/js/adaptive/jquery.flot.min.js')?>
<?=HTML::script('/js/adaptive/jquery.flot.time.min.js')?>

<?php
	$main_cities = array(1 => 'Тюменская область', 1919 => 'Тюмень', 1947 => 'Нефтеюганск', 1948 => 'Нижневартовск', 1979 => 'Сургут');
	
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

<div class="control-group only2" >		

	
	<form action="" class="navbar-form navbar-left" role="search">
		<div class="form-group">
			<label for="only_active" class="control-label">
				<input id="only_active" type="checkbox" class="input-small" placeholder="" name="only_active" <?php if ($only_active) : ?> checked <?php endif; ?> >
				Только активные	
			</label>			
			<input value="<?=strip_tags($s)?>" type="text" class="form-control" placeholder="По заголовкам и комментам" name="s">
			<input type="submit" class="btn btn-default" value="Искать">
			<a href="/<?=Request::current()->uri()?>">Сбросить</a>
		</div>
	</form>	
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
		<th>Заголовок</th>
		<th>Баннер</th>
		<th>Цвет</th>
		<th>
			Дата старта<br>
			<?php if ($sort_by == 'start_date' and $sort == 'desc') : ?>
				<a class="sort" href="/<?=Request::current()->uri().URL::query(array_merge($params, array('sort_by' => 'start_date', 'sort' => 'asc')), false) ?>">(по возр.)</a>
			<?php else : ?>
				<a class="sort" href="/<?=Request::current()->uri().URL::query(array_merge($params, array('sort_by' => 'start_date', 'sort' => 'desc')), false) ?>">(по убыв.)</a>
			<?php endif;?>			
		</th>
		<th>
			Дата окончания<br>
			<?php if ($sort_by == 'end_date' and $sort == 'desc') : ?>
				<a class="sort" href="/<?=Request::current()->uri().URL::query(array_merge($params, array('sort_by' => 'end_date', 'sort' => 'asc')), false) ?>">(по возр.)</a>
			<?php else : ?>
				<a class="sort" href="/<?=Request::current()->uri().URL::query(array_merge($params, array('sort_by' => 'end_date', 'sort' => 'desc')), false) ?>">(по убыв.)</a>
			<?php endif;?>				
		</th>
		<th>Тип</th>
		<th>Активность</th>
		<th>Просмотры</th>
		<th>Клики</th>
		<th>Города</th>
		<th>Группы</th>
		<th>Комментарий</th>
		<th></th>
	</tr>
	<?php foreach ($ads_list as $ads_element) : ?>		
		<?php 		
			$matches = array();
			$visits = $cities = '';
			$is_finded_id = false;

			//Вытаскиваем названия городов
			foreach (explode(',', trim($ads_element->cities,'{}')) as $code)
				if (isset($main_cities[$code])) 
					$cities .= $main_cities[$code].', ';		
			
			//Определяем позиции ссылок во времени: просрочка, не более трех дней до просрочки
			if (time() > strtotime($ads_element->end_date))
				$class = 'color1';
			elseif (time() < strtotime($ads_element->end_date) and time() > strtotime($ads_element->end_date.'-3 days')) 
				$class = 'color2';
			else
				$class = '';
		
			//Смотрим просмотры у объявления по ссылке на него
			if (strrpos($ads_element->link, Kohana::$config->load('common.main_domain')) !== false)//Если страница находится на нашем домене
			{	
				preg_match('/\d+$/', $ads_element->link, $matches);	//Ищем id объявления			
				$is_finded_id = isset($matches[0]) and (int)$matches[0];
				if ($is_finded_id)  //Если найден id
					$visits = ORM::factory('Object')->where('id', '=', (int)$matches[0])->find()->visits;
			}
		?>
	
		<tr class="<?=$class?> type<?=$ads_element->type?>">			
			<td><?=$ads_element->id?></td>
			<td><a target="_blank" href="<?=$ads_element->link?>"><?=$ads_element->title?></a></td>
			<td class="td-banner">
				<div style="position: relative;">
					<?php if (is_file(DOCROOT.'uploads/banners/'.$ads_element->image)) : ?>
							<img src="<?='/uploads/banners/'.$ads_element->image?>" />
							<?php if ($ads_element->type == 3) : ?> <div class="wrapper"><span class="title <?=$ads_element->class?>"><?=htmlspecialchars($ads_element->title)?></span></div> <?php endif; ?>
					<?php endif;?>
				</div>
			</td>
			<td><?=$ads_element->class?></td>
			<td><?=$ads_element->start_date?></td>
			<td><?=$ads_element->end_date?></td>
			<td><?=$ads_element->type?></td>
			<td><?php if ($ads_element->active == 1) : ?> Активна <?php else :?> <span class="red"><b>Неактивна</b></span> <?php endif;?></td>
			<td><?=$visits?></td>
			<td><a target="_blank" href="<?=URL::site(Route::get('reklama/linkstat')->uri(array('id' => $ads_element->id)))?>"><?=$ads_element->clicks_count?></a></td>
			<td><?=trim($cities,', ')?></td>
			<td><?=trim($ads_element->groups,'{}')?></td>
			<td><?=$ads_element->comments?></td>
			<td>
				<?php if ($is_finded_id) : ?><span onclick="renderObjectStat(this, <?=(int)$matches[0]?>, 'fn-stat-container');return false;" class="icon-eye-open"></span><?php endif; ?>
				<a href="<?=Url::site('khbackend/reklama/edit/'.$ads_element->id)?>" class="icon-pencil"></a>
				<a href="<?=Url::site('khbackend/reklama/delete/'.$ads_element->id)?>" class="icon-trash delete_article"></a>
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

<div style="display: none;" class="fn-stat-container fn-window stat-container"><span class="fn-close close"></span><div class="fn-inner inner"></div></div>


<script type="text/javascript">
	function renderObjectStat(obj_src, obj_id, canvas_cont)
	{
		var obj_id = isNaN(parseInt(obj_id)) ? 0 : obj_id;

		if (!obj_id) return;

		var canvas_cont = "." + canvas_cont;		
		var canvas = $(canvas_cont).find('.fn-inner');

		var coords = $(obj_src).offset();		

		$.post("/ajax/ajax_get_obj_stat", { obj_id: obj_id}, function(data) {

			var visits = [];
			var contacts_show_count = [];

			for (i=0; i <= data.length - 1; i++)
			{
				visits.push([data[i].date, data[i].visits]);
				contacts_show_count.push([data[i].date, data[i].contacts_show_count]);
			}

			$(canvas_cont).show().offset({top: coords.top, left: coords.left - 347});	
			$.plot(canvas, [ visits, contacts_show_count ], { xaxis: { mode: "time" } });		
		}, 'json');		
	}
	
	$('.fn-close').click(function(){
		$(this).closest('.fn-window').fadeOut();
	});	
</script>