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
					<a class="usercontact contact-input" data-id="<?=$contact->id?>"><?=$contact->get_contact_value()?></a>
				</span><span class="remove delete_contact" data-id="<?=$contact->id?>"></span></span></div>

				<?php if ($contact->is_phone() AND ! $contact->is_phone_unique()) : ?>
					<div class="alert-bl contact-error" style="display:block">
						<div class="cont">
							<div class="img"></div>
							<div class="arr"></div>
							<p class="text"><span>Такой контакт уже есть у другого пользователя</span></p>
							<a href="">валидация номера</a>
						</div>
					</div>
				<?php endif ?>
		</div>
	</div>
<?php endforeach; ?>
