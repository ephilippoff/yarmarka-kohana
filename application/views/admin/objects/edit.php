<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('textarea').tinymce({
			selector: "textarea",
			theme: "modern",
			image_advtab: true,
			width: '100%',
			verify_html : false,
			toolbar_items_size: 'small',
			plugins: ["visualblocks visualchars code fullscreen"],
		});

		$('#edit_form').submit(function(e){
			e.preventDefault();

			$.post('/khbackend/objects/save/<?=$object->id?>', $(this).serialize(), function(json){
				if (json.code == 200) {
					$('.modal-body .alert-error').hide('slow');
					$('#myModal').modal('hide');
					reload_row(<?=$object->id?>);
				} else if (json.errors) {
					$('.modal-body .alert-error').html(json.errors).show('slow');
				}
			}, 'json');


		});
	});
</script>

<form action="" class="form-horizontal" id="edit_form">
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	<h3 id="myModalLabel">Редактирование объявления</h3>
</div>
<div class="modal-body">
		<div class="alert alert-error hide"></div>

		<input type="text" name="title" style="width:100%" value="<?=$object->title?>" required />
		<br /><br />
		<textarea name="user_text" cols="35" rows="8" class="tiny input-xlarge"><?=$object->user_text?></textarea>
</div>
<div class="modal-footer">
	<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
	<input type="submit" class="btn btn-primary" value="Save changes" />
</div>
</form>