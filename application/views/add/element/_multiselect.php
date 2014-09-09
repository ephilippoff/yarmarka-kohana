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
						<select class="<?=$class?>" name="<?=$name?>[]" id="<?=$id?>" multiple style="height:<?=((count($values)+1)*15)?>px;">
							<? if (!$is_required):?>
								<option value="">--нет--</option>
							<? endif; ?>
							<? foreach($values as $key=>$item): ?>
									<option value='<?=$key?>' 

									<? if ($value AND is_array($value) AND in_array($key, $value)) { echo "selected";} 
										elseif ($value == $key) { echo "selected";} ?> 

									><?=$item?></option>
							<? endforeach; ?>	
						</select>
						<? if ($errors): ?>
								<span class="inform fn-error">
									<span><?=$errors?></span>
								</span>
						<? endif; ?>
						<span class="inform">
							<span>Используйте Ctrl чтобы выбрать несколько значений</span>
						</span>
					</div>
			</div>
		</div>									
</div>