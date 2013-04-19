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
function order(direction) {
	var get = $.parseParams(window.location.search);
	get['order'] = direction;
	window.location.search = decodeURIComponent($.param(get));
	return false;
}
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
	Reg date
	<input type="text" class="input-small dp" name="regdate[from]" value="<?=Arr::get(@$_GET['regdate'], 'from', date('Y-m-d'))?>">
	<input type="text" class="input-small dp" name="regdate[to]" value="<?=Arr::get(@$_GET['regdate'], 'to')?>">
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
		<?php if (Request::current()->query('order') == 'asc') : ?>
		<th class="dropup">
			<a href="#" onClick="return order('desc');">
			Registration date
			<span class="caret"></span>
			</a>
		</th>
		<?php else : ?>
		<th class="dropdown">
			<a href="#" onClick="return order('asc');">
			Registration date
			<span class="caret"></span>
			</a>
		</th>
		<?php endif; ?>
		<th>ip</th>
		<th>Ads</th>
		<th>Invoices</th>
		<th>Msgs</th>
		<th></th>
	</tr>
	<?php foreach ($users as $user) : ?>
	<?php if ($user->is_blocked) : ?>
	<tr class="error">
	<?php else : ?>
	<tr>
	<?php endif; ?>
		<td><?=$user->id?></td>
		<td><?=$user->email?></td>
		<td><?=$user->phone?></td>
		<td><?=$user->city?></td>
		<td><?=$user->fullname?></td>
		<td><?=date('Y-m-d H:i:s', strtotime($user->regdate))?></td>
		<td><?=$user->ip_addr?></td>
		<td>0</td>
		<td>0</td>
		<td>0</td>
		<td>
			<a href="<?=URL::site('khbackend/users/ban/'.$user->id)?>" title="Ban user" class="icon-lock" onClick="return ban(this);"></a>
			<a href="<?=URL::site('khbackend/users/ban_and_unpublish/'.$user->id)?>" onClick="return ban(this);" title="Ban user and unpublish all ads" class="icon-ban-circle"></a>
			<a href="<?=URL::site('khbackend/users/delete/'.$user->id)?>" title="Delete user" onClick="return delete_user(this);" class="icon-trash"></a>
		</td>
	</tr>
	<?php endforeach; ?>
</table>

<?=$pagination?>
