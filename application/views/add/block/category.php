<input type="hidden" name="default_action" id="default_action" value="<?=$data->default_action?>">
<? if ($data->edit): ?>
	<?=$data->value?>
	<input type="hidden" name="<?=$name?>" id="<?=$id?>" value="<?=$data->category_id?>"/>
<? else: ?>		
	<select name="<?=$name?>" id="<?=$id?>" class=<?=$_class?>>
		<option value>---</option>
		<? foreach($data->category_list as $key=> $item) : ?>
			<optgroup label="<?=$key?>">
			<? foreach($item as $id=>$title) : ?>
			<option value="<?=$id?>" <?if ($id == $data->category_id) { echo "selected"; } ?>><?=$title?></option>
			<? endforeach; ?>
			</optgroup>
		<? endforeach; ?>
	</select>
<? endif; ?>
