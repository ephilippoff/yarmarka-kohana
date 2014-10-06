<? if ($data->edit): ?>
	<?=$data->city_title?>
	<input type="hidden" name="<?=$name?>" id="city_id" value="<?=$data->city_id?>" data-title="<?=$data->city_title?>" data-lat="<?=$data->lat?>" data-lon="<?=$data->lon?>"/>
<? else: ?>
	<select  name="<?=$name?>" id="city_id" class="<?=$_class?>" data-lat="<?=$data->lat?>" data-lon="<?=$data->lon?>">
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
