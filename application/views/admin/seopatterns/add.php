<form class="form-horizontal" method="post" enctype="multipart/form-data">
	<div class="control-group <?=Arr::get($errors, 'category_id') ? 'error' : ''?>">
		<label class="control-label">category_id:</label>
		<div class="controls">
			<select name="category_id">
				<?php foreach ($categories as $category) : ?>
					<option value="<?=$category->id?>"><?=$category->title?> (<?=$category->id?>)(<?=$category->parent_id?>)</option>
				<?php endforeach; ?>
			</select>
			<span class="help-inline"><?=Arr::get($errors, 'category_id')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'params') ? 'error' : ''?>">
		<label class="control-label">params:</label>
		<div class="controls">
			<input type="text" value="<?=htmlspecialchars(Arr::get($_POST, 'params'))?>" class="input-block-level" name="params" id="params"  />
			<span class="help-inline"><?=Arr::get($errors, 'params')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'h1') ? 'error' : ''?>">
		<label class="control-label">h1:</label>
		<div class="controls">
			<input type="text" value="<?=htmlspecialchars(Arr::get($_POST, 'h1'))?>" class="input-block-level" name="h1" id="h1"  />
			<span class="help-inline"><?=Arr::get($errors, 'h1')?></span>
		</div>
	</div>
	
	<div class="control-group <?=Arr::get($errors, 'title') ? 'error' : ''?>">
		<label class="control-label">title:</label>
		<div class="controls">
			<input type="text" value="<?=htmlspecialchars(Arr::get($_POST, 'title'))?>" class="input-block-level" name="title" id="title"  />
			<span class="help-inline"><?=Arr::get($errors, 'title')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'description') ? 'error' : ''?>">
		<label class="control-label">description:</label>
		<div class="controls">
			<input type="text" value="<?=htmlspecialchars(Arr::get($_POST, 'description'))?>" class="input-block-level" name="description" id="description"  />
			<span class="help-inline"><?=Arr::get($errors, 'description')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'footer') ? 'error' : ''?>">
		<label class="control-label">footer:</label>
		<div class="controls">
			<input type="text" value="<?=htmlspecialchars(Arr::get($_POST, 'footer'))?>" class="input-block-level" name="footer" id="footer"  />
			<span class="help-inline"><?=Arr::get($errors, 'footer')?></span>
		</div>
	</div>		
	
	<div class="control-group <?=Arr::get($errors, 'keywords') ? 'error' : ''?>">
		<label class="control-label">keywords:</label>
		<div class="controls">
			<input type="text" value="<?=htmlspecialchars(Arr::get($_POST, 'keywords'))?>" class="input-block-level" name="keywords" id="keywords"  />
			<span class="help-inline"><?=Arr::get($errors, 'keywords')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'anchor') ? 'error' : ''?>">
		<label class="control-label">anchor:</label>
		<div class="controls">
			<input type="text" value="<?=htmlspecialchars(Arr::get($_POST, 'anchor'))?>" class="input-block-level" name="anchor" id="anchor"  />
			<span class="help-inline"><?=Arr::get($errors, 'anchor')?></span>
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
				<a href="/khbackend/seopatterns/index" >Вернуться в список</a>
			</p>
		</div>
	</div	
</form>