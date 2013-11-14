<?php $contact_classes = array(
	1 => 'tel',
	2 => 'tel',
	3 => 'skype',
	4 => 'icq',
	5 => 'email',
) ?>
<?php foreach ($user_contacts as $contact) : ?>
	<div class="input contact style2">	
		<label><span><i class="name"><?=$contact->name?> :</i></span></label>		                    			
		<div class="inp-cont-bl ">
			<div class="inp-cont">
				<span class="cont-info">
					<span class="cont">
						<span class="ico <?=$contact_classes[$contact->contact_type_id]?>"></span>
						<a class="usercontact contact-input" data-id="<?=$contact->id?>"><?=$contact->get_contact_value()?></a>
					</span>
					<span class="cont">	
						<span class="like_link remove delete_verified_contact" data-id="<?=$contact->id?>">Удалить</span><br>
						<?php if ($contact->contact_type_id == Model_Contact_Type::EMAIL AND $user->email != $contact->contact_clear) : ?>
							<span class="like_link main_email" data-id="<?=$contact->id?>" style="cursor:pointer">Сделать основным</span><br>
						<?php endif ?>
						<?php if (FALSE AND ! ($contact->contact_type_id === Model_Contact_Type::PHONE AND $contact->moderate == 0) ) : ?>
						<span class="like_link link_objects" data-id="<?=$contact->id?>">Привязать объявления к учетке</span>
						<?php endif ?>
					</span>	
				</span>
				</div>
		</div>
	</div>
<?php endforeach; ?>
