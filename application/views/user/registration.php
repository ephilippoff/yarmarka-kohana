<div class="winner">
	<section class="main-cont">
		<div class="hheader">
			<h1 class="ta-c"><span><strong>
			Регистрация
			</strong></span></h1>
		</div><!--hheader-->

		<?if ($auth): ?>
			<div class="fl100  pt16 pb15">				
				<div class="smallcont reg-msg-cont">
					<p class="mb20">Вы зарегистрированы и авторизованы.</p>
					<p class="mb5">Перейти к:</p>
					<ul>
						<li><a href="/user/profile">Личные данные</a></li>
						<li><a href="/">Главная страница</a></li>
						<li><a href="/add">Подать объявление</a></li>				
					</ul>
				</div>		
			</div>

		<?elseif ($success): ?>
			<div class="fl100  pt16 pb15">
				<div class="smallcont reg-msg-cont">
					На email, который вы указали, отправлено письмо с информацией о завершении регистрации. Вы можете закрыть эту страницу или перейти на <a href='/'>главную</a>.
				</div>
			</div>

		<?else: ?>
		
		<div class="smallcont pb20 pt20">
			<div class="labelcont">
				<label></label>
			</div>
			<div class="fieldscont">
				<div class="inp-cont-short">
					<?=$ulogin_html?>
					<?php if ($ulogin_errors) : ?><div class="red mt10"><?=$ulogin_errors?></div><?php endif?>	
				</div>

			</div>
		</div>	
		
		<form method="POST"  action="" id="element_list">			
			<?=Form::hidden('csrf', $token)?>
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
								
								<input type="password" name="pass2" value="<? if ($params->pass2) echo $params->pass2;?>" autocomplete="off"/>
								
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
						<label><span>Тип</span></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<div class="inp-cont <? if ($error->type) echo "error";?>">
								<span class="required-label">*</span>

								<input name="type" type="radio" id="type_fl" value="1" <? if ($params->type == "1") echo "checked";?>/><label for="type_fl">Частное лицо</label>
								<? if (!$error->type): ?>
								<span class="inform">
									<span>Ограничение на количество объявлений:</span></br>
									<? foreach($limited_categories as $category):?>
										<?=$category->title?> (<?=$category->max_count_for_user?>),
									<? endforeach; ?>
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
						<label><span>Введите слово с картинки</span></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<div class="inp-cont  <? if ($error->captcha) echo "error";?>">
								<span class="required-label">*</span>
								<?=$captcha?>
								<input type="text" name="captcha" value="<? if ($params->captcha) echo $params->captcha;?>" autocomplete="off"/>
								<? if ($error->captcha): ?>
								<span class="inform fn-error">
									<span><?=$error->captcha?></span>
								</span>
								<? else: ?>								
									<span class="inform">
										<span>на русском языке</span>
									</span>
								<? endif; ?>								
							</div>
						</div>
					</div>									
				</div>

				<div class="smallcont">
					<div class="labelcont">
						
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<div class="inp-cont accept <? if ($error->i_agree) echo "error";?>"">								
								<input type="checkbox" value="yes" name="i_agree"> Я согласен с <a href="/article/usloviya-ispolzovaniya-saita-yarmarka" target="_blank">условиями использования</a> и <a href="/article/pravila-razmeshcheniya-obyavlenii" target="_blank">правилами размещения объявлений</a> на сайте "Ярмарка".								
								<?php if ($error->i_agree): ?>
									<span class="inform fn-error">
										<span><?=$error->i_agree?></span>
									</span>
								<?php endif?>
							</div>
						</div>
					</div>									
				</div>
				<? if ($error->csrf): ?>
					<div class="smallcont">
						<div class="labelcont">
							<label><span></span></label>
						</div>
						<div class="fieldscont  fn-error">
							<div class="inp-cont-short">
								<div class="inp-cont accept">								
									<span style="color:red"><?=$error->csrf;?></span>
								</div>
							</div>
						</div>									
					</div>
				<? endif; ?>

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
