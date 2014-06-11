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

<form class="form-horizontal" method="post" enctype="multipart/form-data">
	<div class="control-group <?=Arr::get($errors, 'title') ? 'error' : ''?>">
		<label class="control-label">Заголовок:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'title', @$ad_element->title)?>" class="input-block-level" name="title" id="title"  />
			<span class="help-inline"><?=Arr::get($errors, 'title')?></span>
		</div>
	</div>

	<div class="control-group <?=Arr::get($errors, 'link') ? 'error' : ''?>">
		<label class="control-label">Ссылка:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'link', @$ad_element->link)?>" class="input-block-level" name="link" id="title"  />
			<span class="help-inline"><?=Arr::get($errors, 'link')?></span>
		</div>
	</div>
		
	<div class="control-group <?=Arr::get($errors, 'class') ? 'error' : ''?>">
		<label class="control-label">Цвет ссылки:</label>
		<div class="controls">
			<?=Form::select('class', array('black' => 'Черный', 'red' => 'Красный', 'green' => 'Зеленый'), Arr::get($_POST, 'class', @$ad_element->class)) ?>
		</div>	
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'class') ? 'error' : ''?>">
		<label class="control-label">Города:</label>
		<div class="controls">
			<?=Form::select('cities[]', array(1 => 'Тюменская область', 1919 => 'Тюмень', 1947 => 'Нефтеюганск', 1948 => 'Нижневартовск', 1979 => 'Сургут'), null, array('multiple', 'size' => 5)) ?>
		</div>	
	</div>	

	<div class="control-group <?=Arr::get($errors, 'class') ? 'error' : ''?>">
		<label class="control-label">Группы:</label>
		<div class="controls">
			<?=Form::select('reklama_group[]', $reklama_group, null, array('multiple', 'size' => 10)) ?>
		</div>	
	</div>		
	
	<div class="control-group only2" >		
		<label class="control-label">Баннер:</label>
		<div class="controls">
			<input type="file" class="input-small" placeholder="banner" name="image" >
		</div>		
	</div>
	
	<div class="control-group only2" >		
		<label class="control-label">Интервал для показа:</label>
		<div class="controls">
			от
			<input type="text" class="input-small dp" placeholder="от" name="start_date" value="<?=Arr::get($_POST, 'start_date', date('Y-m-d'))?>">
			до
			<input type="text" class="input-small dp" placeholder="до" name="end_date" value="<?=Arr::get($_POST, 'end_date', date('Y-m-d', strtotime('+7 days')))?>">
		</div>		
	</div>

	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Сохранить</button>
			<!--<button type="reset"  class="btn">Reset</button>-->
		</div>
	</div>
</form>