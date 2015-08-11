<? if ($requests->count()):?>
	<div class="smallcont p10">
		<h2>Привязанные пользователи:</h2>
	</div>
<? endif; ?>
<? foreach ($users as $childuser):?>
	<div class="smallcont">
		<div class="labelcont">
			<label><span><?=$childuser->id?></span></label>
		</div>
		<div class="fieldscont">										
			<div class="">
				<div class="inp-cont">
					<div class="pt4">
						<? if (trim($childuser->fullname)): ?>
							<?=$childuser->fullname?> 
						<? else: ?>
							<Пользователь не указал имя>
						<? endif; ?>
						(<a href="/users/<?=$childuser->id?>"><?=$childuser->email?></a>) | 
						<a href="/user/employers?method=unlink&id=<?=$childuser->id?>">Удалить</a>
					</div>
				</div>
			</div>									
		</div>
	</div>
<? endforeach; ?>

<div class="smallcont">
	<div class="labelcont">
		<label><span>Email</span></label>
	</div>
	<div class="fieldscont">										
		<div class="">
			<div class="inp-cont <? if ($error){ echo 'error';} ?>">
				<form action="/user/employers?method=link" method="POST" id="search_employer">
					<input type="text" style="width:250px" name="email"/>
					<div onclick="$('#search_employer').submit()" class="button blue"><span>Добавить</span></div>
					<? if ($error): ?>
						<span class="inform">
							<span><?=$error?></span>
						</span>
					<? elseif ($is_post AND !$error):?>
						<span class="inform">
							<span>Сотрудник добавлен</span>
						</span>
					<? endif;?>
					<span class="inform">
						<span>Впишите email вашего сотрудника, нажмите кнопку "Добавить". </br>
						Эти пользователи смогут добавлять объявления от имени Вашей компании.</br>
						Учетная запись сотрудника должна быть с типом "Частное лицо"</span>
					</span>
					
					
				</form>
	  		</div>
		</div>
	</div>									
</div>
<? if ($requests->count()):?>
	<div class="smallcont p10">
		<h2>Вам отправили запросы на привязку:</h2>
	</div>
<? endif; ?>
<? foreach ($requests as $requestuser):?>
	<div class="smallcont">
		<div class="labelcont">
			<label><span><?=$requestuser->linked_user->id?></span></label>
		</div>
		<div class="fieldscont">										
			<div class="">
				<div class="inp-cont">
					<div class="pt4">
						<? if (trim($requestuser->linked_user->fullname)): ?>
							<?=$requestuser->linked_user->fullname?> 
						<? else: ?>
							<Пользователь не указал имя>
						<? endif; ?>
						(<a href="/users/<?=$requestuser->id?>"><?=$requestuser->linked_user->email?></a>) | 
						<a href="/user/employers?method=accept_request&id=<?=$requestuser->linked_user_id?>">Добавить</a> |
						<a href="/user/employers?method=decline_request&id=<?=$requestuser->linked_user_id?>">Удалить</a>
						
					</div>
				</div>
			</div>									
		</div>
	</div>
<? endforeach; ?>
