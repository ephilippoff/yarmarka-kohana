<form class="form-horizontal" method="post">
	<div class="control-group <?=Arr::get($errors, 'title') ? 'error' : ''?>">
		<label class="control-label">title:</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'title')?>" class="input-block-level" name="title" id="title"  />
			<span class="help-inline"><?=Arr::get($errors, 'title')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'parent_id') ? 'error' : ''?>">
		<label class="control-label">parent_id:</label>
		<div class="controls">
			<select name="parent_id" class="input-block-level">
				<?php foreach ($categories as $p_category) : ?>
					<option value="<?=$p_category->id?>"><?=$p_category->title?> (<?=$p_category->id?>)(<?=$p_category->parent_id?>)</option>
				<?php endforeach; ?>
			</select>
			<span class="help-inline"><?=Arr::get($errors, 'parent_id')?></span>
		</div>
	</div>	
	
	<div class="control-group">
		<label class="control-label">business types</label>
		<div class="controls">
			<?=Form::select('business_types[]', $business_types, Arr::get($_POST, 'business_types'), 
			array('id' => 'business_types', 'class' => 'input-xxlarge', 'multiple' => 'multiple', 'size' => 25))?>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'is_ready') ? 'error' : ''?>">
		<label class="control-label">is_ready:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'is_ready'))?>" class="input-block-level" name="is_ready" id="is_ready"  />
			<span class="help-inline"><?=Arr::get($errors, 'is_ready')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'weight') ? 'error' : ''?>">
		<label class="control-label">weight:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'weight'))?>" class="input-block-level" name="weight" id="weight"  />
			<span class="help-inline"><?=Arr::get($errors, 'weight')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'template') ? 'error' : ''?>">
		<label class="control-label">template:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'template'))?>" class="input-block-level" name="template" id="template"  />
			<span class="help-inline"><?=Arr::get($errors, 'template')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'use_template') ? 'error' : ''?>">
		<label class="control-label">use_template:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'use_template'))?>" class="input-block-level" name="use_template" id="use_template"  />
			<span class="help-inline"><?=Arr::get($errors, 'use_template')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'is_main') ? 'error' : ''?>">
		<label class="control-label">is_main:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'is_main'))?>" class="input-block-level" name="is_main" id="is_main"  />
			<span class="help-inline"><?=Arr::get($errors, 'is_main')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'main_menu_icon') ? 'error' : ''?>">
		<label class="control-label">is_ready:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'main_menu_icon'))?>" class="input-block-level" name="main_menu_icon" id="main_menu_icon"  />
			<span class="help-inline"><?=Arr::get($errors, 'main_menu_icon')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'sinonim') ? 'error' : ''?>">
		<label class="control-label">sinonim:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'sinonim'))?>" class="input-block-level" name="sinonim" id="sinonim"  />
			<span class="help-inline"><?=Arr::get($errors, 'sinonim')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'seo_name') ? 'error' : ''?>">
		<label class="control-label">seo_name:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'seo_name'))?>" class="input-block-level" name="seo_name" id="seo_name"  />
			<span class="help-inline"><?=Arr::get($errors, 'seo_name')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'description') ? 'error' : ''?>">
		<label class="control-label">description:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'description'))?>" class="input-block-level" name="description" id="description"  />
			<span class="help-inline"><?=Arr::get($errors, 'description')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'max_count_for_user') ? 'error' : ''?>">
		<label class="control-label">max_count_for_user:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'seo_name'))?>" class="input-block-level" name="max_count_for_user" id="max_count_for_user"  />
			<span class="help-inline"><?=Arr::get($errors, 'max_count_for_user')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'max_count_for_contact') ? 'error' : ''?>">
		<label class="control-label">max_count_for_contact:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'max_count_for_contact'))?>" class="input-block-level" name="max_count_for_contact" id="max_count_for_contact"  />
			<span class="help-inline"><?=Arr::get($errors, 'max_count_for_contact')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'is_main_for_seo') ? 'error' : ''?>">
		<label class="control-label">is_main_for_seo:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'is_main_for_seo'))?>" class="input-block-level" name="is_main_for_seo" id="is_main_for_seo"  />
			<span class="help-inline"><?=Arr::get($errors, 'is_main_for_seo')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'title_auto_fill') ? 'error' : ''?>">
		<label class="control-label">title_auto_fill:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'title_auto_fill'))?>" class="input-block-level" name="title_auto_fill" id="title_auto_fill"  />
			<span class="help-inline"><?=Arr::get($errors, 'title_auto_fill')?></span>
		</div>
	</div>

	<div class="control-group <?=Arr::get($errors, 'title_auto_if') ? 'error' : ''?>">
		<label class="control-label">title_auto_if:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'title_auto_if'))?>" class="input-block-level" name="title_auto_if" id="title_auto_if"  />
			<span class="help-inline"><?=Arr::get($errors, 'title_auto_if')?></span>
		</div>
	</div>


	
	<div class="control-group <?=Arr::get($errors, 'text_required') ? 'error' : ''?>">
		<label class="control-label">text_required:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'text_required'))?>" class="input-block-level" name="text_required" id="text_required"  />
			<span class="help-inline"><?=Arr::get($errors, 'text_required')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'nophoto') ? 'error' : ''?>">
		<label class="control-label">nophoto:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'nophoto'))?>" class="input-block-level" name="nophoto" id="nophoto"  />
			<span class="help-inline"><?=Arr::get($errors, 'nophoto')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'novideo') ? 'error' : ''?>">
		<label class="control-label">novideo:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'novideo'))?>" class="input-block-level" name="novideo" id="novideo"  />
			<span class="help-inline"><?=Arr::get($errors, 'novideo')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'main_menu_image') ? 'error' : ''?>">
		<label class="control-label">main_menu_image:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'main_menu_image'))?>" class="input-block-level" name="main_menu_image" id="main_menu_image"  />
			<span class="help-inline"><?=Arr::get($errors, 'main_menu_image')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'submenu_template') ? 'error' : ''?>">
		<label class="control-label">submenu_template:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'submenu_template'))?>" class="input-block-level" name="submenu_template" id="submenu_template"  />
			<span class="help-inline"><?=Arr::get($errors, 'submenu_template')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'text_name') ? 'error' : ''?>">
		<label class="control-label">text_name:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'caption'))?>" class="input-block-level" name="text_name" id="text_name"  />
			<span class="help-inline"><?=Arr::get($errors, 'text_name')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'rule') ? 'error' : ''?>">
		<label class="control-label">rule:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'rule'))?>" class="input-block-level" name="rule" id="rule"  />
			<span class="help-inline"><?=Arr::get($errors, 'rule')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'caption') ? 'error' : ''?>">
		<label class="control-label">caption:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'caption'))?>" class="input-block-level" name="caption" id="caption"  />
			<span class="help-inline"><?=Arr::get($errors, 'caption')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'show_map') ? 'error' : ''?>">
		<label class="control-label">show_map:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'show_map'))?>" class="input-block-level" name="show_map" id="show_map"  />
			<span class="help-inline"><?=Arr::get($errors, 'show_map')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'address_required') ? 'error' : ''?>">
		<label class="control-label">address_required:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'address_required'))?>" class="input-block-level" name="address_required" id="address_required"  />
			<span class="help-inline"><?=Arr::get($errors, 'address_required')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'plan_name') ? 'error' : ''?>">
		<label class="control-label">plan_name:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'plan_name'))?>" class="input-block-level" name="plan_name" id="plan_name"  />
			<span class="help-inline"><?=Arr::get($errors, 'plan_name')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'through_weight') ? 'error' : ''?>">
		<label class="control-label">through_weight:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'through_weight'))?>" class="input-block-level" name="through_weight" id="through_weight"  />
			<span class="help-inline"><?=Arr::get($errors, 'through_weight')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'url') ? 'error' : ''?>">
		<label class="control-label">url:</label>
		<div class="controls">
			<input type="text" value="<?=(Arr::get($_POST, 'url'))?>" class="input-block-level" name="url" id="url"  />
			<span class="help-inline"><?=Arr::get($errors, 'url')?></span>
		</div>
	</div>
	
	
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Сохранить</button>
		</div>
	</div>
</form>