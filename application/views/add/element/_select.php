<select class="<?=$class?>" name="<?=$name?>" id="<?=$id?>">
	<option value="">--<?=$title?>--</option>
	<? foreach($values as $key=>$item): ?>
			<option value='<?=$key?>' <? if ($value == $key) { echo "selected";}?> ><?=$item?></option>
	<? endforeach; ?>	
</select>