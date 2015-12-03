<? foreach($data->files as $file): ?>
	
	<div class="img-b" id="img_<?=$file["id"]?>" style="display:none;">
		<div class="img ">
			<img class="img <? if ($file["active"]) echo "active";?>" src="<?=$file['filepath']?>" data-original="<?= $file['original']; ?>"/>
		</div>
		<div class="href-bl"><span href="" class="remove fn-remove span-link">Удалить</span></div>
		<input type="hidden" name="userfile[]" value="<?=$file['filename']?>"/>
	</div>

<? endforeach; ?>