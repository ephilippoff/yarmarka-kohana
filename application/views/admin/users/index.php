<style type="text/css" media="screen">
	.container {
		width: 1280px;
	}
</style>


<?=HTML::script('bootstrap/datepicker/js/bootstrap-datepicker.js')?>
<?=HTML::style('bootstrap/datepicker/css/datepicker.css')?>

<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
	// enable datepicker
	$('.dp').datepicker({
		format:	'yyyy-mm-dd'
	}).on('changeDate', function(){
		$(this).datepicker('hide');
	});

	// enable tooltips
	$('a').tooltip();
});
function ban(obj) {
	var reason = prompt('Ban reason:');
	if (reason != null) {
		$.post($(obj).attr('href'), {reason:reason}, function(json){
			if (json.is_blocked) {
				$(obj).parents('tr').addClass('error');
			}
		}, 'json');
	}

	return false;
}
function delete_user(obj) {
	if (confirm('Delete user?')) {
		$.post($(obj).attr('href'), {}, function(json){
			if (json.code == 200) {
				$(obj).parents('tr').remove();
			}
		}, 'json');
	}
	return false;
}
</script>

<form class="form-inline">
	<div class="input-prepend">
		<span class="add-on"><i class="icon-envelope"></i></span>
		<input class="span2" id="prependedInput" type="text" placeholder="User email" name="email" value="<?=Arr::get($_GET, 'email')?>">
    </div>
	<div class="input-prepend">
		<span class="add-on">Tel</span>
		<input class="span2" id="prependedInput" type="text" placeholder="User phone" name="phone" value="<?=Arr::get($_GET, 'phone')?>">
    </div>
	<div class="input-prepend">
		<span class="add-on">Regdate</i></span>
		<input type="text" class="input-small dp" placeholder="date from" name="regdate[from]" value="<?=Arr::get(@$_GET['regdate'], 'from', date('Y-m-d'))?>">
		<input type="text" class="input-small dp" placeholder="date to" name="regdate[to]" value="<?=Arr::get(@$_GET['regdate'], 'to')?>">
	</div>
	<?=Form::select('role', array('' => '--select role--')+$roles, Arr::get($_GET, 'role'), array('class' => 'span2'))?>
	<label class="checkbox">
		<?=Form::checkbox('has_invoices', 1, (bool) Arr::get($_GET, 'has_invoices'))?>Has paid invoices
	</label>
	<input type="submit" name="" value="Filter" class="btn btn-primary">
	<input type="reset" name="" value="Clear" class="btn">
</form>

<table class="table table-hover table-condensed" style="font-size:85%;">
	<tr>
		<th>#</th>
		<th>Email</th>
		<th>Phone</th>
		<th>City</th>
		<th>Name</th>

		<?php if ($direction == 'asc') : ?>
		<th class="dropup">
		<?php else : ?>
		<th class="dropdown">
		<?php endif; ?>
			<?php if ($direction == 'asc') : ?>
			<a href="#" onClick="return order('regdate', 'desc');">
			<?php else : ?>
			<a href="#" onClick="return order('regdate', 'asc');">
			<?php endif; ?>
			Registration date
			<?php if ($sort_by == 'regdate') : ?>
			<span class="caret"></span>
			<?php endif; ?>
			</a>
		</th>

		<th>ip</th>

		<?php if ($direction == 'asc') : ?>
		<th class="dropup">
		<?php else : ?>
		<th class="dropdown">
		<?php endif; ?>
			<?php if ($direction == 'asc') : ?>
			<a href="#" onClick="return order('objects_cnt', 'desc');">
			<?php else : ?>
			<a href="#" onClick="return order('objects_cnt', 'asc');">
			<?php endif; ?>
			Ads
			<?php if ($sort_by == 'objects_cnt') : ?>
			<span class="caret"></span>
			<?php endif; ?>
			</a>
		</th>

		<?php if ($direction == 'asc') : ?>
		<th class="dropup">
		<?php else : ?>
		<th class="dropdown">
		<?php endif; ?>
			<?php if ($direction == 'asc') : ?>
			<a href="#" onClick="return order('invoices_cnt', 'desc');">
			<?php else : ?>
			<a href="#" onClick="return order('invoices_cnt', 'asc');">
			<?php endif; ?>
			Invoices
			<?php if ($sort_by == 'invoices_cnt') : ?>
			<span class="caret"></span>
			<?php endif; ?>
			</a>
		</th>

		<?php if ($direction == 'asc') : ?>
		<th class="dropup">
		<?php else : ?>
		<th class="dropdown">
		<?php endif; ?>
			<?php if ($direction == 'asc') : ?>
			<a href="#" onClick="return order('msgs_cnt', 'desc');">
			<?php else : ?>
			<a href="#" onClick="return order('msgs_cnt', 'asc');">
			<?php endif; ?>
			Msgs
			<?php if ($sort_by == 'msgs_cnt') : ?>
			<span class="caret"></span>
			<?php endif; ?>
			</a>
		</th>

		<th></th>
	</tr>
	<?php foreach ($users as $user) : ?>
	<?php if ($user->is_blocked) : ?>
	<tr class="error">
	<?php else : ?>
	<tr>
	<?php endif; ?>
		<td><a href="<?=Url::site('khbackend/users/user_info/'.$user->id)?>" onClick="return popup(this)"><?=$user->id?></a></td>
		<td><?=$user->email?></td>
		<td><?=$user->phone?></td>
		<td><?=$user->city?></td>
		<td><?=$user->fullname?></td>
		<td><?=date('d.m.Y H:i', strtotime($user->regdate))?></td>
		<td><a href="<?=URL::site('khbackend/users/ip_info/'.$user->ip_addr)?>" onClick="return popup(this);"><?=$user->ip_addr?></a></td>
		<td><span class="badge"><?=$user->objects_cnt?></span></td>
		<td><span class="badge"><?=$user->invoices_cnt?></span></td>
		<td><span class="badge"><?=$user->msgs_cnt?></span></td>
		<td>
			<a href="<?=URL::site('khbackend/users/ban/'.$user->id)?>" title="Ban user" class="icon-lock" onClick="return ban(this);"></a>
			<a href="<?=URL::site('khbackend/users/ban_and_unpublish/'.$user->id)?>" onClick="return ban(this);" title="Ban user and unpublish all ads" class="icon-ban-circle"></a>
			<a href="<?=URL::site('khbackend/users/delete/'.$user->id)?>" title="Delete user" onClick="return delete_user(this);" class="icon-trash"></a>
		</td>
	</tr>
	<?php endforeach; ?>
</table>

<?=$pagination?>
