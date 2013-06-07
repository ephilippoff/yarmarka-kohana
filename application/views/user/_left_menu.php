<aside class="p_room-menu">
	<ul class="islide-menu">
		<li><a href="" id="islide_myads"><i class="ico ico-myadd"></i><span>Мои объявления</span></a>
			<ul class="no_text-decoration">
				<?php if (Request::current()->action() == 'myads') : ?>
				<li class=""><b><i class="ico "></i><span>Все</span></b></li>
				<?php else : ?>
				<li class=""><a href="<?=URL::site('user/myads')?>" class="clickable"><i class="ico "></i><span>Все</span></a></li>
				<?php endif; ?>
				<li class=""><a href="<?=URL::site('user/published')?>" class="clickable"><i class="ico "></i><span>Опубликованные</span></a></li>
				<li class=""><a href="<?=URL::site('user/unpublished')?>" class="clickable"><i class="ico "></i><span>Снятые</span></a></li>
				<li class=""><a href="<?=URL::site('user/in_archive')?>" class="clickable"><i class="ico "></i><span>В архиве</span></a></li>
				<li class=""><a href="<?=URL::site('user/rejected')?>" class="clickable"><i class="ico "></i><span>Заблокированные до исправления</span></a></li>
				<li class=""><a href="<?=URL::site('user/banned')?>" class="clickable"><i class="ico "></i><span>Заблокированные окончательно</span></a></li>
				<li class="mt31">
					<?php if (Request::current()->action() == 'favorites') : ?>
					<b><i class="ico ico-favorites"></i><span>Избранные</span></b>
					<?php else : ?>
					<a href="<?=URL::site('user/favorites')?>" class="clickable"><i class="ico ico-favorites"></i><span>Избранные</span></a>
					<?php endif; ?>
				</li>
			</ul>
		</li>
		<?php if (Request::current()->action() == 'subscriptions') : ?>
		<li class="active">
		<?php else : ?>
		<li>
		<?php endif; ?>
			<a href="<?=URL::site('user/subscriptions')?>" id="islide_subscriptions" class="clickable"><i class="ico ico-mysub"></i><span>Мои подписки</span></a>
		</li>
		<li><a href="" id="islide_services"><i class="ico ico-myserv"></i><span>Сервисы</span></a>
			<ul>
				<?php if (Request::current()->action() == 'invoices') : ?>
				<li><i class="ico "></i><span><b>Счета</b></span></li>
				<?php else : ?>
				<li><a href="<?=URL::site('user/invoices')?>" class="clickable"><i class="ico "></i><span>Счета</span></a></li>
				<?php endif; ?>
			</ul>
		</li>
		<li><a href="" id="islide_profile"><i class="ico ico-profile"></i><span>Профиль</span></a>
			<ul>
				<?php if (Request::current()->action() == 'profile') : ?>
				<li><i class="ico "></i><span><b>Личные данные</b></span></li>
				<?php else : ?>
				<li><a href="<?=URL::site('user/profile')?>" class="clickable"><i class="ico "></i><span>Личные данные</span></a></li>
				<?php endif; ?>
			</ul>
		</li>
	</ul>
</aside>
