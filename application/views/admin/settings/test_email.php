
<script>
	$(document).ready(function() {
		
		$("#templates").change(function(){
			console.log( $(this).val() );
			$.post('/khbackend/settings/get_test_message/'+$(this).val(), function(json){
				if (json.result) {
					$('#frame').prop("src","/khbackend/sms/email/" + json.result);
					$('#email_id').val(json.result);
					$('#frame').removeClass("hidden");
				} else {
					$('#frame').prop("src","");
					$('#email_id').val('');
					$('#frame').addClass("hidden");
				}
			}, 'json');
		});
		$("#templates").trigger('change')
	});
</script>

<form method="POST">
	<select name="template" id="templates">
		<option value="add_notice">Добавили/изменили объявление</option>
		<option value="block_contact">Заблокировали объявление</option>
		<option value="contact_verification_code">Проверочный код для контакта</option>
		<!-- <option value="fast_register_success">Быстрая регистрация</option> -->
		<option value="forgot_password">Забыли пароль</option>
		<option value="kupon_notify">Покупка купона</option>
		<option value="manage_object">Модерация объявлений</option>
		<option value="object_to_archive">Архивация объявлений</option>
		<option value="payment_success">Подтверждение оплаты заказа</option>
		<!-- <option value="payment_success_apply_notify">Оплачена услуга на сайте!</option> -->
		<option value="register_data">На сайте «Ярмарка-онлайн» для вас автоматически создана учетная запись</option>
		<option value="register_success">Успешно зарегистрировались</option>
	</select><br>
	<textarea name="to" placeholder="To... (delimiter is ,)" cols="100" rows="5"><?php if(isset($_POST['to'])) { ?><?php echo htmlspecialchars($_POST['to']); ?><?php } ?></textarea><br>
	

	<iframe id="frame" src="" frameborder="1" width="850" height="400" class="hidden">
	</iframe><br>

	<input type="hidden" id="email_id" name="email_id" value="">

	<input id="submit" type="submit" value="Отправить" class="btn btn-primary"/>
</form>