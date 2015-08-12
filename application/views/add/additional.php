<div class="row mb10 fn-additional" id="additional_org_name" style="<?= ($settings->additional_org_name ? '' : 'display:none') ?>">
	<div class="col-md-3 col-xs-4 labelcont">
		<label>Название компании</label>
	</div>
	<div class="col-md-9 col-xs-8">
		<div class="inp-cont <? if ($errors->additional_org_name) echo "error"; ?>">
			<span class="required-star">*</span>																		
			<?= Form::input("additional_org_name", $values->additional_org_name, array('class' => 'w100p')) ?>
			<? if ($errors->additional_org_name): ?>
				<span class="inform error">
					<?= $errors->additional_org_name ?>
				</span>
			<? endif; ?>
			<span class="inform">
				Некорректные названия, такие как "ИП", "ООО", "Михаил" не пройдут модерацию
			</span>					
		</div>
	</div>
</div>

<div class="row mb10 fn-additional" id="additional_vakancy_org_type" style="<?= ($settings->additional_vakancy_org_type ? '' : 'display:none') ?>">
	<div class="col-md-3 col-xs-4 labelcont">
		<label>Тип компании</label>
	</div>
	<div class="col-md-9 col-xs-8">
		<div class="inp-cont <? if ($errors->additional_vakancy_org_type) echo "error"; ?>">
			<span class="required-star">*</span>																		
			<?= Form::select("additional_vakancy_org_type", $vakancy_org_type, $values->additional_vakancy_org_type, array('class' => 'w100p')) ?>
			<? if ($errors->additional_vakancy_org_type): ?>
				<span class="inform error">
					<?= $errors->additional_vakancy_org_type ?>
				</span>
			<? endif; ?>					
		</div>
	</div>
</div>

<div class="row mb10 fn-additional" id="additional_commoninfo" style="<?= ($settings->additional_commoninfo ? '' : 'display:none') ?>">
	<div class="col-md-3 col-xs-4 labelcont">
		<label>О компании</label>
	</div>
	<div class="col-md-9 col-xs-8">
		<div class="inp-cont <? if ($errors->additional_commoninfo) echo "error"; ?>">
			<span class="required-star">*</span>																		
			<?= Form::textarea("additional_commoninfo", $values->additional_commoninfo, array('class' => 'w100p')) ?>
			<? if ($errors->additional_commoninfo): ?>
				<span class="inform error">
					<?= $errors->additional_commoninfo ?>
				</span>
			<? endif; ?>
			<span class="inform">
				Краткая информация о деятельности компании. Например: сколько лет на рынке, сколько сотрудников, федеральная/региональная и др.
			</span>					
		</div>
	</div>
</div>