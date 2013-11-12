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
					<span class="cont">
						<span class="ico <?=$contact_classes[$contact->contact_type_id]?>"></span>
						<a class="usercontact contact-input" data-id="<?=$contact->id?>"><?=$contact->get_contact_value()?></a>
					</span>
					<span class="cont">		
						<span class="like_link remove delete_contact" data-id="<?=$contact->id?>">Удалить</span>
						<br />
						<?php if (in_array($contact->contact_type_id, Model_Contact_Type::get_verifiyng_types())) : ?>
						<span class="like_link fn-contact-verify-button" data-id="<?=$contact->id?>">Верифицировать</span>
						<?php endif ?>
					</span>	
				</span>
			</div>

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

<script>
jQuery(document).ready(function($) {
	$('.fn-contact-verify-button').click(function(){
		var contactWindow = new verifyContactWindow({contact_type : 1, contact_value : '+7-917-714-5656'});
	});
});
</script>

<script type="text/template" id="verify-contact-window">
<div class="popup enter-popup fn-verify-contact-win" style="display: block;"><!-- TODO add class  fn-verify-contact-win-->
	<div class="popup-cont">
		<div class="close fn-verify-contact-win-close"></div> <!-- TODO add class  fn-verify-contact-win-close-->
		<header>
			<h2>Проверка контакта <%=contact_value%></h2>
		</header>
		<div class="cont">
			<div class="left-bl">
				<div class="input inputcode">
					<div class="inp-cont-bl ">
						<div class="inp-cont">
							<div class="inp">
								<input class="placeholder fn-input-code" name="inp1" type="text" placeholder="Введите код">
							</div>
						</div>
						<div class="alert-bl big fn-error-block"  style="display: none;"><!-- TODO add class fn-error-block -->
    						<div class="cont">
    							<div class="img"></div><div class="arr"></div>
    							<p class="text"><span class="fn-error-block-text"></span><span><div class='fn-btn-re-send'>Отправить еще раз</div></span></p> <!-- TODO add class  fn-error-block-text fn-btn-re-send-->
    						</div>
    					</div>
					</div>
				</div>
				<div>
				<div>
				<div class="btn-bl">
					<div class="yarmarka">«Ярмарка онлайн»</div>
					<div class="btn-red btn-subscribe fn-verify-contact-win-submit"><span>Продолжить</span></div><!-- TODO add class  fn-verify-contact-win-submit-->
				</div>
			</div>
			<div class="right-bl">
				&nbsp;
			</div>
		</div>
	</div>
</div>
</script>