<div class="fl100">	
	<div class="smallcont fn-additional" id="additional_org_name" style="<?=($settings->additional_org_name ? '': 'display:none')?>">
		<div class="labelcont">
			<label><span>Название компании</span></label>
		</div>
		<div class="fieldscont">				 						
			<div class="inp-cont-long">
				<div class="inp-cont <? if ($errors->additional_org_name) echo "error";?>">
					<span class="required-label">*</span>
					<?=Form::input("additional_org_name", $values->additional_org_name)?>
					<? if ($errors->additional_org_name): ?>
						<span class="inform error">
							<span><?=$errors->additional_org_name?></span>
						</span>
					<? endif; ?>
					<span class="inform">
						<span>Некорректные названия, такие как "ИП", "ООО", "Михаил" не пройдут модерацию</span>
					</span>
				</div> <!--inp-cont -->
			</div> <!-- inp-cont-short -->
		</div> <!-- fieldscont -->		
	</div>	 <!-- smallcont -->
	<div class="smallcont fn-additional" id="additional_vakancy_org_type" style="<?=($settings->additional_vakancy_org_type ? '': 'display:none')?>">
		<div class="labelcont">
			<label><span>Тип компании</span></label>
		</div>
		<div class="fieldscont">				 						
			<div class="inp-cont-long">
				<div class="inp-cont <? if ($errors->additional_vakancy_org_type) echo "error";?>">
					<span class="required-label">*</span>
					<?=Form::select("additional_vakancy_org_type" , $vakancy_org_type, $values->additional_vakancy_org_type)?>
					<? if ($errors->additional_vakancy_org_type): ?>
						<span class="inform error">
							<span><?=$errors->additional_vakancy_org_type?></span>
						</span>
					<? endif; ?>
				</div> <!--inp-cont -->
			</div> <!-- inp-cont-short -->
		</div> <!-- fieldscont -->		
	</div>	 <!-- smallcont --> 
	<div class="smallcont fn-additional" id="additional_commoninfo" style="<?=($settings->additional_commoninfo ? '': 'display:none')?>">
		<div class="labelcont">
			<label><span>О компании</span></label>
		</div>
		<div class="fieldscont">				 						
			<div class="inp-cont-long">
				<div class="inp-cont <? if ($errors->additional_commoninfo) echo "error";?>">
					<span class="required-label">*</span>
					<?=Form::textarea("additional_commoninfo", $values->additional_commoninfo)?>
					<? if ($errors->additional_commoninfo): ?>
						<span class="inform error">
							<span><?=$errors->additional_commoninfo?></span>
						</span>
					<? endif; ?>
					<span class="inform">
						<span>Краткая информация о деятельности компании. Например: сколько лет на рынке, сколько сотрудников, федеральная/региональная и др.</span>
					</span>
				</div> <!--inp-cont -->
			</div> <!-- inp-cont-short -->
		</div> <!-- fieldscont -->		
	</div>	 <!-- smallcont --> 
</div>  <!-- fl100 -->