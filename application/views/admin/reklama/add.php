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
		<label class="control-label">Текст ссылки:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'title')?>" class="input-block-level" name="title" id="title"  />
			<span class="help-inline"><?=Arr::get($errors, 'title')?></span>
		</div>
	</div>

	<div class="control-group <?=Arr::get($errors, 'priority') ? 'error' : ''?>">
		<label class="control-label">Приоритет:</label>
		<div class="controls">
			<input type="number" value="<?=Arr::get($_POST, 'priority', @$ad_element->priority)?>" class="input-block-level" name="priority" id="priority"  />
			<span class="help-inline"><?=Arr::get($errors, 'priority')?></span>
		</div>
	</div>

	<div class="control-group <?=Arr::get($errors, 'link') ? 'error' : ''?>">
		<label class="control-label">Ссылка:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'link')?>" class="input-block-level" name="link" id="title"  />
			<span class="help-inline"><?=Arr::get($errors, 'link')?></span>
		</div>
	</div>
		
	<div class="control-group <?=Arr::get($errors, 'class') ? 'error' : ''?>">
		<label class="control-label">Цвет ссылки:</label>
		<div class="controls">
			<?=Form::select('class', array('black' => 'Черный', 'red' => 'Красный', 'green' => 'Зеленый', 'white' => 'Белый'), Arr::get($_POST, 'class')) ?>
		</div>	
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'class') ? 'error' : ''?>">
		<label class="control-label">Города:</label>
		<div class="controls">
			<?=Form::select('cities[]', $cities, Arr::get($_POST, 'cities'), array('multiple', 'size' => 15)) ?>
		</div>	
	</div>	

	<div class="control-group <?=Arr::get($errors, 'class') ? 'error' : ''?>">
		<label class="control-label">Группы:</label>
		<div class="controls">
			<?=Form::select('reklama_group[]', $reklama_group, Arr::get($_POST, 'reklama_group'), array('multiple', 'size' => 5)) ?>
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
	
	<div class="control-group <?=Arr::get($errors, 'class') ? 'error' : ''?>">
		<label class="control-label">Тип ссылки:</label>
		<div class="controls">
			<?=Form::select('type', array(1 => 'Текст(1)', 2 => 'Баннер(2)', 3 => 'Текст/графика(3)'), Arr::get($_POST, 'type')) ?>
		</div>	
	</div>	
	
	<div class="control-group only2" >		
		<label class="control-label">Активна:</label>
		<div class="controls">			
			<input type="checkbox" class="input-small" placeholder="Показывать" name="active" <?php if (Arr::get($_POST, 'active')) echo 'checked' ?> >
		</div>		
	</div>	
	
	<div class="control-group only2" >		
		<label class="control-label">Комментарии:</label>
		<div class="controls">
			<textarea name="comments"></textarea>
		</div>		
	</div>	

	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Сохранить</button>
			<!--<button type="reset"  class="btn">Reset</button>-->
		</div>
	</div>
</form>