<div class="form-cont">
	<? if ($requests->count()): ?>
		<div class="row mb10">
			<div class="col-md-3 col-xs-4 labelcont">
				<label></label>
			</div>
			<div class="col-md-9 col-xs-8">
				<h2>Привязанные пользователи:</h2>
			</div>
		</div>
	<? endif; ?>
	<? foreach ($users as $childuser): ?>
		<div class="row mb10">
			<div class="col-md-3 col-xs-4 labelcont">
				<label><?= $childuser->id ?></label>
			</div>
			<div class="col-md-9 col-xs-8">
				<div class="inp-cont">
					<div class="pt4">
						<? if (trim($childuser->fullname)): ?>
							<?= $childuser->fullname ?> 
						<? else: ?>
							<Пользователь не указал имя>
						<? endif; ?>
						(<a href="/users/<?= $childuser->id ?>"><?= $childuser->email ?></a>) | 
						<a href="/user/employers?method=unlink&id=<?= $childuser->id ?>">Удалить</a>
					</div>
				</div>
			</div>
		</div>
	<? endforeach; ?>

	<div class="row mb10">
		<div class="col-md-3 col-xs-4 labelcont">
			<label>Email</label>
		</div>
		<div class="col-md-9 col-xs-8">
			<div class="inp-cont <?if ($error) {echo 'error';}?>">
				<form action="/user/employers?method=link" method="POST" id="search_employer">
					<input class="w100p" type="text" name="email"/>
					<div onclick="$('#search_employer').submit()" class="button button-style1 bg-color-blue mt10 mb10">Добавить</div>
						<? if ($error): ?>
						<span class="inform">
						<?= $error ?>
						</span>
						<?	elseif ($is_post AND ! $error): ?>
						<span class="inform">
							Сотрудник добавлен
						</span>
						<? endif; ?>
					<span class="inform">
						<span>Впишите email вашего сотрудника, нажмите кнопку "Добавить". </br>
							Эти пользователи смогут добавлять объявления от имени Вашей компании.</br>
							Учетная запись сотрудника должна быть с типом "Частное лицо"</span>
					</span>
				</form>
			</div>
		</div>
	</div>

<? if ($requests->count()): ?>
		<div class="row mb10">
			<div class="col-md-3 col-xs-4 labelcont">
				<label></label>
			</div>
			<div class="col-md-9 col-xs-8">
				<h2>Вам отправили запросы на привязку:</h2>
			</div>
		</div>
	<? endif; ?>

<? foreach ($requests as $requestuser): ?>

		<div class="row mb10">
			<div class="col-md-3 col-xs-4 labelcont">
				<label><?= $requestuser->linked_user->id ?></label>
			</div>
			<div class="col-md-9 col-xs-8">
				<div class="inp-cont">
					<div class="pt4">
						<? if (trim($requestuser->linked_user->fullname)): ?>
							<?= $requestuser->linked_user->fullname ?> 
						<? else: ?>
							<Пользователь не указал имя>
						<? endif; ?>
						(<a href="/users/<?= $requestuser->id ?>"><?= $requestuser->linked_user->email ?></a>) | 
						<a href="/user/employers?method=accept_request&id=<?= $requestuser->linked_user_id ?>">Добавить</a> |
						<a href="/user/employers?method=decline_request&id=<?= $requestuser->linked_user_id ?>">Удалить</a>

					</div>
				</div>
			</div>
		</div>
<? endforeach; ?>
</div>