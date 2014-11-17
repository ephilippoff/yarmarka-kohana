<div class="winner page-addobj">
	<section class="main-cont">
		<div class="hheader">
			<h1 class="ta-c"><span><strong>
			Завершение регистрации на сайте бесплатных объявлений «Ярмарка»
			</strong></span></h1>
		</div><!--hheader-->
		<div class="fl100 ta-c pt16 pb15">
			<?=$message?>
		</div>
		<? if ($success): ?>
			<div class="fl100  pt16 pb15">
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

		<? else:?>
			<div class="smallcont">
				<div class="labelcont">
					<label><span>Перейти к:</span></label>
				</div>
				<div class="fieldscont">
					<div class="inp-cont-short">
						<div class="inp-cont"  style="color:red;">
							<a href="/user/login">Войти на сайт</a>
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
							<a href="/user/forgot_password">Напомнить пароль</a>
						</div>
					</div>
				</div>									
			</div>
		<? endif;?>

	</section>
</div>
