<div class="winner page-addobj">
	<section class="main-cont">
		<div class="hheader">
			<h1 class="ta-c"><span><strong>
			Завершение регистрации на сайте бесплатных объявлений «Ярмарка»
			</strong></span></h1>
		</div><!--hheader-->
		
		<div class="fl100 ta-c pt16 pb15">
			<div class="smallcont reg-msg-cont">
			<p class="mb20"><?=$message?></p>			
			<? if ($success): ?>
					
					<p class="mb5">Перейти к:</p>
					<ul>
						<li><a href="/user/profile">Личные данные</a></li>
						<li><a href="/">Главная страница</a></li>
						<li><a href="/add">Подать объявление</a></li>				
					</ul>
								
			<? else: ?>
			
					<p class="mb5">Перейти к:</p>
					<ul>
						<li><a href="/user/login">Войти на сайт</a></li>
						<li><a href="/user/forgot_password">Напомнить пароль</a></li>			
					</ul>
								
			<? endif;?>
			</div>	
		</div>
	</section>
</div>
