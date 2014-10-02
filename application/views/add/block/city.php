<? if ($data->edit): ?>
	<?=$data->value?>
	<input type="hidden" name="<?=$name?>" id="city_id" value="<?=$data->city_id?>" data-title="<?=$data->value?>" data-lat="<?=$data->lat?>" data-lon="<?=$data->lon?>"/>
<? else: ?>
	<select  name="<?=$name?>" id="city_id" class=<?=$_class?>>
		<option value>---</option>
		<? foreach($data->city_list as $key=> $item) : ?>
			<optgroup label="<?=$key?>">
			<? foreach($item as $id=>$city) : ?>
			<option value="<?=$id?>" <?if ($id == $data->city_id) { echo "selected"; } ?> lat="<?=$city['lat']?>" lon="<?=$city['lon']?>"><?=$city['title']?></option>
			<? endforeach; ?>
			</optgroup>
		<? endforeach; ?>
	</select>
<? endif; ?>	
