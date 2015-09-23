<div class="form-cont">
<script type="text/javascript">

		function delete_verified_contact(id, context) {
			if (confirm('Объявления с этим контактом будут сняты с публикации. Хотите удалить контакт?')) {
				var obj = this;
				$.post('/ajax/delete_user_contact', {contact_id: id}, function(json){
					if (json.code == 200) {
						$(".contact_"+id).remove();
					}
				}, 'json');
			}
		}

</script>

<div class="row mb15 mt15">
	<?php if (!count($user_contacts)) : ?>
			<div class="col-md-3 col-xs-4 labelcont">
				<label></label>
			</div>
			<div class="col-md-9 col-xs-8">
				<b>У Вас нет привязанных контактов</b>
			</div>	
	<?php else : ?>
		<?php foreach ($user_contacts as $contact) : ?>	
	
			<div class="col-md-3 col-xs-4 labelcont contact_<?=$contact->id?>">
				<label><?=$contact->name?></label>
			</div>
			<div class="col-md-9 col-xs-8 contact_<?=$contact->id?>">
				<div class="inp-cont">
						<a class="usercontact contact-input"><?=$contact->get_contact_value()?></a>
						<span class="span-link ml10 fn_delete_verified_contact" onclick="delete_verified_contact(<?=$contact->id?>, this)">Удалить</span>
				</div>
			</div>

									
		<?php endforeach; ?>
	<?php endif;?>
</div>
</div>