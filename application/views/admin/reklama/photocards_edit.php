<form class="form-horizontal" method="post" enctype="multipart/form-data">
	
	<div class="control-group ">
		<label class="control-label">Категория:</label>
		<div class="controls">
			<?=Form::select('category_id', $categories, Arr::get($_POST, 'category_id', @$ad_element->category_id)) ?>
		</div>	
	</div>

	<div class="control-group ">
		<label class="control-label">Расположение:</label>
		<div class="controls">
			<?=Form::select('service_options', array(
				"main_page" => "Главная",
				"obj_rubric" => "Рубрика",
				"obj_main_rubric" => "Главная рубрика(посадочная)"
			), Arr::get($_POST, 'service_options', @$ad_element->service_options)) ?>
		</div>	
	</div>

	<div class="control-group only2" >		
		<label class="control-label">Дата завершения:</label>
		<div class="controls">
			<input type="text" class="input dp" placeholder="от" name="date_expiration" value="<?=@$ad_element->date_expiration?>">
		</div>		
	</div>
	
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Сохранить</button>
			<!--<button type="reset"  class="btn">Reset</button>-->
		</div>
	</div>
</form>