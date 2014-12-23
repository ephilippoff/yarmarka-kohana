<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('select[name=reason_id]').change(function(){
			$('textarea[name=reason]').val($(this).find('option:selected').text());
		});

		$('#decline_form').submit(function(e){
			e.preventDefault();

			$.post('/khbackend/users/orginfoinn_decline/<?=$user->id?>', $(this).serialize(), function(json){
				if (json.code == 200) {
					$('.modal-body .alert-error').hide('slow');
					$('#myModal').modal('hide');
					$('.user<?=$user->id?>').hide();
				} else {
					$('.modal-body .alert-error').html('Укажите причину').show('slow');
				}
			}, 'json');

		});
	});
</script>

<form action="" class="form-horizontal" id="decline_form">
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	<h3 id="myModalLabel">
			Отклонить загрузку ИНН
	</h3>
</div>
<div class="modal-body">
		<div class="alert alert-error hide"></div>

		<input type="hidden" name="state" value="<?=$state?>" />

		<div class="control-group">
		<label class="control-label">Причина</label>
			<div class="controls">
				<?=Form::select('reason_id', array('' => '-- выберите причину --')+$reasons, NULL, array('class' => 'input-xlarge'))?>
			</div>
		</div>

		<div class="control-group">
		<label class="control-label">Текст причины</label>
			<div class="controls">
				<textarea name="reason" cols="35" rows="8" class="input-xlarge" required></textarea>
			</div>
		</div>

		<div class="control-group">
		<label class="control-label">Отправить письму автору</label>
			<div class="controls">
				<input type="checkbox" name="send_email" value="1" checked />
			</div>
		</div>

		<div class="control-group">
		<label class="control-label">Бан пользователя со снятием объявлений</label>
			<div class="controls">
				<input type="checkbox" name="ban_user" />
			</div>
		</div>
</div>
<div class="modal-footer">
	<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
	<input type="submit" class="btn btn-primary" value="Save changes" />
</div>
</form>
