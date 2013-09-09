<style type="text/css" media="screen">
	.container {
		width: 98%;
	}
</style>

<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
	// enable tooltips
	$('a').tooltip();

	$('.moderate').on('click', function(){
		var obj = this;
		$.post($(this).attr('href'), {verified:$(this).data('verified')}, function(json){
			if (json.code == 200) {
				console.log(obj);
				console.log($(obj).parents('tr'));
				$(obj).parents('tr').remove();
			}
		}, 'json');

		return false;
	});
});
</script>

<table class="table table-hover table-condensed" style="font-size:85%;" id="objects">
	<tr>
		<th>#</th>
		<th>Contact</th>
		<th>Contact clear</th>
		<th>Object</th>
		<th>User</th>
		<th></th>
	</tr>
	<?php foreach ($contacts as $contact) : ?>
	<tr>
		<td><?=$contact->id?></td>
		<td><?=$contact->contact?></td>
		<td><?=$contact->contact_clear?></td>
		<td>
			<?php if ($contact->object->loaded()) : ?>
				<?=$contact->object->title?>
			<?php endif ?>
		</td>
		<td>
			<?php if ($contact->user->loaded()) : ?>
				<?=$contact->user->get_user_name()?>
			<?php endif ?>
		</td>
		<td>
			<a href="<?=URL::site('khbackend/phones/moderate/'.$contact->id)?>" data-verified="1" class="moderate btn btn-success">Прошел модерацию</a>
			<a href="<?=URL::site('khbackend/phones/moderate/'.$contact->id)?>" data-verified="0" class="moderate btn btn-danger">Не прошел</a>
		</td>
	</tr>
	<?php endforeach; ?>
</table>

<div class="row">
	<div class="span10"><?=$pagination?></div>
</div>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>