<?=HTML::script('bootstrap/datepicker/js/bootstrap-datepicker.js')?>
<?=HTML::style('bootstrap/datepicker/css/datepicker.css')?>

<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
	// enable datepicker
	$('.dp').datepicker({
		format:	'yyyy-mm-dd'
	}).on('changeDate', function(){
		$(this).datepicker('hide');
	});	
});


</script>

<?php
	$main_cities = array(1 => 'Тюменская область', 1919 => 'Тюмень', 1947 => 'Нефтеюганск', 1948 => 'Нижневартовск', 1979 => 'Сургут');
	$date_start   = isset($ad_element->date_start)   ? $ad_element->date_start   : Arr::get($_POST, 'date_start',   date('Y-m-d'));
	$date_expired   = isset($ad_element->date_expired)   ? $ad_element->date_expired   : Arr::get($_POST, 'date_expired',   date('Y-m-d', strtotime('+7 days')));
	$states = array(0 => 'Неактивна', 1 => 'Активна', 2 => 'Предпросмотр');
?>

<form class="form-horizontal" method="post" enctype="multipart/form-data">
	
	<div class="control-group <?=Arr::get($errors, 'menu_name') ? 'error' : ''?>">
		<label class="control-label">Меню:</label>
		<div class="controls">
			<?=Form::select('menu_name', $menu_names, Arr::get($_POST, 'menu_name', @$ad_element->menu_name)) ?>
			<span class="help-inline"><?=Arr::get($errors, 'menu_name')?></span>
		</div>	
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'category_id') ? 'error' : ''?>">
		<label class="control-label">Рубрика:</label>
		<div class="controls">
			<?=Form::select('category_id', $categories, Arr::get($_POST, 'category_id', @$ad_element->category_id), array( 'size' => 15)) ?>
			<span class="help-inline"><?=Arr::get($errors, 'category_id')?></span>
		</div>	
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'kupon_category_id') ? 'error' : ''?>">
		<label class="control-label">Рубрика купонов:</label>
		<div class="controls">
			<?=Form::select('kupon_category_id', $kupon_categories, Arr::get($_POST, 'kupon_category_id', @$ads_element->kupon_category_id)) ?>
			<span class="help-inline"><?=Arr::get($errors, 'kupon_category_id')?></span>
		</div>	
	</div>	
		
	<div class="control-group <?=Arr::get($errors, 'cities') ? 'error' : ''?>">
		<label class="control-label">Города:</label>
		<div class="controls">
			<?=Form::select('cities[]', 
							$main_cities, 
							Arr::get($_POST, 'cities', Dbhelper::convert_pg_array(@$ad_element->cities)), 
							array('multiple', 'size' => 5)) ?>
		</div>	
	</div>	
	
	<div class="control-group only2" >		
		<label class="control-label">Дата старта(включительно):</label>
		<div class="controls">			
			<input type="text" class="input-small dp" placeholder="Дата старта" name="date_start" value="<?=$date_start?>">
		</div>		
	</div>		
	
	<div class="control-group only2" >		
		<label class="control-label">Дата окончания(включительно):</label>
		<div class="controls">			
			<input type="text" class="input-small dp" placeholder="Дата окончания" name="date_expired" value="<?=$date_expired?>">
		</div>		
	</div>		
	
	<div class="control-group only2 " >		
		<label class="control-label">Баннер:</label>
		<div class="controls">
				<?php if (is_file(DOCROOT.'uploads/banners/menu/'.@$ad_element->image)) : ?>
						<p><img style="max-width:150px" src="<?='/uploads/banners/menu/'.@$ad_element->image?>" /></p>
						<!--<p><input type="checkbox" name="delete_image"> Удалить загруженный баннер</p>-->
				<?php endif;?>			
			<input type="file" class="input-small" placeholder="banner" name="image" >
			<span class="help-inline"><?=Arr::get($errors, 'image')?></span>
		</div>		
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'class') ? 'error' : ''?>">
		<label class="control-label">X:</label>
		<div class="controls">
			<input type="text" class="input-small" placeholder="x" name="x" value="<?=Arr::get($_POST, 'x', @$ad_element->x)?>">
		</div>	
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'class') ? 'error' : ''?>">
		<label class="control-label">Y:</label>
		<div class="controls">
			<input type="text" class="input-small" placeholder="y" name="y" value="<?=Arr::get($_POST, 'y', @$ad_element->y)?>">
		</div>	
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'menu_width') ? 'error' : ''?>">
		<label class="control-label">Ширина меню:</label>
		<div class="controls">
			<input type="text" class="input-small" placeholder="Введите значение в px" name="menu_width" value="<?=Arr::get($_POST, 'menu_width', @$ad_element->menu_width)?>">
		</div>	
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'menu_height') ? 'error' : ''?>">
		<label class="control-label">Высота меню:</label>
		<div class="controls">
			<input type="text" class="input" placeholder="Введите значение в px" name="menu_height" value="<?=Arr::get($_POST, 'menu_height', @$ad_element->menu_height)?>">
		</div>	
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'map_params') ? 'error' : ''?>">
		<label class="control-label">Координаты:</label>
		<div class="controls">
			<input type="text" class="input-block-level" placeholder="Введите координаты" name="map_params" value="<?=Arr::get($_POST, 'map_params', @$ad_element->map_params)?>">
		</div>	
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'href') ? 'error' : ''?>">
		<label class="control-label">Ссылка:</label>
		<div class="controls">
			http:// <input type="text" class="input" placeholder="Ссылка" name="href" value="<?=Arr::get($_POST, 'href', @$ad_element->href)?>">
		</div>	
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'state') ? 'error' : ''?>">
		<label class="control-label">Статус:</label>
		<div class="controls">
			<?=Form::select('state', $states, Arr::get($_POST, 'state', @$ad_element->state)) ?>
			<span class="help-inline"><?=Arr::get($errors, 'state')?></span>
		</div>	
	</div>	
	
			
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Сохранить</button>
			<!--<button type="reset"  class="btn">Reset</button>-->
		</div>
	</div>
</form>