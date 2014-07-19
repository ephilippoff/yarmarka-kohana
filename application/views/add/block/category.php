<label for="rubricid">Раздел</label>	
<? if ($edit): ?>
	<?=$value?>
<? else: ?>		
	<select name="rubricid">
		<option value>---</option>
		<? foreach($category_list as $item) : ?>
			<option value="<?=$item->id?>" <?if ($item->id == $category_id) { echo "selected"; } ?>><?=$item->title?></option>
		<?php endforeach; ?>
	</select>
<? endif; ?>
