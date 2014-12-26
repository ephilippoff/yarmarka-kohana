<div class="smallcont" id="div_<?=$name?>">
			<div class="labelcont">
				<label><span><?=$title?></span></label>
			</div>
			<div class="fieldscont">
				<div class="inp-cont-long">
					<div class="inp-cont <? if ($errors) echo "error";?>">
						<? if ($is_required):?>
							<span class="required-label">*</span>
						<? endif; ?>
						<input id="<?=$id?>" type="text" name="<?=$name?>" value="<?=$value?>" autocomplete="off"/>
						<? if ($errors): ?>
							<span class="inform fn-error">
								<span><?=$errors?></span>
							</span>
						<? endif; ?>
						<span class="inform">
							<span>Например: ул. Мельникайте, д. 44, корп. 2</span>
						</span>
					</div>
			</div>
		</div>									
</div>