<div class="form-cont">
<script type="text/javascript">
	$(document).ready(function(){
		$('.fn_delete_verified_contact').live('click', function(){
			if (confirm('Объявления с этим контактом будут сняты с публикации. Хотите удалить контакт?')) {
				var obj = this;
				$.post('/ajax/delete_user_contact', {contact_id:$(this).data('id')}, function(json){
					if (json.code == 200) {
						$(obj).parents('.contact').remove();
					}
				}, 'json');
			}
		});		
	})
</script>
<div class="row mb15 mt15">
	<div class="col-md-3 col-xs-4 labelcont">
		<label></label>
	</div>
	<div class="col-md-9 col-xs-8">
		В этом разделе Вы можете увидеть все контакты, привязанные к вашему аккаунту. Также здесь можно удалять контакты. В этом случае происходит их отвязка от вашего аккаунта. Кроме того, все объявления, в которых были указаны удаленные контакты, снимаются с публикации. Удаленный контакт можно заново привязать к своему аккаунту на странице подачи объявления.
	</div>
</div>

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
	
			<div class="col-md-3 col-xs-4 labelcont">
				<label><?=$contact->name?></label>
			</div>
			<div class="col-md-9 col-xs-8">
				<div class="inp-cont">
						<a class="usercontact contact-input" data-id="<?=$contact->id?>"><?=$contact->get_contact_value()?></a>
						<span class="span-link ml10 fn_delete_verified_contact" data-id="<?=$contact->id?>">Удалить</span>
				</div>
			</div>

									
		<?php endforeach; ?>
	<?php endif;?>
</div>
</div>