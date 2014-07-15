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
			<?=Form::select('class', array('black' => 'Черный', 'red' => 'Красный', 'green' => 'Зеленый', 'white' => 'Белый'), Arr::get($_POST, 'class', @$ad_element->class)) ?>
		</div>	
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'class') ? 'error' : ''?>">
		<label class="control-label">Города:</label>
		<div class="controls">
			<?=Form::select('cities[]', 
							array(1 => 'Тюменская область', 1919 => 'Тюмень', 1947 => 'Нефтеюганск', 1948 => 'Нижневартовск', 1979 => 'Сургут'), 
							Arr::get($_POST, 'cities', Dbhelper::convert_pg_array(@$ad_element->cities)), 
							array('multiple', 'size' => 5)) ?>
		</div>	
	</div>	

	<div class="control-group <?=Arr::get($errors, 'class') ? 'error' : ''?>">
		<label class="control-label">Группы:</label>
		<div class="controls">
			<?=Form::select('reklama_group[]', $reklama_group, Arr::get($_POST, 'reklama_group', dbhelper::convert_pg_array(@$ad_element->groups)), array('multiple', 'size' => 10)) ?>
		</div>	
	</div>		
	
	<div class="control-group only2" >		
		<label class="control-label">Баннер:</label>
		<div class="controls">
				<?php if (is_file(DOCROOT.'uploads/banners/'.@$ad_element->image)) : ?>
						<p><img src="<?='/uploads/banners/'.@$ad_element->image?>" /></p>
						<p><input type="checkbox" name="delete_image"> Удалить загруженный баннер</p>
				<?php endif;?>			
			<input type="file" class="input-small" placeholder="banner" name="image" >

		</div>		
	</div>
	
	<div class="control-group only2" >		
		<label class="control-label">Интервал для показа:</label>
		<div class="controls">
			от
			<input type="text" class="input-small dp" placeholder="от" name="start_date" value="<?=Arr::get($_POST, 'start_date', @$ad_element->start_date)?>">
			до
			<input type="text" class="input-small dp" placeholder="до" name="end_date" value="<?=Arr::get($_POST, 'end_date', @$ad_element->end_date)?>">
		</div>		
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'class') ? 'error' : ''?>">
		<label class="control-label">Тип ссылки:</label>
		<div class="controls">
			<?=Form::select('type', array(1 => 'Текст(1)', 2 => 'Баннер(2)', 3 => 'Текст/графика(3)'), Arr::get($_POST, 'type', @$ad_element->type)) ?>
		</div>	
	</div>		
	
	<div class="control-group only2" >		
		<label class="control-label">Активна:</label>
		<div class="controls">			
			<input type="checkbox" class="input-small" placeholder="Показывать" name="active" <?php if (Arr::get($_POST, 'active', @$ad_element->active)) echo 'checked' ?> >
		</div>		
	</div>	
	
	<div class="control-group only2" >		
		<label class="control-label">Комментарии:</label>
		<div class="controls">
			<textarea name="comments"><?=Arr::get($_POST, 'comments', @$ad_element->comments)?></textarea>
		</div>		
	</div>		

	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Сохранить</button>
			<!--<button type="reset"  class="btn">Reset</button>-->
		</div>
	</div>
</form>