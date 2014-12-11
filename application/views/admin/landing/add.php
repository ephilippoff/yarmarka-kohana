<form class="form-horizontal" method="post" enctype="multipart/form-data">

	<div class="control-group only2 <?=Arr::get($errors, 'domain') ? 'error' : ''?>" >		
		<label class="control-label">Домен:</label>
		<div class="controls">			
			<input type="text" class="input-medium dp" placeholder="Домен" name="domain" value="<?=Arr::get($_POST, 'domain')?>">
			<span class="help-inline"><?=Arr::get($errors, 'domain')?></span>
		</div>		
	</div>	
			
	<div class="control-group only2 <?=Arr::get($errors, 'object_id') ? 'error' : ''?>" >		
		<label class="control-label">ID объявления:</label>
		<div class="controls">			
			<input type="text" class="input-medium dp" placeholder="ID объявления" name="object_id" value="<?=Arr::get($_POST, 'object_id')?>">
			<span class="help-inline"><?=Arr::get($errors, 'object_id')?></span>
		</div>		
	</div>	
		
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Сохранить</button>
			<!--<button type="reset"  class="btn">Reset</button>-->
		</div>
	</div>

</form>