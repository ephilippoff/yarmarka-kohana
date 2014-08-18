<? if ($data->edit): ?>
	<?=$data->value?>
<? else: ?>
	<input type="text" maxlength="75"  name="<?=$name?>" id="<?=$id?>" class="<?=$_class?>" value="<?=$data->value?>"/>	
<? endif; ?>			
