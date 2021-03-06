<form class="form-horizontal" method="post">
	
	<div class="control-group <?=Arr::get($errors, 'title') ? 'error' : ''?>">
		<label class="control-label">Название:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'title')?>" class="input-block-level" name="title" id="title"  />
			<span class="help-inline"><?=Arr::get($errors, 'title')?></span>
		</div>
	</div>

	<div class="control-group <?=Arr::get($errors, 'url') ? 'error' : ''?>">
		<label class="control-label">URL:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'url')?>" class="input-block-level" name="url" id="url"  />
			<span class="help-inline"><?=Arr::get($errors, 'url')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'parent_id') ? 'error' : ''?>">
		<label class="control-label">Родитель:</label>
		<div class="controls">
			<select name="parent_id" class="input-block-level">
				<option value="0">--</option>
				<?php foreach ($categories as $p_category) : ?>
					<option value="<?=$p_category->id?>" <?php if ($parent_id == $p_category->id):?>selected<?php endif;?> ><?=$p_category->title?> (<?=$p_category->id?>)(<?=$p_category->parent_id?>)</option>
				<?php endforeach; ?>
			</select>
			<span class="help-inline"><?=Arr::get($errors, 'parent_id')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'weight') ? 'error' : ''?>">
		<label class="control-label">Вес:</label>
		<div class="controls">
			<input type="number" value="<?=(Arr::get($_POST, 'weight', 0))?>" class="input-block-level" name="weight" id="weight"  />
			<span class="help-inline"><?=Arr::get($errors, 'weight')?></span>
		</div>
	</div>
	
	
	<div class="control-group <?=Arr::get($errors, 'description') ? 'error' : ''?>">
		<label class="control-label">Описание:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'description'))?>" class="input-block-level" name="description" id="description"  />
			<span class="help-inline"><?=Arr::get($errors, 'description')?></span>
		</div>
	</div>
	
	
	<div class="control-group">
		<label class="control-label">Показывать только админу:</label>
		<div class="controls">
			<input type="checkbox" value="1" class="input-block-level" name="for_admin" id="for_admin"  />
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'main_menu_image') ? 'error' : ''?>">
		<label class="control-label">Картинка:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'main_menu_image'))?>" class="input-block-level" name="main_menu_image" id="main_menu_image"  />
			<span class="help-inline"><?=Arr::get($errors, 'main_menu_image')?></span>
		</div>
	</div>
	
	
	
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Сохранить</button>
		</div>
	</div>
</form>