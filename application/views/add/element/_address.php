<div class="row mb10" id="div_<?= $name ?>">
	<div class="col-md-3 col-xs-4 labelcont">
		<label><?= $title ?></label>
	</div>
	<div class="col-md-9 col-xs-8">
		<div class="inp-cont <? if ($errors) echo "error"; ?>">
			<? if ($is_required): ?>
				<span class="required-star">*</span>
			<? endif; ?>
			<input class="w100p" id="<?= $id ?>" type="text" name="<?= $name ?>" value="<?= $value ?>" autocomplete="off"/>
			<? if ($errors): ?>
				<span class="inform fn-error">
					<?= $errors ?>
				</span>
			<? endif; ?>
			<span class="inform">
				Например: ул. Мельникайте, д. 44, корп. 2
			</span>					
		</div>
	</div>
</div>