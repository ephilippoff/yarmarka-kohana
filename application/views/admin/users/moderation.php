<script type="text/javascript">
	//$(document).ready(function() {

		function orginfo_moderate(user_id, method, message)
		{
			var estimate = $("#estimate_"+user_id).val();
			$.post( "/ajax/admin/orginfo_moderate", {user_id:user_id, method:method, message:message, estimate:estimate}, function( data ) {
			  	console.log(data);	
			  	$(".user"+user_id).hide();
			},"json");
		}

	//});
</script>
<a href="/khbackend/users/moderation">Модерация</a> | 
<a href="/khbackend/users/moderation?filter=moderated">Прошли модерацию</a> | 
<a href="/khbackend/users/moderation?filter=all">Все</a>
<table class="table table-hover table-condensed promo">
	<tr>
		<th>Date</th>
		<th>Email</th>
		<th>Инфо</th>
		<th>Скан</th>
		<th></th>
	</tr>
	<? foreach ($users as $user): ?>
	<tr class="user<?=$user->id?>">
		<td><?=$user->moderate_on?></td>
		<td><a href="/khbackend/users/user_info/<?=$user->id?>" target="_blank"><?=$user->email?></a></td>
		<td>
			
			<table style="width:400px;">
				<tr>
					<td>Название</td>
					<td><?=$user->org_name?></td>
				</tr>
				<tr>
					<td>Юр. название</td>
					<td><?=$user->org_full_name?></td>
				</tr>
				<tr>
					<td>ИНН</td>
					<td><?=$user->org_inn?></td>
				</tr>
				<tr>
					<td>Адрес</td>
					<td><?=$user->org_post_address?></td>
				</tr>
				<tr>
					<td>Телефон</td>
					<td><?=$user->org_phone?></td>
				</tr>
				<tr>
					<td>Описание</td>
					<td style="width:250px;">
						<div style="height:100px;overflow:hidden;">
							<?=$user->about?>
						</div>
					</td>
				</tr>
				<?
					$logo = NULL;
					if ($user->filename)
						$logo = Imageci::getSitePaths($user->filename);
				?>
				<? if ($logo): ?>
				<tr>
					
					<td>Лого</td>
					<td>					
						<img src='<?=$logo["120x90"]?>'/>
					</td>
				</tr>
				<? endif; ?>
			</table>
		</td>
		<td>
			<?
				$inn_skan = Imageci::getSitePaths($user->org_inn_skan);
			?>
			<img src='<?=$inn_skan["original"]?>' width="400"/>
		</td>
		<td> 
			<select id="estimate_<?=$user->id?>">
				<option>--</option>
				<? foreach ($estimates as  $key => $estimate): ?>

					<option value="<?=$key?>" <? if ($user->estimate == $key) echo "selected";?>><?=$estimate?></option>
				<? endforeach; ?>
			</select>
			<? if ($moderate_enable): ?>
				<p><button onclick="orginfo_moderate(<?=$user->id?>, 'ok');" class="btn btn-success"><span class="text">ОК</span></button></p>
			<? endif;?>
				<p><a href="/khbackend/users/orginfoinn_declineform/<?=$user->id?>" class="btn btn-warning " data-toggle="modal" data-target="#myModal"><span class="text">Отменить</span></a></p>
			
		</td>
	</tr>
	<? endforeach;?>
</table>

<?php if ($pagination->total_pages > 1) : ?>
	<div class="row">
		<div class="span10"><?=$pagination?></div>
	</div>
<?php endif; ?>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>