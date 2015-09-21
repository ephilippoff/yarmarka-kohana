<form class="form-horizontal" method="post" enctype="multipart/form-data">
	<div class="control-group <?=Arr::get($errors, 'full_text') ? 'error' : ''?>">
		<label class="control-label">full_text:</label>
		<div class="controls">
			<textarea class="input-block-level" name="full_text" id="full_text" cols="30" rows="10"><?=Arr::get($_POST, 'full_text')?></textarea>
			<span class="help-inline"><?=Arr::get($errors, 'full_text')?></span>
		</div>
	</div>

	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Сохранить</button>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label"></label>
		<div class="controls">
			<p>
				<a href="/khbackend/object_reasons/index" >Вернуться в список</a>
			</p>
		</div>
	</div>	
</form>