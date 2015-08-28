<!--noindex-->
<div class="popup fn-callback-request-wrp">
	<div class="callback-request-window">
		<div class="header mr35">
			Заказ обратного звонка
			<img src="/images/operator.png">

		</div>
		<div class="cont fn-form-cont">
					
			<div class="group cf mb20">			
				<div class="field-cont">
					<span class="notice-msg">Введите ваши данные для обратной связи:</span>
				</div>
			</div>			
			<div class="group cf">
				<div class="label-cont">
					<span>Ваши Ф.И.О:</span>
				</div>
				<div class="field-cont">
					<input class="fn-fio" name="fio" type="text">
					<span class="required">*</span>
				</div>
			</div>
			<div class="group cf">
				<div class="label-cont">
					<span>Номер телефона:</span>
				</div>
				<div class="field-cont">
					<input class="fn-phone" name="phone" maxlength="15" type="text">
					<span class="required">*</span>
					<p class="notice-msg2">В формате 7(XXX)XXX-XX-XX</p>
				</div>
			</div>
			<div class="group cf">
				<div class="label-cont">
					<span>Пожелания оператору:</span>
				</div>
				<div class="field-cont">
					<textarea name="comment" class="fn-comment"></textarea>
				</div>
				<p class="notice-msg2">Например время, в которое вам удобно принять звонок</p>
			</div>									

			<input class="fn-object-id" name="object_id" type="hidden" value="0">
			<input class="fn-key" name="key" type="hidden" value="<?=$callback_key?>">
			
		<div class="ta-c">
			<button class="fn-request-send btn-request-send">Отправить</button>
		</div>			
			
			<div class="fn-errors-cont errors-cont mt15"></div>
		</div>

		<div class="success-cont fn-success-cont" style="display: none;">
			Спасибо!<br>
			В ближайшее время<br>
			оператор вам перезвонит!
		</div>


		<span class="fn-close close" title="Закрыть"></span>
	</div>
	<!--</div>-->			
</div>

<script type="text/javascript">

	$(document).ready(function() {

		(function(){

				function request_send_handler() {						

					var data = {
						fio:	   window.find('.fn-fio').val(),
						phone:	   window.find('.fn-phone').val(),
						object_id: window.find('.fn-object-id').val(),
						comment:   window.find('.fn-comment').val(),
						key:	   window.find('.fn-key').val()
					}; 
										
					var errors_cont  = window.find('.fn-errors-cont');
					var success_cont = window.find('.fn-success-cont');
					var form_cont    = window.find('.fn-form-cont');
					
					errors_cont.html('');
					
					request_send.unbind('click').addClass('disabled').html('Отправка ...');

					$.post('/ajax/callback_request', data, function(json) {
						
						request_send.bind('click', request_send_handler).removeClass('disabled').html('Отправить');
						
						if (json.code == 500)
						{
							
							for (i = 0; i <= json.errors.length - 1; i++)
							{
								errors_cont.append($('<p>').html(json.errors[i]));
							}
						}
						else
						{
							form_cont.remove();
							success_cont.fadeIn('fast');
						}
					}, 'json');
				}				
		
				var window = $('.fn-callback-request-wrp');
				var request_send = window.find('.fn-request-send');		
				request_send.bind('click', request_send_handler);	
		})();
		
	})
</script>
<!--/noindex-->