<style type="text/css" media="screen">
	.container {
		width: 1280px;
	}
</style>
<table class="table table-hover table-condensed">
	<tr>
		<th>#</th>
		<th>Email</th>
		<th>Phone</th>
		<th>City</th>
		<th>Name</th>
		<th>Registration date</th>
		<th>ip</th>
		<th>Objects</th>
		<th>Invoices</th>
		<th>Messages</th>
		<th>action</th>
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
		<td><?=$user->regdate?></td>
		<td><?=$user->ip_addr?></td>
		<td>0</td>
		<td>0</td>
		<td>0</td>
		<td>
			<a href="<?=URL::site('khbackend/users/delete/'.$user->id)?>" title="Ban" class="icon-ban-circle"></a>
			<a href="<?=URL::site('khbackend/users/delete/'.$user->id)?>" title="Delete" onClick="return confirm('Delete?');" class="icon-trash"></a>
		</td>
	</tr>
	<?php endforeach; ?>
</table>

<?=$pagination?>
