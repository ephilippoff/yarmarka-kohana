<div class="row mb10" id="div_<?= $name ?>">
	<div class="col-md-3 col-xs-4 labelcont">
		<label><?= $title ?></label>
	</div>
	<div class="col-md-9 col-xs-8">
		<div class="inp-cont <? if ($errors) echo "error"; ?>">
			<? if ($is_required): ?>
				<span class="required-star">*</span>
			<? endif; ?>
			<select class="<?= $class ?> w100p" name="<?= $name ?>[]" id="<?= $id ?>" multiple style="height:<?= ((count($values) + 1) * 15) ?>px;">
				<? if (!$is_required): ?>
					<option value="">--нет--</option>
				<? endif; ?>
				<? foreach ($values as $key => $item): ?>
					<option value='<?= $key ?>' 

							<? if ($value AND is_array($value) AND in_array($key, $value)) {
								echo "selected";
							} elseif ($value == $key) {
								echo "selected";
							}
							?> 

							><?= $item ?></option>
				<? endforeach; ?>	
			</select>
			<? if ($errors): ?>
				<span class="inform fn-error">
					<?= $errors ?>
				</span>
			<? endif; ?>
			<span class="inform">
				Используйте Ctrl чтобы выбрать несколько значений
			</span>					
		</div>
	</div>
</div>