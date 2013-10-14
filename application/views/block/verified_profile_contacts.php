<?php $contact_classes = array(
	1 => 'tel',
	2 => 'tel',
	3 => 'skype',
	4 => 'icq',
	5 => 'email',
) ?>

<?php foreach ($user_contacts as $contact) : ?>
	<div class="input contact style2">	
		<label><span><i class="name"><?=$contact->name?>:</i></span></label>		                    			
		<div class="inp-cont-bl ">
			<div class="inp-cont">
				<span class="cont-info">
				<span class="cont"><span class="ico <?=$contact_classes[$contact->contact_type_id]?>"></span>
					<a class="usercontact contact-input" data-id="<?=$contact->id?>"><?=$contact->contact?></a>
				</span>
					<span class="remove delete_contact" data-id="<?=$contact->id?>"></span>
					<?php if ($contact->contact_type_id == Model_Contact_Type::EMAIL AND $user->email != $contact->contact_clear) : ?>
						<span class="ico main_email" data-id="<?=$contact->id?>" title="сделать основным" style="cursor:pointer"></span>
					<?php endif ?>
				</span>
				</div>
		</div>
	</div>
<?php endforeach; ?>
