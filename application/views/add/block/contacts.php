
<? $i = 0; ?>
<?	foreach ($data->contacts as $contact):	?>

	<div class="contact-cont">
		<div class="cont-left">
			<select id="contact_type_select_<?=$i?>"  name="contact_<?=$i?>_type" class="sl-contact-type fn-contact-type">
				<? foreach ($data->contact_types as $contact_type):?>
					<option value='<?=$contact_type->id?>' data-validation-type="<?=$contact_type->validation_type?>" data-format="<?=$contact_type->format?>" <?php if ($contact["type"]==$contact_type->id){echo 'selected';}?>><?=$contact_type->name?></option>
				<? endforeach; ?>
			</select>
			<input id="contact_value_input_<?=$i?>" class='inp_contact fn-contact-value' type="text" value="<?=$contact["value"]?>" name="contact_<?=$i?>_value"/>
			<span class="inform"><span class="fn-contact-inform"><?/* inform*/?></span></span>
		</div>
		<div class="cont-right">
			<span title="Верифицировать" class="button apply fn-contact-verify-button">Подтвердить контакт</span>
			<span class="cansel like-link fn-contact-delete-button" title="Удалить">Удалить</span>
		</div><!--contact-right-->

		<div id="error_contacts" class="alert-bl fn-alert-overlay hidden">
			<div class="cont"><? /* error message*/?></div>
			<div class="arr"></div>
		</div>						
	</div><!--contact-cont-->
<? $i++; ?>
<? if ($i == $data->max_count_contacts) break;?>
<? endforeach; ?>

<? if ($i<$data->max_count_contacts) :?>
	<div id="contacts2" class="contacts-cont fn-contacts-container">			
		<div class="contact-cont">
			<div class="cont-left">
				<select id="contact_type_select_1"  name="contact_1_type" class="sl-contact-type fn-contact-type">
					<option value='1'>Мобильный тел.</option>
					<option value='2'>Городской тел.</option>
					<option value='5'>Email</option>
				</select>
				<input id="contact_value_input_1" class="inp_contact fn-contact-value" type="text" value="" name="contact_1_value" />
				<span class="inform"><span class="fn-contact-inform">Пр прпвап ваппвап вапвапвпвап вапвапвапвар вапвапв</span></span>
			</div>
			<div class="cont-right">
				<span title="Верифицировать" class="button apply fn-contact-verify-button">Подтвердить контакт</span>
				<span class="cansel like-link fn-contact-delete-button" title="Удалить">Удалить</span>
			</div><!--contact-right-->

			<div id="error_contacts" class="alert-bl fn-alert-overlay hidden">
				<div class="cont"><? /* error message*/?></div>
				<div class="arr"></div>
			</div>						
		</div><!--contact-cont-->
	</div>
<? endif; ?>