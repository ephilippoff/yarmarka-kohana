<label for="userfile">Фото</label>	
<input name="userfile[]" type="file" />		

<? foreach($files as $file): ?>
	<img class="img <? if ($file["id"] == $main_image_id) echo "active";?>" src="<?=$file['filepath']?>"/>
<? endforeach; ?>

<? foreach($files as $file): ?>
	<input type="hidden" name="userfile[]" value="<?=$file['filename']?>"/>
<? endforeach; ?>	
