<div class="winner">
	<section class="main-cont">
		<div class="hheader">
			<h1 class="ta-c"><span><strong>
			Регистрация на сайте бесплатных объявлений «Ярмарка»
			</strong></span></h1>
		</div><!--hheader-->

		<?if ($auth): ?>
			<div class="fl100  pt16 pb15">
				<?="Вы уже зарегистрированы и авторизованы"?>
				<div class="smallcont">
					<div class="labelcont">
						<label><span>Перейти к:</span></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<div class="inp-cont"  style="color:red;">
								<a href="/user/profile">Личные данные</a>
							</div>
						</div>
					</div>									
				</div>
				<div class="smallcont">
					<div class="labelcont">
						<label><span></span></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<div class="inp-cont"  style="color:red;">
								<a href="/">Главная страница</a>
							</div>
						</div>
					</div>									
				</div>
				<div class="smallcont">
					<div class="labelcont">
						<label><span></span></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<div class="inp-cont"  style="color:red;">
								<a href="/add">Подать объявление</a>
							</div>
						</div>
					</div>									
				</div>			
			</div>

		<?elseif ($success): ?>
			<div class="fl100  pt16 pb15">
				<?="На е-мейл, который вы указали, отправлено письмо для подтверждения регистрации. Вы можете закрыть эту страницу или перейти на <a href='yarmarka.biz'>главную</a>."?>
			</div>

		<?else: ?>
		<form method="POST"  action="" id="element_list">			
			<?=Form::hidden('csrf', Security::token(TRUE))?>
			<div class="fl100  pt16 pb15">
				<div class="smallcont">
					<div class="labelcont">
						<label><span>Email</span></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<div class="inp-cont <? if ($error->login) echo "error";?>">
								<span class="required-label">*</span>
								
								<input type="text" name="login" value="<? if ($params->login) echo $params->login;?>"/>
								<? if ($error->login): ?>
									<span class="inform fn-error">
										<span><?=$error->login?></span>
									</span>
								<? else: ?>
									<span class="inform">
										<span>На ваш E-mail придет письмо с подтверждением регистрации</span>
									</span>
								<? endif; ?>
							</div>
						</div>
					</div>									
				</div>

				<div class="smallcont">
					<div class="labelcont">
						<label><span>Пароль</span></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<div class="inp-cont <? if ($error->pass) echo "error";?>">
								<span class="required-label">*</span>
								
								<input type="password" name="pass" value="<? if ($params->pass) echo $params->pass;?>" autocomplete="off"/>
								
								<? if ($error->pass): ?>
								<span class="inform fn-error">
									<span><?=$error->pass?></span>
								</span>
								<? else: ?>								
									<span class="inform">
										<span>Поле пароль не может быть пустым</span>
									</span>
								<? endif; ?>
							</div>
						</div>
					</div>									
				</div>

				<div class="smallcont">
					<div class="labelcont">
						<label><span></span></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<div class="inp-cont <? if ($error->pass2) echo "error";?>">
								<span class="required-label">*</span>
								
								<input type="password" name="pass2" value="<? if ($params->pass) echo $params->pass;?>" autocomplete="off"/>
								
								<? if ($error->pass2): ?>
								<span class="inform fn-error">
									<span><?=$error->pass2?></span>
								</span>
								<? else: ?>
									<span class="inform">
										<span>Введите тот же пароль второй раз</span>
									</span>
								<? endif; ?>
								
							</div>
						</div>
					</div>									
				</div>

				<div class="smallcont">
					<div class="labelcont">
						<label><span>Статус</span></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<div class="inp-cont <? if ($error->type) echo "error";?>">
								<span class="required-label">*</span>
								
								
								
								

								<input name="type" type="radio" id="type_fl" value="1" <? if ($params->type == "1") echo "checked";?>/><label for="type_fl">Частное лицо</label>
								<? if (!$error->type): ?>
								<span class="inform">
									<span>В некоторые рубрики действует ограничение на количество объявлений</span>
								</span>
							    <? endif; ?>

							    <input name="type" type="radio" id="type_company" value="2" <? if ($params->type == "2") echo "checked";?>/><label for="type_company">Компания</label></br>
								<? if (!$error->type): ?>
								<span class="inform">
									<span>Для подтверждения этого статуса, требуется предоставить ИНН. Предоставляются дополнительные услуги и расширенные лимиты для подачи объявлений</span>
								</span>
								<? endif; ?>

							    <? if ($error->type): ?>
								<span class="inform fn-error">
									<span><?=$error->type?></span>
								</span>
								<? endif; ?>
								
							</div>
						</div>
					</div>									
				</div>


				<div class="smallcont">
					<div class="labelcont">
						<label><span></span></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<div class="inp-cont">								
								Нажимая на кнопку "Далее", я принимаю <a href="/article/usloviya-ispolzovaniya-saita-yarmarka" target="_blank">условия использования</a> и <a href="/article/pravila-razmeshcheniya-obyavlenii" target="_blank">правила размещения объявлений</a> на сайте "Ярмарка".
							</div>
						</div>
					</div>									
				</div>
	


			</div>
			<div class="fl100 form-next-cont">
				<div class="smallcont">
					<div class="labelcont"></div>	
					<div class="fieldscont ta-r mb15">						
						<div onclick="$('#element_list').submit()" class="button blue icon-arrow-r btn-next"><span>Далее</span></div>							
					</div><!--fieldscont-->
				</div><!--smallcont-->	
			</div>
		</form>
		<? endif; ?>
	</section>
</div>
