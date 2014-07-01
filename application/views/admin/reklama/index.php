<?=HTML::script('/js/adaptive/jquery.flot.min.js')?>
<?=HTML::script('/js/adaptive/jquery.flot.time.min.js')?>

<?php
	$main_cities = array(1 => 'Тюменская область', 1919 => 'Тюмень', 1947 => 'Нефтеюганск', 1948 => 'Нижневартовск', 1979 => 'Сургут');
?>
<table class="table table-hover table-condensed promo">
	<tr>
		<th>
			Id<br>
			<?php if ($sort_by == 'id' and $sort == 'desc') : ?>
				<a class="sort" href="/<?=Request::current()->uri()?>?sort_by=id&sort=asc">(по возр.)</a>
			<?php else : ?>
				<a class="sort" href="/<?=Request::current()->uri()?>?sort_by=id&sort=desc">(по убыв.)</a>
			<?php endif;?>
		</th>
		<th>Заголовок</th>
		<th>Баннер</th>
		<th>Цвет</th>
		<th>
			Дата старта<br>
			<?php if ($sort_by == 'start_date' and $sort == 'desc') : ?>
				<a class="sort" href="/<?=Request::current()->uri()?>?sort_by=start_date&sort=asc">(по возр.)</a>
			<?php else : ?>
				<a class="sort" href="/<?=Request::current()->uri()?>?sort_by=start_date&sort=desc">(по убыв.)</a>
			<?php endif;?>			
		</th>
		<th>
			Дата окончания<br>
			<?php if ($sort_by == 'end_date' and $sort == 'desc') : ?>
				<a class="sort" href="/<?=Request::current()->uri()?>?sort_by=end_date&sort=asc">(по возр.)</a>
			<?php else : ?>
				<a class="sort" href="/<?=Request::current()->uri()?>?sort_by=end_date&sort=desc">(по убыв.)</a>
			<?php endif;?>				
		</th>
		<th>Просмотры</th>
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
	
		<tr class="<?=$class?>">			
			<td><?=$ads_element->id?></td>
			<td><a target="_blank" href="<?=$ads_element->link?>"><?=$ads_element->title?></a></td>
			<td>
				<?php if (is_file(DOCROOT.'uploads/banners/'.$ads_element->image)) : ?>
						<img src="<?='/uploads/banners/'.$ads_element->image?>" />
				<?php endif;?>
			</td>
			<td><?=$ads_element->class?></td>
			<td><?=$ads_element->start_date?></td>
			<td><?=$ads_element->end_date?></td>
			<td><?=$visits?></td>
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