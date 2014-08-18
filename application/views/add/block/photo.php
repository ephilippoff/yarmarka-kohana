
<? foreach($data->files as $file): ?>
	
	<div class="img-b">
		<div class="img ">
			<img class="img <? if ($file["id"] == $data->main_image_id) echo "active";?>" src="<?=$file['filepath']?>"/>
		</div>
		<div class="href-bl"><span href="" class="remove">Удалить</span></div>
	</div>
<? endforeach; ?>

<? foreach($data->files as $file): ?>
	<input type="hidden" name="userfile[]" value="<?=$file['filename']?>"/>
<? endforeach; ?>	
