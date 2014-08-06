<form class="form-horizontal" method="post" enctype="multipart/form-data">
	<div class="control-group <?=Arr::get($errors, 'user_id') ? 'error' : ''?>">
		<label class="control-label">ID пользователя</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'user_id')?>" class="input-block-level" name="user_id" id="user_id"  />
			<span class="help-inline"><?=Arr::get($errors, 'user_id')?></span>
		</div>
	</div>

	<div class="control-group <?=Arr::get($errors, 'name') ? 'error' : ''?>">
		<label class="control-label">Имя настройки</label>
		<div class="controls">
			<input type="text" value="massload" class="input-block-level" name="name" id="name"/>
			<span class="help-inline"><?=Arr::get($errors, 'name')?></span>
		</div>
	</div>

	<div class="control-group only1 articles-rubrics-box">
		<label class="control-label">Категория:</label>
		<div class="controls">
			<input type="text" class="input-block-level" name="value" id="value"/>
			<?//Form::select('value', Array("--Выберите категорию--")+$categories, Arr::get($_POST, 'category'), array('style' => 'width:500px;')) ?>
		</div>
	</div>


	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Сохранить</button>
			<!--<button type="reset"  class="btn">Reset</button>-->
		</div>
	</div>
	
</form>

<table class="table table-hover table-condensed promo">
	<tr>
		<th>Id</th>
		<th>Id User</th>
		<th>Email</th>
		<th>Name</th>
		<th>Value</th>
		<th></th>
	</tr>
	<?php foreach ($user_settings as $item) : ?>
		<?
			$category = Kohana::$config->load('massload/bycategory.'.$item->value);
			if ($category)
				$value = $category["name"];
			else 
				$value = $item->value;
		?>
		<tr>			
			<td><?=$item->id?></td>
			<td><?=$item->user_id?></td>
			<td><?=$item->user->email?></td>
			<td><?=$item->name?></td>
			<td><?=$value?></td>	
			<td>
				<a href="<?=Url::site('khbackend/users/delete_settings/'.$item->id)?>" class="icon-trash delete_article"></a>
			</td>		
		</tr>
	<?php endforeach; ?>
</table>