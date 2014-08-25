<? if ($data->edit): ?>
	<?=$data->value?>
	<input type="hidden" name="<?=$name?>" id="<?=$id?>" value="<?=$data->city_id?>" data-title="<?=$data->value?>"/>
<? else: ?>
	<select  name="<?=$name?>" id="<?=$id?>" class=<?=$_class?>>
		<option value>---</option>
		<? foreach($data->city_list as $key=> $item) : ?>
			<optgroup label="<?=$key?>">
			<? foreach($item as $id=>$title) : ?>
			<option value="<?=$id?>" <?if ($id == $data->city_id) { echo "selected"; } ?>><?=$title?></option>
			<? endforeach; ?>
			</optgroup>
		<? endforeach; ?>
	</select>
<? endif; ?>	
