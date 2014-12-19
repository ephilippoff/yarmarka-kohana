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
<a href="/khbackend/users/moderation">Модерация</a> | 
<a href="/khbackend/users/moderation?filter=all">Все</a>
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
		<td><a href="/khbackend/users/user_info/<?=$user->id?>" target="_blank"><?=$user->email?></a></td>
		<td><?=$user->org_name?></td>
		<td><?=$user->org_full_name?></td>
		<td><?=$user->org_inn?></td>
		<td>
			<?
				$inn_skan = Imageci::getSitePaths($user->org_inn_skan);
			?>
			<img src='<?=$inn_skan["original"]?>' width="400"/>
		</td>
		<td> 
			<? if ($moderate_enable): ?>
				<p><button onclick="orginfo_moderate(<?=$user->id?>, 'ok');" class="btn btn-success"><span class="text">ОК</span></button></p>
			<? endif;?>
				<p><a href="/khbackend/users/orginfoinn_declineform/<?=$user->id?>" class="btn btn-warning " data-toggle="modal" data-target="#myModal"><span class="text">Отменить</span></a></p>
			
		</td>
	</tr>
	<? endforeach;?>
</table>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>