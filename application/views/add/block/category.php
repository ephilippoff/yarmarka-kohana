<? if ($data->edit): ?>
	<?=$data->value?>
	<input type="hidden" name="<?=$name?>" id="<?=$id?>" value="<?=$data->category_id?>"/>
<? else: ?>		
	<select name="<?=$name?>" id="<?=$id?>" class=<?=$_class?>>
		<option value>---</option>
		<? foreach($data->category_list as $item) : ?>
			<option value="<?=$item->id?>" <?if ($item->id == $data->category_id) { echo "selected"; } ?>><?=$item->title?></option>
		<? endforeach; ?>
	</select>
<? endif; ?>
