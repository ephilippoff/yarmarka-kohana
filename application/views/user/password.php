<script>
$(document).ready(function(){
	$('#islide_profile').click();
});
</script>

<form method="post" id="change_password">
<div class="winner cabinet profile">
	<section class="main-cont">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">
				<header><span class="title">Смена пароля</span></header>
				<div class="p_cont secure-bl myinfo">
					<article class="iinput-bl">

					<?php if (Session::instance()->get_once('success')) : ?>
					<div style="color:green">Пароль успешно изменен</div>
					<?php endif ?>

					<ul>
						<li>
							<div class="input style2 <?php if ($error) echo 'error'?>">
								<label><span><i class="name">Пароль:</i></span></label>					                    			
								<p class="myinform profile-input-wrapper">
									<div class="inp-cont-bl">
										<div class="inp-profile">
											<div class="inp">
												<input type="password" name="password" value="" />
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
												<input type="password" name="password_repeat" value="" />
											</div>
										</div>
									</div>
								</p>
							</div>
						</li>
					</ul>
					</article>
					<div onclick="$('#change_password').submit()" class="btn-red big bnt-reg next"><span>Сохранить</span></div>
				</div>
			</section>
		</div>	   
		  
	</section>
</div><!--end content winner-->
</form>