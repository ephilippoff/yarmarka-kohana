<label for="city_id">Город</label>
<? if ($edit): ?>
	<?=$value?>
<? else: ?>
	<select name="city_id">
		<option value>---</option>
		<? foreach($city_list as $item) : ?>
			<option value="<?=$item->id?>" <?if ($item->id == $city_id) { echo "selected"; } ?>><?=$item->title?></option>
		<?php endforeach; ?>
		<option>Другой город...</option>
	</select>
<? endif; ?>	
