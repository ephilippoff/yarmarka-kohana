<script type="text/javascript">
	//$(document).ready(function() {

		function orginfo_moderate(user_id, method, message)
		{
			$.post( "/ajax/admin/orginfo_moderate", {user_id:user_id, method:method, message:message}, function( data ) {
			  	console.log(data);	
			  	$(".user"+user_id).hide();
			},"json");
		}

	//});
</script>
<table class="table table-hover table-condensed promo">
	<tr>
		<th>Date</th>
		<th>Email</th>
		<th>Название</th>
		<th>Юр. название</th>
		<th>ИНН</th>
		<th>Скан</th>
		<th></th>
	</tr>
	<? foreach ($users as $user): ?>
	<tr class="user<?=$user->id?>">
		<td><?=$user->moderate_on?></td>
		<td><a><?=$user->email?></a></td>
		<td><?=$user->org_name?></td>
		<td><?=$user->org_full_name?></td>
		<td><?=$user->org_inn?></td>
		<td><img src='<?=$user->org_inn_skan?>' width="400"/></td>
		<td>
			<p><button onclick="orginfo_moderate(<?=$user->id?>, 'ok');" class="btn btn-success"><span class="text">ОК</span></button></p>
			<p><button onclick="orginfo_moderate(<?=$user->id?>, 'cancel');" class="btn btn-warning "><span class="text">Отменить</span></button></p>
		</td>
	</tr>
	<? endforeach;?>
</table>