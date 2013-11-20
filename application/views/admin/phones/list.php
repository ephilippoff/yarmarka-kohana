<style type="text/css" media="screen">
	.container {
		width: 98%;
	}
</style>

<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
	// enable tooltips
	$('a').tooltip();

	$(document.body).on('click', '.moderate', function(){
		var obj = this;
		var contact_id = $(obj).data('id');
		var row = $(obj).parents('td.buttons');
		var check = $(obj).data('confirm') ? confirm($(obj).data('confirm')) : true;

		if (check) {
			row.html('<a class="btn">Loading...</a>');
			$.getJSON($(this).attr('href'), function(json){
				if (json.code == 200) {
					row.load('/khbackend/phones/buttons/'+contact_id);
				}
			});
		}

		return false;
	});
});
</script>

<form class="form-inline">
	<div class="input-prepend">
		<span class="add-on"><i class="icon-search"></i></span>
		<input class="span2" id="prependedInput" type="text" placeholder="Phone" name="phone" value="<?=Arr::get($_GET, 'phone')?>">
    </div>
	<?=Form::select('status', array('' => '--select status--')+$statuses, Arr::get($_GET, 'status'), array('class' => 'span2'))?>
	<input type="submit" name="" value="Filter" class="btn btn-primary">
	<input type="reset" name="" value="Clear" class="btn">
</form>

<table class="table table-hover table-condensed" style="font-size:85%;" id="objects">
	<tr>
		<th>#</th>
		<th>Contact</th>
		<th>User</th>
		<th></th>
	</tr>
	<?php foreach ($contacts as $contact) : ?>
	<tr>
		<td><?=$contact->id?></td>
		<td><?=Text::format_phone($contact->contact)?></td>
		<td>
			<?php if ($contact->verified_user->loaded()) : ?>
				<a href="<?=URL::site('khbackend/users/user_info/'.$contact->verified_user->id)?>" onClick="return popup(this);">
					<?=$contact->verified_user->get_user_name()?>
				</a>
			<?php endif ?>
		</td>
		<td class="buttons">
			<?=View::factory('admin/phones/buttons', array('contact' => $contact))?>
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