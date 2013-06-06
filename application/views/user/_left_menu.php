<aside class="p_room-menu">
	<ul class="islide-menu">
		<li><a href="" id="islide_myads"><i class="ico ico-myadd"></i><span>Мои объявления</span></a>
			<ul class="no_text-decoration">
				<li class=""><a href=""><i class="ico "></i><span>Активные</span></a>
					<ul>
						<li><a href=""><i class="ico "></i><span>Знакомства</span></a></li>
						<li><a href=""><i class="ico "></i><span>Автомобили</span></a></li>
						<li><a href=""><i class="ico "></i><span>Домашние любимцы</span></a></li>
					</ul>
				</li>
				<li><a href=""><i class="ico "></i><span>Неактивные</span></a>
					<ul>
						<li><a href=""><i class="ico "></i><span>Знакомства</span></a></li>
						<li><a href=""><i class="ico "></i><span>Автомобили</span></a></li>
						<li><a href=""><i class="ico "></i><span>Домашние любимцы</span></a></li>
					</ul>
				</li>
				<li><a href=""><i class="ico "></i><span>На модерации</span></a></li>
				<li><a href=""><i class="ico "></i><span>Черновики</span></a></li>
				<li><a href=""><i class="ico "></i><span>Удаленные</span></a></li>
				<li class="mt31">
					<?php if (Request::current()->action() == 'favorites') : ?>
					<b><i class="ico ico-favorites"></i><span>Избранные</span></b>
					<?php else : ?>
					<a href="<?=URL::site('user/favorites')?>" class="clickable"><i class="ico ico-favorites"></i><span>Избранные</span></a>
					<?php endif; ?>
				</li>
			</ul>
		</li>
		<li><a href="" id="islide_subscriptions"><i class="ico ico-mysub"></i><span>Мои подписки</span></a></li>
		<li><a href=""><i class="ico ico-myserv"></i><span>Сервисы</span></a>
			<ul>
				<li><a href=""><i class="ico "></i><span>Размещенные в нескольких городах</span></a></li>
				<li><a href=""><i class="ico "></i><span>«Ярмарка +»</span></a></li>
				<li><a href=""><i class="ico "></i><span>Безопасность</span></a></li>
				<li><a href=""><i class="ico "></i><span>Счета</span></a></li>
				<li><a href=""><i class="ico "></i><span>Безопасность</span></a></li>
			</ul>
		</li>
		<li><a href="" id="islide_profile"><i class="ico ico-profile"></i><span>Профиль</span></a>
			<ul>
				<li>
					<i class="ico "></i><span>
						Личные данные
					</span>
				</li>
				<li><a href=""><i class="ico "></i><span>Счета</span></a></li>
				<li><a href=""><i class="ico "></i><span>Безопасность</span></a></li>
			</ul>
		</li>
	</ul>
</aside>
