<?php if (Session::instance()->get_once('success')) : ?>
<div style="color:green">Пароль успешно изменен</div>
<?php endif ?>

<form action="/user/password" id="password" method="POST">
<ul>
	<li>
		<div class="input style2 <?php if ($error) echo 'error'?>">
			<label><span><i class="name">Пароль:</i></span></label>					                    			
			<p class="myinform profile-input-wrapper">
				<div class="inp-cont-bl">
					<div class="inp-profile">
						<div class="inp">
							<input style="box-sizing: initial;" type="password" name="password" value="" />
						</div>
					</div>
				</div>
			</p>

			<div class="alert-bl " <?php if ($error) : ?>style="display: block" <?php endif ?>>
				<div class="cont">
					<div class="img"></div>
					<div class="arr"></div>
					<p class="text"><span><?=$error?></span></p>
				</div>
			</div>
		</div>
	</li>

	<li>
		<div class="input style2 <?php if ($error) echo 'error'?>">
			<label><span><i class="name">Повтор пароля:</i></span></label>					                    			
			<p class="myinform profile-input-wrapper">
				<div class="inp-cont-bl">
					<div class="inp-profile">
						<div class="inp">
							<input style="box-sizing: initial;" type="password" name="password_repeat" value="" />
						</div>
					</div>
				</div>
			</p>
		</div>
	</li>
</ul>
</form>
<div onclick="$('#password').submit()" class="button blue icon-arrow-r btn-next"><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Сохранить</span></div>							