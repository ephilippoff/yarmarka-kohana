<label for="title_adv">Заголовок</label>	
<? if ($edit): ?>
	<?=$value?>
<? else: ?>
	<input type="text" maxlength="75" id="title_adv" name="title_adv" value="<?=$value?>"/>	
<? endif; ?>			
