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
	$states = array(0 => 'Неактивна', 1 => 'Активна', 2 => 'Предпросмотр');
?>

<form class="form-horizontal" method="post" enctype="multipart/form-data">

	<div class="control-group <?=Arr::get($errors, 'menu_name') ? 'error' : ''?>">
		<label class="control-label">Меню:</label>
		<div class="controls">
			<?=Form::select('menu_name', $menu_names, Arr::get($_POST, 'menu_name')) ?>
			<span class="help-inline"><?=Arr::get($errors, 'menu_name')?></span>
		</div>	
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'category_id') ? 'error' : ''?>">
		<label class="control-label">Рубрика:</label>
		<div class="controls">
			<?=Form::select('category_id', $categories, Arr::get($_POST, 'category_id'), array( 'size' => 15)) ?>
			<span class="help-inline"><?=Arr::get($errors, 'category_id')?></span>
		</div>	
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'kupon_category_id') ? 'error' : ''?>">
		<label class="control-label">Рубрика купонов:</label>
		<div class="controls">
			<?=Form::select('kupon_category_id', $kupon_categories, Arr::get($_POST, 'kupon_category_id')) ?>
			<span class="help-inline"><?=Arr::get($errors, 'kupon_category_id')?></span>
		</div>	
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'news_category_id') ? 'error' : ''?>">
		<label class="control-label">Рубрика новостей:</label>
		<div class="controls">
			<?=Form::select('news_category_id', $news_categories, Arr::get($_POST, 'news_category_id')) ?>
			<span class="help-inline"><?=Arr::get($errors, 'news_category_id')?></span>
		</div>	
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'cities') ? 'error' : ''?>">
		<label class="control-label">Города:</label>
		<div class="controls">
			<?=Form::select('cities[]', $cities, Arr::get($_POST, 'cities'), array('multiple', 'size' => 10)) ?>
			<span class="help-inline"><?=Arr::get($errors, 'cities')?></span>
		</div>	
	</div>	
	
	<div class="control-group only2" >		
		<label class="control-label">Дата старта(включительно):</label>
		<div class="controls">			
			<input type="text" class="input-small dp" placeholder="Дата старта" name="date_start" value="<?=Arr::get($_POST, 'date_start', date('Y-m-d'))?>">
		</div>		
	</div>	
	
	<div class="control-group only2" >		
		<label class="control-label">Дата окончания(включительно):</label>
		<div class="controls">			
			<input type="text" class="input-small dp" placeholder="Дата окончания" name="date_expired" value="<?=Arr::get($_POST, 'date_expired', date('Y-m-d', strtotime('+7 days')))?>">
		</div>		
	</div>	
			
	<div class="control-group only2 " >		
		<label class="control-label">Баннер:</label>
		<div class="controls">
			<input type="file" class="input-small" placeholder="banner" name="image" >
			<span class="help-inline"><?=Arr::get($errors, 'image')?></span>
		</div>		
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'x') ? 'error' : ''?>">
		<label class="control-label">X:</label>
		<div class="controls">
			<input type="text" class="input-small" placeholder="x" name="x" value="<?=Arr::get($_POST, 'x', 0)?>">
		</div>	
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'y') ? 'error' : ''?>">
		<label class="control-label">Y:</label>
		<div class="controls">
			<input type="text" class="input-small" placeholder="y" name="y" value="<?=Arr::get($_POST, 'y', 0)?>">
		</div>	
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'menu_width') ? 'error' : ''?>">
		<label class="control-label">Ширина меню:</label>
		<div class="controls">
			<input type="text" class="input" placeholder="Введите значение в px" name="menu_width" value="<?=Arr::get($_POST, 'menu_width', 0)?>">
		</div>	
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'menu_height') ? 'error' : ''?>">
		<label class="control-label">Высота меню:</label>
		<div class="controls">
			<input type="text" class="input" placeholder="Введите значение в px" name="menu_height" value="<?=Arr::get($_POST, 'menu_height', 0)?>">
		</div>	
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'map_params') ? 'error' : ''?>">
		<label class="control-label">Координаты:</label>
		<div class="controls">
			<input type="text" class="input-block-level" placeholder="Введите координаты" name="map_params" value="<?=Arr::get($_POST, 'map_params', '')?>">
		</div>	
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'href') ? 'error' : ''?>">
		<label class="control-label">Ссылка:</label>
		<div class="controls">
			http:// <input type="text" class="input" placeholder="Ссылка" name="href" value="<?=Arr::get($_POST, 'href', '')?>">
		</div>	
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'state') ? 'error' : ''?>">
		<label class="control-label">Статус:</label>
		<div class="controls">
			<?=Form::select('state', $states, Arr::get($_POST, 'state')) ?>
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