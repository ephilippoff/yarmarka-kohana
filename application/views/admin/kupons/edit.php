<form class="form-horizontal" method="post" enctype="multipart/form-data">
	
	<div class="control-group <?=Arr::get($errors, 'status') ? 'error' : ''?>">
		<label class="control-label">Статус:</label>
		<div class="controls">
			<?=Form::select('status', array('1' => 'Непогашен', '2' => 'Погашен', '3' => 'Возврат'), Arr::get($_POST, 'status', @$ad_element->status)) ?>
		</div>	
	</div>	

	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Сохранить</button>
			<!--<button type="reset"  class="btn">Reset</button>-->
		</div>
	</div>
</form>