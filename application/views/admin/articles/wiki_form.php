<form class="form-horizontal" method="post" enctype="multipart/form-data">
	<div class="control-group <?=Arr::get($errors, 'url') ? 'error' : ''?>">
		<label class="control-label">Url</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'url', @$article->url)?>" class="input-block-level" name="url" id="url"  />
			<span class="help-inline"><?=Arr::get($errors, 'url')?></span>
		</div>
	</div>

	<div class="control-group <?=Arr::get($errors, 'city') ? 'error' : ''?>">
		<label class="control-label">City</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'city', @$article->city)?>" class="input-block-level" name="city" id="city">
			<span class="help-inline"><?=Arr::get($errors, 'city')?></span>		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Save</button>
			<!--<button type="reset"  class="btn">Reset</button>-->
		</div>
	</div>
</form>
