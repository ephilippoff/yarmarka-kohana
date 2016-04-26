<div class="form-cont">
	<form method="POST" id="userinfo" enctype="multipart/form-data">

		<div class="row mb10">
			<div class="col-md-3  labelcont">
				<label>Идентификатор</label>
			</div>
			<div class="col-md-9 ">
				<div class="inp-cont ">
					<?= $user->id ?>				
				</div>
			</div>
		</div>		

		<div class="row mb10">
			<div class="col-md-3  labelcont">
				<label>Email</label>
			</div>
			<div class="col-md-9 ">
				<div class="inp-cont ">
					<?= $user->email ?>				
				</div>
			</div>
		</div>		

		<script type="text/javascript">
			function reset_orgtype() {
				if (confirm("Вы уверены что хотите сменить тип учетной записи на 'Частное лицо?'")) {
					window.location = "/user/reset_orgtype";
				}
			}
		</script>		

		<div class="row mb10">
			<div class="col-md-3  labelcont">
				<label>Тип учетной записи</label>
			</div>
			<div class="col-md-9 ">
				<div class="inp-cont">
					<div class="pt4">
						<?= $types[$user->org_type] ?>
					</div>
					<? if ($user->org_type == 2): ?>
						<span class="inform">
							<span>Необходимо заполнить информацию о компании <a href="/user/orginfo">здесь</a></br>
								Вы можете сбросить тип учетной записи на "Частное лицо" если перейдете по <span class="link" style="cursor:pointer;" href="/user/reset_orgtype" onclick="reset_orgtype()">ссылке</span>. Учитывайте что объявления сверх лимитов будут сняты с публикации , все привязки из раздела "Сотрудники" отменятся.
							</span>
						</span>
					<? endif; ?>
					<? if ($user->org_type == 1): ?>
						<span class="inform">
							<span>Вы можете сменить тип учетной записи на "Компания" если перейдете по <a href="/user/reset_to_company">ссылке</a></br>
								Для подтверждения потребуется загрузить скан оригинала или копии ИНН.</br>
								<? if ($parent_user->loaded()): ?>
									При смене типа учетной записи, привязка к компании <?= $parent_user->org_name ?> отменится.
								<? endif; ?>
							</span>
						</span>
					<? endif; ?>
				</div>
			</div>
		</div>

		<script type="text/javascript">
			function reset_parent_user() {
				if (confirm("Вы уверены что хотите отменить привязку к компании?")) {
					window.location = "/user/reset_parent_user";
				}
			}

			function isValidEmail(email)
			{
				return /^[a-z0-9]+([-._][a-z0-9]+)*@([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,4}$/.test(email)
						&& /^(?=.{1,64}@.{4,64}$)(?=.{6,100}$).*/.test(email);
			}

			function request_to_company()
			{
				var email = $('#request_email').val();
				if (isValidEmail(email)) {
					window.location = "/user/user_link_request?email=" + email;
				}
			}
		</script>


		<div class="row mb10">
			<div class="col-md-3  labelcont">
				<label>Привязка к компании</label>
			</div>
			<div class="col-md-9 ">
				<div class="inp-cont ">
					<? if (!$request_company->loaded()): ?>
						<div class="pt4">

							<? if ($parent_user->loaded()): ?>
								<?= $parent_user->org_name ?> (<?= $parent_user->email ?>) | <span class="link" style="cursor:pointer;" href="/user/reset_parent_user" onclick="reset_parent_user()">отменить привязку</span>
								<? if ($parent_user->filename): ?>
									<div class="p10">
										<? $logo = Imageci::getSitePaths($parent_user->filename); ?>
										<img src="<?= $logo["120x90"] ?>">
									</div>
								<? endif; ?>
							<? else: ?>
								<div class="pb4">
									Нет привязки
								</div>


								<input class="w100p" type="text" id="request_email" placeholder="Введите Email компании"/>
								<span onclick="request_to_company();" class="mt10 mb10 button button-style1 bg-color-blue">Отправить запрос на привязку</span>
							<? endif; ?>

						</div>
						<? if (!$parent_user->loaded()): ?>
							<span class="inform">
								<span>Вы можете размещать объявления от лица какойлибо общей учетной записи компании, если компания подтвердила свой ИНН и добавила Вашу учетную запись в разделе "Сотрудники". Отправьте запрос на привязку.</span>
							</span>
						<? endif; ?>

					<? else: ?>
						<div class="pt4">
							Нет привязки. Отправлен запрос |  <a href="/user/user_link_request?method=delete_request">Отменить</a> </br>
							<?= $request_company->user->org_name ?> (<?= $request_company->user->email ?>)
							<? if ($request_company->user->filename): ?>
								<div class="p10">
									<? $logo = Imageci::getSitePaths($request_company->user->filename); ?>
									<img src="<?= $logo["120x90"] ?>">
								</div>
							<? endif; ?>
						</div>
					<? endif; ?>				
				</div>
			</div>
		</div>			





		<? if ($user->org_type == 1 AND ! $parent_user->loaded()): ?>

			<div class="row mb10">
				<div class="col-md-3  labelcont">
					<label>Рубрики с ограничениями</label>
				</div>
				<div class="col-md-9 ">
					<div class="inp-cont">
						<div class="pt4">
							<? foreach ($categories_limit as $category): ?>
								<?= $category->title ?> (<?= $category->max_count_for_user ?>),
							<? endforeach; ?>
						</div>
						<span class="inform">
							<span>В эти рубрики можно подать не более указанного количества объявлений.</br>
								Для большего количества нужно сменить тип учетной записи на "Компания". Либо привязать свою учетную запись к компании. </br>
								Для некоторых случаев, мы можем расширить лимиты индивидуально, обратитесь в техподдержку.
							</span>
						</span>
					</div>
				</div>
			</div>			

		<? endif; ?>



		<? if (!$parent_user->loaded() AND count($individual_limit) > 0): ?>

			<div class="row mb10">
				<div class="col-md-3  labelcont">
					<label>Индивидуальные ограничения</label>
				</div>
				<div class="col-md-9 ">
					<div class="inp-cont ">
						<div class="inp-cont">
							<div class="pt4">
								<? foreach ($individual_limit as $category): ?>
									<?= $category['title'] ?> (<?= $category['individual_limit'] ?>),
								<? endforeach; ?>
							</div>
							<span class="inform">
								<? if ($user->org_type == 1): ?>
									<span>Для этих рубрик, установлены расширенные лимиты на количество объявлений индивидуально для Вас</span>
								<? else: ?>
									<span><?= Kohana::message('validation/object_form', 'max_objects_company') ?></span>
								<? endif; ?>
							</span>
						</div>					
					</div>
				</div>
			</div>			

		<? endif; ?>




		<? foreach ($form as $field): ?>


			<div class="row mb10">
				<div class="col-md-3  labelcont">
					<label><?= $field["title"] ?></label>
				</div>

				<?
				$lenth_unput = "col-md-6";
				if ($field["type"] == "long" OR $field["type"] == "text")
					$lenth_unput = "col-md-12";
				?>			

				<div class="col-md-9 ">
					<div class="row">
						<div class="<?= $lenth_unput ?>">
							<div class="inp-cont <? if ($errors->{$field["name"]}) echo "error"; ?>">
								<? if ($field["required"]): ?>
									<span class="required-star">*</span>
								<? endif; ?>

								<? if ($field["type"] == "photo"): ?>
									<?= $field["html"] ?>
									<? if ($field["path"]): ?>
										<img src="<?= $field["path"] ?>" style="padding:10px;">
									<? endif; ?>
								<? else: ?>
									<?= $field["html"] ?>
								<? endif; ?>
								<? if ($field["description"]): ?>
									<span class="inform">
										<span><?= $field["description"] ?></span>
									</span>
								<? endif; ?>
								<? if ($errors->{$field["name"]}): ?>
									<span class="inform fn-error">
										<span><?= $errors->{$field["name"]} ?></span>
									</span>
								<? endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>					

		<? endforeach; ?>


		<div class="row mb20">
			<div class="col-md-3 labelcont">
				<label></label>
			</div>
			<div class="col-md-9 ta-r">	
				<span onclick="$('#userinfo').submit()" class="button button-style1 bg-color-blue btn-next">Сохранить <i class="fa fa-arrow-right"></i></span>
			</div>
		</div>			
	</form>
</div>