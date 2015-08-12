<? $i = 0; ?>
<?	foreach ($data->contacts as $contact):	?>
	<div id="contact_<?=$i?>" class="contact-cont fn-contact <? if (!$contact["verified"]) echo "noverified"; ?> <?if ($contact["verified"]) echo "verified";?>" id="contact_item_<?=$i?>" data-item-id="<?=$i?>">
		<div class="cont-left">
			<select id="contact_type_select_<?=$i?>"  name="contact_<?=$i?>_type" class="sl-contact-type fn-contact-type">
				<? foreach ($data->contact_types as $contact_type):?>
					<option value='<?=$contact_type->id?>' data-validation-type="<?=$contact_type->validation_type?>" data-format="<?=$contact_type->format?>" <?php if ($contact["type"]==$contact_type->id){echo 'selected';}?>><?=$contact_type->name?></option>
				<? endforeach; ?>
			</select>
			<input id="contact_value_input_<?=$i?>" class='inp_contact fn-contact-value' type="text" value="<?=$contact["value"]?>" name="contact_<?=$i?>_value"/>
			<span class="inform">
				<span class="fn-contact-inform">
					<?if ($contact["verified"]): ?>
						Контакт подтвержден
					<?elseif (!$contact["verified"]): ?>
						Контакт не подтвержден
					<?endif; ?>
				</span>
			</span>
		</div>
		<div class="cont-right">
			<span title="Верифицировать" class="button button-style1 bg-color-blue apply fn-contact-verify-button">Подтвердить контакт</span>
			<span class="cansel span-link fn-contact-delete-button" title="Удалить">Удалить</span>
		</div><!--contact-right-->			
	</div><!--contact-cont-->
<? $i++; ?>
<? //if ($i == $data->max_count_contacts) break;?>
<? endforeach; ?>