<? foreach($data->files as $file): ?>
	
	<div class="img-b" id="img_<?=$file["id"]?>">
		<div class="img ">
			<img class="img <? if ($file["active"]) echo "active";?>" src="<?=$file['filepath']?>"/>
		</div>
		<div class="href-bl"><span href="" class="remove fn-remove">Удалить</span></div>
		<input type="hidden" name="userfile[]" value="<?=$file['filename']?>"/>
	</div>
<? endforeach; ?>