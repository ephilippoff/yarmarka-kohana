<?php if ($success) : ?>
	<div class="alert alert-success">Аккаунт создан. ID = <?=$user_id?></div>
<?php endif;?>

<form class="form-horizontal" method="post">
	<div class="control-group <?=Arr::get($errors, 'login') ? 'error' : ''?>">
		<label class="control-label">Логин(email)</label>
		<div class="controls">
			<input type="text" value="" class="input-block-level" name="login" id="login" />
			<span class="help-inline"><?=Arr::get($errors, 'login')?></span>
		</div>
	</div>

	<div class="control-group <?=Arr::get($errors, 'pass') ? 'error' : ''?>">
		<label class="control-label">Пароль</label>
		<div class="controls">
			<input type="password" value="" class="input-block-level" name="pass" id="pass">
			<span class="help-inline"><?=Arr::get($errors, 'pass')?></span>
		</div>
	</div>

	<div class="control-group <?=Arr::get($errors, 'pass2') ? 'error' : ''?>">
		<label class="control-label">Пароль</label>
		<div class="controls">
			<input type="password" value="" class="input-block-level" name="pass2" id="pass2">
			<span class="help-inline"><?=Arr::get($errors, 'pass2')?></span>
		</div>
	</div>	
	
	<div class="control-group <?=Arr::get($errors, 'org_name') ? 'error' : ''?>">
		<label class="control-label">Название организации</label>
		<div class="controls">
			<input type="text" value="" class="input-block-level" name="org_name" id="org_name">
			<span class="help-inline"><?=Arr::get($errors, 'org_name')?></span>
		</div>
	</div>	


	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Save</button>
			<!--<button type="reset"  class="btn">Reset</button>-->
		</div>
	</div>
</form>