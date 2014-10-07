<div class="winner">
	<section class="main-cont">
		<div class="hheader">
			<h1 class="ta-c"><span><strong>
			Напоминание пароля
			</strong></span></h1>
		</div><!--hheader-->

		<?if ($user):?>
			<div class="fl100 ta-c pt16 pb15">
			Вы авторизованы и можете поменять пароль в <a href="/user/password">личном кабинете</a>
			</div>
		<? elseif($status == "success"): ?>
			<div class="fl100 ta-c pt16 pb15">
				Вам на почту отправлена ссылка для восстановления пароля. Если письма нет, попробуйте <a href="/user/forgot_password">еще раз</a>, возможно вы неверно указали ваш email.
			</div>
		<? elseif($status == "failure"): ?>
			<div class="fl100 ta-c pt16 pb15">
				Ссылка на восстановление пароля устарела, либо пользователь заблокирован или не найден. Попробуйте восстановить пароль <a href="/user/forgot_password">еще раз</a>.
			</div>
		<? else: ?>
		<form method="POST"  action="" id="element_list">			

			<div class="fl100  pt16 pb15">
				<div class="smallcont">
					<div class="labelcont">
						<label><span>E-mail</span></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<div class="inp-cont <? if ($error) echo "error";?>">
								<span class="required-label">*</span>
								
								<input type="text" name="email" value="<? if ($params->email) echo $params->email;?>"/>
								
								<? if ($error): ?>
									<span class="inform fn-error">
										<span><?=$error?></span>
									</span>
								<? endif; ?>

								<span class="inform">
									<span>Введите адрес email с которым регистрировались на сайте</span>
								</span>
								
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
		<? endif;?>
	</section>
</div>
