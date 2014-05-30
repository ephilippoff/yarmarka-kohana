<?=HTML::script('bootstrap/datepicker/js/bootstrap-datepicker.js')?>
<?=HTML::style('bootstrap/datepicker/css/datepicker.css')?>

<script type="text/javascript" charset="utf-8">

</script>

<form class="form-horizontal" method="post" enctype="multipart/form-data">
	<div class="control-group <?=Arr::get($errors, 'title') ? 'error' : ''?>">
		<label class="control-label">Title</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'title', @$ad_element->title)?>" class="input-block-level" name="title" id="title"  />
			<span class="help-inline"><?=Arr::get($errors, 'title')?></span>
		</div>
	</div>

	<div class="control-group <?=Arr::get($errors, 'link') ? 'error' : ''?>">
		<label class="control-label">link</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'link', @$ad_element->link)?>" class="input-block-level" name="link" id="title"  />
			<span class="help-inline"><?=Arr::get($errors, 'link')?></span>
		</div>
	</div>
		
	<div class="control-group <?=Arr::get($errors, 'class') ? 'error' : ''?>">
		<label class="control-label">Link color:</label>
		<div class="controls">
			<?=Form::select('class', array('black' => 'Черный', 'red' => 'Красный', 'green' => 'Зеленый'), Arr::get($_POST, 'class', @$ad_element->class)) ?>
		</div>	
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'class') ? 'error' : ''?>">
		<label class="control-label">Cities:</label>
		<div class="controls">
			<?=Form::select('cities[]', array(1 => 'Тюменская область', 1919 => 'Тюмень', 1947 => 'Нефтеюганск', 1948 => 'Нижневартовск', 1979 => 'Сургут'), null, array('multiple', 'size' => 5)) ?>
		</div>	
	</div>	

	<div class="control-group <?=Arr::get($errors, 'class') ? 'error' : ''?>">
		<label class="control-label">Groups:</label>
		<div class="controls">
			<?=Form::select('reklama_group[]', $reklama_group, null, array('multiple', 'size' => 10)) ?>
		</div>	
	</div>		
	
	<div class="control-group only2" >		
		<label class="control-label">Banner:</label>
		<div class="controls">
			<input type="file" class="input-small" placeholder="banner" name="image" >
		</div>		
	</div>


	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Save</button>
			<!--<button type="reset"  class="btn">Reset</button>-->
		</div>
	</div>
</form>