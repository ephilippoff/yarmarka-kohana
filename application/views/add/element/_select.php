<select class="<?=$class?>" name="<?=$name?>">
	<option>--<?=$title?>--</option>
	<? foreach($values as $key=>$item): ?>
			<option value='<?=$key?>' <? if ($value == $key) { echo "selected";}?> ><?=$item?></option>
	<? endforeach; ?>	
</select>