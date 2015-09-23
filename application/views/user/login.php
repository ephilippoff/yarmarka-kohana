<div class="winner">
	<section class="main-cont">
		<div class="hheader">
			<h1 class="ta-c"><span><strong>
			Авторизация
			</strong></span></h1>
		</div><!--hheader-->

		<?if ($user):?>
			<div class="fl100 ta-c pt16 pb15">
			Вы уже авторизованы
			</div>
		<? else: ?>
		<form method="POST"  action="" id="element_list">			
			<?=Form::hidden('csrf', $token)?>
			<div class="fl100  pt40 pb15">
				<div class="smallcont">
					<div class="labelcont">
						<label><span>Логин</span></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<div class="inp-cont">
								<span class="required-label">*</span>
								
								<input type="text" name="login" value="<? if ($params->login) echo $params->login;?>"/>
									
								
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
							<div class="inp-cont <? if ($error) echo "error";?>">
								<span class="required-label">*</span>
								
								<input type="password" name="pass" value="<? if ($params->pass) echo $params->pass;?>"/>
									
								<? if ($error): ?>
								<span class="inform fn-error">
									<span><?=$error?></span>
								</span>
								<? endif; ?>
							</div>
						</div>
					</div>									
				</div>

				<div class="smallcont pb20">
					<div class="labelcont">
						<label></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<a target="_blank" href="/user/registration">Зарегистрироваться</a>
						</div>
					</div>
				</div>	

				<div class="smallcont pb20">
					<div class="labelcont">
						<label></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<a target="_blank" href="/user/forgot_password">Напомнить / сменить пароль</a>
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
		
		<div class="smallcont pb20">
			<div class="labelcont">
				<label></label>
			</div>
			<div class="fieldscont">
				<div class="inp-cont-short">
					<p>Или войти под учетной записью одного из сервисов:</p>
					<br>
					<?=$ulogin_html?>
					<?php if ($ulogin_errors) : ?><div class="red mt10"><?=$ulogin_errors?></div><?php endif?>					
				</div>

			</div>
		</div>				
		
		<? endif;?>
	</section>
</div>
