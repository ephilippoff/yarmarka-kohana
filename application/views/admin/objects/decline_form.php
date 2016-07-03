<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('select[name=reason_id]').change(function(){
			$('textarea[name=reason]').val($(this).find('option:selected').text());
		});

		$('#decline_form').submit(function(e){
			e.preventDefault();

			var reload_row_func = reload_row || false;

			$.post('/khbackend/objects/decline/<?=$object->id?>', $(this).serialize(), function(json){
				if (json.code == 200) {
					$('.modal-body .alert-error').hide('slow');
					$('#myModal').modal('hide');
					
					if (reload_row_func) {
						reload_row_func(<?=$object->id?>, 1);

					}
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
		<?php if ($is_bad == 1) : ?>
			Блокировать до исправления
		<?php elseif ($is_bad == 2) : ?>
			Блокировать окончательно
		<?php else : ?>
			Удалить объявление
		<?php endif; ?>
	</h3>
</div>
<div class="modal-body">
		<div class="alert alert-error hide"></div>

		<input type="hidden" name="is_bad" value="<?=$is_bad?>" />

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
</div>
<div class="modal-footer">
	<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
	<input type="submit" class="btn btn-primary js-submit" value="Save changes" />
</div>
</form>
