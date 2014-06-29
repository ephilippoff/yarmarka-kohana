<div id="city" class="add_form_info">
		<div class="text"><b>Город</b></div>
	   <div class="values">
		   <select name="city_id">
				<option>---</option>
				<? foreach($city_list as $item) : ?>
					<option value="<?=$item->id?>" <?if ($item->id == $city_id) { echo "selected"; } ?>><?=$item->title?></option>
				<?php endforeach; ?>
				<option>Другой город...</option>
			</select>
	   </div>
</div>
