<div id="contacts" data-max="<?=$max_count_contacts?>">
	<div class="add_form_info">
		<div class="text">Контактное лицо&nbsp;<span class="red">*</span></div>
		<div class="values">
			<input type="text" name="contact" value="<?=$contact_person?>" />
			<div id="error_contact" class="error" style="display:none;color: red;"></div>
		</div>
	</div>
</div>
<div id="contacts2">
	<? $i = 0; ?>
	<?	foreach ($contacts as $contact):	?>
	
	<div class="add_form_info contact_type_select_info" id="contact_item_<?=$i?>" data-item-id="<?=$i?>">
		<div class="text">
			<select data-placeholder="Выберите тип контакта..." id="contact_type_select_<?=$i?>"  name="contact_<?=$i?>_type">
				<? foreach ($contact_types as $contact_type)
				{?>
				<option value='<?=$contact_type->id?>' data-validation-type="<?=$contact_type->validation_type?>" data-format="<?=$contact_type->format?>" <?php if ($contact["type"]==$contact_type->id){echo 'selected';}?>><?=$contact_type->name?></option>
				<?
				}?>
			</select>
		</div>
		<div class="values with_right">
			<input id="contact_value_input_<?=$i?>" class='inputs_contact' type="text" value="<?=$contact["value"]?>" name="contact_<?=$i?>_value"/>
			<span onclick="DeleteContact(<?=$i?>)" title="Удалить"  class='like_link'>Удалить</span>
		</div>
	</div>
	<? $i++; ?>
	<? if ($i == $max_count_contacts) break;?>
	<? endforeach; ?>
	<? if ($i<$max_count_contacts) :?>
	<div class="add_form_info contact_type_select_info" id="contact_item_<?=$i?>" data-item-id="0">
		<div class="text">
			<select data-placeholder="Выберите тип контакта..." id="contact_type_select_<?=$i?>" name="contact_<?=$i?>_type">
				<? foreach ($contact_types as $contact_type)
				{?>
				<option value='<?=$contact_type->id?>' data-comment="<?=$contact_type->comment?>" data-validation-type="<?=$contact_type->validation_type?>" data-format="<?=$contact_type->format?>"><?=$contact_type->name?></option>
				<?
				}?>
			</select>
		</div>
		<div class="values with_right">
			<input id="contact_value_input_<?=$i?>" class='inputs_contact' type="text" value="" name="contact_<?=$i?>_value"/>
			<span onclick="DeleteContact(<?=$i?>)" title="Удалить"  class='like_link'>Удалить</span>
		</div>
	</div>
	<? endif; ?>
</div>
<div class="add_form_info" id='add_form_informer' count-load-contacts='<?=$i?>'>
	<div class="text">&nbsp; </div>
	<div class="values" id="miniform_show_button"  <? if($i >= $max_count_contacts) echo 'style="display:none"'; ?>>
		<span onclick="AddFieldContact()" title="Добавить контакт"  class='like_link'>Добавить еще телефон или email</span>
		
	</div>
</div>