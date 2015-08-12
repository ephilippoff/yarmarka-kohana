<select class="<?=$class?> w100p" name="<?=$name?>" id="<?=$id?>">
	<option value="">--<?=$title?>--</option>
	<? foreach($values as $key=>$item): ?>
			<option value='<?=$key?>' 

			<? if ($value AND is_array($value) AND in_array($key, $value)) { echo "selected";} 
										elseif ($value == $key) { echo "selected";} ?>

			><?=$item?></option>
	<? endforeach; ?>	
</select>