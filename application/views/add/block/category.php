<div id="categories" class="add_form_info">
		<div class="text"><b>Раздел</b></div>
	   <div class="values">
	   <select name="rubricid">
			<option>---</option>
			<? foreach($category_list as $item) : ?>
				<option value="<?=$item->id?>" <?if ($item->id == $category_id) { echo "selected"; } ?>><?=$item->title?></option>
			<?php endforeach; ?>
		</select>
	   </div>
</div>

