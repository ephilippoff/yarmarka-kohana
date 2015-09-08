<form class="form-horizontal" method="post" enctype="multipart/form-data">
	<div class="control-group <?=Arr::get($errors, 'category') ? 'error' : ''?>">
		<label class="control-label">category:</label>
		<div class="controls">
			<select name="category">
				<?php foreach ($categories as $category) : ?>
					<?php $selected = ($category->id == $item->category) ? 'selected' : '' ?>
					<option <?=$selected?> value="<?=$category->id?>"><?=$category->title?> (<?=$category->id?>)(<?=$category->parent_id?>)</option>
				<?php endforeach; ?>
			</select>
			<span class="help-inline"><?=Arr::get($errors, 'category')?></span>
		</div>
	</div>

	<div class="control-group <?=Arr::get($errors, 'attribute') ? 'error' : ''?>">
		<label class="control-label">attribute:</label>
		<div class="controls">
			<select name="attribute">
				<?php foreach ($attributes as $attribute) : ?>
					<?php $selected = ($attribute->id == $item->attribute) ? 'selected' : '' ?>
					<option <?=$selected?> value="<?=$attribute->id?>"><?=$attribute->title?> (<?=$attribute->id?>)</option>
				<?php endforeach; ?>
			</select>			
			<span class="help-inline"><?=Arr::get($errors, 'attribute')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'weight') ? 'error' : ''?>">
		<label class="control-label">weight:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'weight', @$item->weight)?>" class="input-block-level" name="weight" id="weight"  />
			<span class="help-inline"><?=Arr::get($errors, 'weight')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'is_required') ? 'error' : ''?>">
		<label class="control-label">is_required:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'is_required', @$item->is_required)?>" class="input-block-level" name="is_required" id="is_required"  />
			<span class="help-inline"><?=Arr::get($errors, 'is_required')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'is_title') ? 'error' : ''?>">
		<label class="control-label">is_title:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'is_title', @$item->is_title)?>" class="input-block-level" name="is_title" id="is_title"  />
			<span class="help-inline"><?=Arr::get($errors, 'is_title')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'is_main') ? 'error' : ''?>">
		<label class="control-label">is_main:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'is_main', @$item->is_main)?>" class="input-block-level" name="is_main" id="is_main"  />
			<span class="help-inline"><?=Arr::get($errors, 'is_main')?></span>
		</div>
	</div>		
	
	<div class="control-group <?=Arr::get($errors, 'attribute_cols_count') ? 'error' : ''?>">
		<label class="control-label">attribute_cols_count:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'attribute_cols_count', @$item->attribute_cols_count)?>" class="input-block-level" name="attribute_cols_count" id="attribute_cols_count"  />
			<span class="help-inline"><?=Arr::get($errors, 'attribute_cols_count')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'is_seo_used') ? 'error' : ''?>">
		<label class="control-label">is_seo_used:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'is_seo_used', @$item->is_seo_used)?>" class="input-block-level" name="is_seo_used" id="is_seo_used"  />
			<span class="help-inline"><?=Arr::get($errors, 'is_seo_used')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'is_selectable') ? 'error' : ''?>">
		<label class="control-label">is_selectable:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'is_selectable', @$item->is_selectable)?>" class="input-block-level" name="is_selectable" id="is_selectable"  />
			<span class="help-inline"><?=Arr::get($errors, 'is_selectable')?></span>
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
			<p>
				<a href="/khbackend/references/index" >Вернуться в список</a>
			</p>
		</div>
	</div	
</form>