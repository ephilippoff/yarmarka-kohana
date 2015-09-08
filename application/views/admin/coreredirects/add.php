<form class="form-horizontal" method="post" enctype="multipart/form-data">
	<div class="control-group <?=Arr::get($errors, 'source') ? 'error' : ''?>">
		<label class="control-label">source:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'source')?>" class="input-block-level" name="source" id="source"  />
			<span class="help-inline"><?=Arr::get($errors, 'source')?></span>
		</div>
	</div>

	<div class="control-group <?=Arr::get($errors, 'destination') ? 'error' : ''?>">
		<label class="control-label">destination:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'destination')?>" class="input-block-level" name="destination" id="destination"  />
			<span class="help-inline"><?=Arr::get($errors, 'destination')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'number') ? 'error' : ''?>">
		<label class="control-label">number:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'number')?>" class="input-block-level" name="number" id="number"  />
			<span class="help-inline"><?=Arr::get($errors, 'number')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'use_white_ip') ? 'error' : ''?>">
		<label class="control-label">use_white_ip:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'use_white_ip')?>" class="input-block-level" name="use_white_ip" id="use_white_ip"  />
			<span class="help-inline"><?=Arr::get($errors, 'use_white_ip')?></span>
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
				<a href="/khbackend/coreredirects/index" >Вернуться в список</a>
			</p>
		</div>
	</div>	
</form>