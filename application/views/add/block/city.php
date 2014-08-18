<? if ($data->edit): ?>
	<?=$data->value?>
<? else: ?>
	<select  name="<?=$name?>" id="<?=$id?>" class=<?=$_class?>>
		<option value>---</option>
		<? foreach($data->city_list as $item) : ?>
			<option value="<?=$item->id?>" <?if ($item->id == $data->city_id) { echo "selected"; } ?>><?=$item->title?></option>
		<?php endforeach; ?>
		<option>Другой город...</option>
	</select>
<? endif; ?>	
