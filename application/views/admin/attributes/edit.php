<form class="form-horizontal" method="post" enctype="multipart/form-data">
	<div class="control-group <?=Arr::get($errors, 'title') ? 'error' : ''?>">
		<label class="control-label">title:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'title', @$item->title)?>" class="input-block-level" name="title" id="title"  />
			<span class="help-inline"><?=Arr::get($errors, 'title')?></span>
		</div>
	</div>

	<div class="control-group <?=Arr::get($errors, 'solid_size') ? 'error' : ''?>">
		<label class="control-label">solid_size:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'solid_size', @$item->solid_size)?>" class="input-block-level" name="solid_size" id="solid_size"  />
			<span class="help-inline"><?=Arr::get($errors, 'solid_size')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'frac_size') ? 'error' : ''?>">
		<label class="control-label">frac_size:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'frac_size', @$item->frac_size)?>" class="input-block-level" name="frac_size" id="frac_size"  />
			<span class="help-inline"><?=Arr::get($errors, 'frac_size')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'prefix') ? 'error' : ''?>">
		<label class="control-label">prefix:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'prefix', @$item->prefix)?>" class="input-block-level" name="prefix" id="prefix"  />
			<span class="help-inline"><?=Arr::get($errors, 'prefix')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'unit') ? 'error' : ''?>">
		<label class="control-label">unit:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'unit', @$item->unit)?>" class="input-block-level" name="unit" id="unit"  />
			<span class="help-inline"><?=Arr::get($errors, 'unit')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'max_text_length') ? 'error' : ''?>">
		<label class="control-label">max_text_length:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'max_text_length', @$item->max_text_length)?>" class="input-block-level" name="max_text_length" id="max_text_length"  />
			<span class="help-inline"><?=Arr::get($errors, 'max_text_length')?></span>
		</div>
	</div>		
	
	<div class="control-group <?=Arr::get($errors, 'is_textarea') ? 'error' : ''?>">
		<label class="control-label">is_textarea:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'is_textarea', @$item->is_textarea)?>" class="input-block-level" name="is_textarea" id="is_textarea"  />
			<span class="help-inline"><?=Arr::get($errors, 'is_textarea')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'type') ? 'error' : ''?>">
		<label class="control-label">type:</label>
		<div class="controls">
			<?=Form::select('type', array('text' => 'text', 'integer' => 'integer', 'numeric' => 'numeric', 'boolean' => 'boolean', 'list' => 'list'), @$item->type) ?>
			<span class="help-inline"><?=Arr::get($errors, 'type')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'comment') ? 'error' : ''?>">
		<label class="control-label">comment:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'comment', @$item->comment)?>" class="input-block-level" name="comment" id="comment"  />
			<span class="help-inline"><?=Arr::get($errors, 'comment')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'is_prefix') ? 'error' : ''?>">
		<label class="control-label">is_prefix:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'is_prefix', @$item->is_prefix)?>" class="input-block-level" name="is_prefix" id="is_prefix"  />
			<span class="help-inline"><?=Arr::get($errors, 'is_prefix')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'is_unit') ? 'error' : ''?>">
		<label class="control-label">is_unit:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'is_unit', @$item->is_unit)?>" class="input-block-level" name="is_unit" id="is_unit"  />
			<span class="help-inline"><?=Arr::get($errors, 'is_unit')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'parent') ? 'error' : ''?>">
		<label class="control-label">parent:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'parent', @$item->parent)?>" class="input-block-level" name="parent" id="parent"  />
			<span class="help-inline"><?=Arr::get($errors, 'parent')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'is_price') ? 'error' : ''?>">
		<label class="control-label">is_price:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'is_price', @$item->is_price)?>" class="input-block-level" name="is_price" id="is_price"  />
			<span class="help-inline"><?=Arr::get($errors, 'is_price')?></span>
		</div>
	</div>		
	
	<div class="control-group <?=Arr::get($errors, 'is_descr') ? 'error' : ''?>">
		<label class="control-label">is_descr:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'is_descr', @$item->is_descr)?>" class="input-block-level" name="is_descr" id="is_descr"  />
			<span class="help-inline"><?=Arr::get($errors, 'is_descr')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'id_tr') ? 'error' : ''?>">
		<label class="control-label">is_descr:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'id_tr', @$item->id_tr)?>" class="input-block-level" name="id_tr" id="id_tr"  />
			<span class="help-inline"><?=Arr::get($errors, 'id_tr')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'seo_name') ? 'error' : ''?>">
		<label class="control-label">seo_name:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'seo_name', @$item->seo_name)?>" class="input-block-level" name="seo_name" id="seo_name"  />
			<span class="help-inline"><?=Arr::get($errors, 'seo_name')?></span>
		</div>
	</div>	
	

	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Сохранить</button>
			<!--<button type="reset"  class="btn">Reset</button>-->
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label"></label>
		<div class="controls">
			<a href="/khbackend/attributes/index" >Вернуться в список</a>
		</div>
	</div>	
</form>
