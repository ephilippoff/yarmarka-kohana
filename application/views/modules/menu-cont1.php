<div class="menu-cont menu-cont1 module">
	<div class="row">
		<div class="cont1 col-md-6 clearfix">
			<span class="menu-item"><a href=""><i class="ico ico16 vkontakte-ico16"></i></a></span>
			<?php if (!array_key_exists("HTTP_FROM", $_SERVER)) : ?>
				<span onclick="window.location = '/article/help'" class="menu-item">Помощь</span>
				<span onclick="window.location = 'http://feedback.yarmarka.biz/'" class="menu-item">Техподдержка</span>
				<span onclick="window.location = '/ourservices/reklamodatelyam'" class="menu-item">Рекламодателям</span>
				<span onclick="window.location = '/article/pravila-razmeshcheniya-obyavlenii'" class="menu-item">Правила</span>				
			<?php endif; ?>
		</div>
		<div class="cont2 col-md-6 clearfix">
			<?php if (Auth::instance()->get_user()) : ?>	
				<div class="menu-item-wrp">
					<span class="menu-item fn-lk-menu-cont"><span class="caret mr3"></span>Личный кабинет</span>
					<div class="proom-list iLight-cont fn-lk-menu border-color-blue popup-menu-cont right">
						<ul>
							<li><a href="<?= URL::site('user/published') ?>"><div class="img"><img src="<?= URL::site("/images/pr1.png") ?>" alt="" /></div>Мои объявления</a></li>
							<li><a href="<?= URL::site('user/subscriptions') ?>"><div class="img"></div>Мои подписки</a></li>				
							<li><a href="<?= URL::site('user/favorites') ?>"><div class="img"></div>Мои избранные</a></li>
							<li><a href="<?= URL::site('user/userinfo') ?>"><div class="img"><img src="<?= URL::site("/images/pr2.png") ?>" alt="" /></div>Личные данные</a></li>
							<li><a href="<?= URL::site('user/services_history') ?>"><div class="img"></div>История услуг</a></li>
							<li><a href="<?= URL::site('user/newspapers') ?>"><div class="img"></div>Купленные газеты</a></li>
							<li><a href="<?= URL::site('user/objectload') ?>" class="red"><div class="img"></div>Массовая загрузка</a></li>

							<?php if (Request::current()->action() != 'userpage') : ?>
								<?php if (Auth::instance()->get_user()->org_type == 2) : ?><li class="last"><a class="green" href="/users/<?= Auth::instance()->get_user()->login ?>"><div class="img"><!--<img src="" alt="" />--></div>Страница компании</a></li><?php endif; ?>
							<?php endif; ?>								

							<li class="last border-color-blue"><a href="<?= URL::site('user/logout') ?>"><div class="img"><img src="<?= URL::site('images/pr3.png') ?>" alt="" /></div>Выход</a></li>
						</ul>
					</div>							
				</div>				
			<?php endif ?>				

			<span class="menu-item">Избранное (0)</span>
			<?php if (!array_key_exists("HTTP_FROM", $_SERVER)) : ?>
				<span onclick="window.location = '/cart'" class="menu-item">Корзина (<span class="fn-cartCounter">0</span>)</span>
			<?php endif; ?>
		</div>
	</div>
</div>