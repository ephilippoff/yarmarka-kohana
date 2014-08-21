<div class="smallcont" id="div_<?=$name?>">
			<div class="labelcont">
				<label><span><?=$title?></span></label>
			</div>
			<div class="fieldscont">
				<div class="inp-cont">
					<div class="inp-cont-long <? if ($errors) echo "error";?>">
						<? if ($is_required):?>
						<span class="required-label">*</span>
						<? endif; ?>
						<input id="<?=$id?>" type="text" name="<?=$name?>" value="<?=$value?>"/>

						<span class="inform">
							<span>Пример заполнения адреса</span>
						</span>
					</div>
			</div>
		</div>									
</div>