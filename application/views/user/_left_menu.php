<aside class="p_room-menu">
	<ul class="islide-menu">
		<li class="info-tooltip" data-controller-character="advert"><a href="" id="islide_myads"><i class="ico ico-myadd"></i><span>Мои объявления</span></a>
			<ul class="no_text-decoration">
				<?php if (Request::current()->action() == 'myads') : ?>
				<li class=""><span class="noclickable"><b><i class="ico "></i><span>Все</span></b></span></li>
				<?php else : ?>
				<li class=""><a href="<?=URL::site('user/myads')?>" class="clickable"><i class="ico "></i><span>Все</span></a></li>
				<?php endif; ?>

				<?php if (Request::current()->action() == 'published') : ?>
				<li class=""><span class="noclickable"><b><i class="ico "></i><span>Опубликованные</span></b></span></li>
				<?php else : ?>
				<li class=""><a href="<?=URL::site('user/published')?>" class="clickable"><i class="ico "></i><span>Опубликованные</span></a></li>
				<?php endif; ?>

				<?php if (Request::current()->action() == 'unpublished') : ?>
				<li class=""><span class="noclickable"><b><i class="ico "></i><span>Снятые</span></b></span></li>
				<?php else : ?>
				<li class=""><a href="<?=URL::site('user/unpublished')?>" class="clickable"><i class="ico "></i><span>Снятые</span></a></li>
				<?php endif; ?>

				<?php if (Request::current()->action() == 'in_archive') : ?>
				<li class=""><span class="noclickable"><b><i class="ico "></i><span>В архиве</span></b></span></li>
				<?php else : ?>
				<li class=""><a href="<?=URL::site('user/in_archive')?>" class="clickable"><i class="ico "></i><span>В архиве</span></a></li>
				<?php endif; ?>

				<?php if (Request::current()->action() == 'rejected') : ?>
				<li class=""><span class="noclickable"><b><i class="ico "></i><span>Заблокированные до исправления</span></b></span></li>
				<?php else : ?>
				<li class=""><a href="<?=URL::site('user/rejected')?>" class="clickable"><i class="ico "></i><span>Заблокированные до исправления</span></a></li>
				<?php endif; ?>

				<?php if (Request::current()->action() == 'banned') : ?>
				<li class=""><span class="noclickable"><b><i class="ico "></i><span>Заблокированные окончательно</span></b></span></li>
				<?php else : ?>
				<li class=""><a href="<?=URL::site('user/banned')?>" class="clickable"><i class="ico "></i><span>Заблокированные окончательно</span></a></li>
				<?php endif; ?>

				<li class="mt31">
					<?php if (FALSE) : ?>
					<?=Request::factory('block/user_from_employees_menu')->execute()?>
					<?php endif ?>

					<?php if (Request::current()->action() == 'favorites') : ?>
					<span class="noclickable"><b><i class="ico ico-favorites"></i><span>Избранные</span></b></span>
					<?php else : ?>
					<a href="<?=URL::site('user/favorites')?>" class="clickable"><i class="ico ico-favorites"></i><span>Избранные</span></a>
					<?php endif; ?>
				</li>
			</ul>
		</li>
		<?php if (Request::current()->action() == 'subscriptions') : ?>
		<li class="active">
		<?php else : ?>
		<li class="info-tooltip" data-controller-character="index">
		<?php endif; ?>
			<a href="<?=URL::site('user/subscriptions')?>" id="islide_subscriptions" class="clickable"><i class="ico ico-mysub"></i><span>Мои подписки</span></a>
		</li>
		<li class="info-tooltip" data-controller-character="index"><a href="" id="islide_services"><i class="ico ico-myserv"></i><span>Сервисы</span></a>
			<ul>
				<?php if (Request::current()->action() == 'invoices') : ?>
				<li><span class="noclickable"><i class="ico "></i><span><b>Счета</b></span></span></li>
				<?php else : ?>
				<li><a href="<?=URL::site('user/invoices')?>" class="clickable"><i class="ico "></i><span>Счета</span></a></li>
				<?php endif; ?>			

				<?php /*if (Request::current()->action() == 'massload') : ?>
				<li><span class="noclickable"><i class="ico "></i><span><b>Массовая загрузка</b></span></span></li>
				<?php else : ?>
				<li><a href="<?=URL::site('user/massload')?>" class="clickable"><i class="ico "></i><span>Массовая загрузка</span></a></li>
				<?php endif; */?>

				<?php if (Request::current()->action() == 'objectload') : ?>
				<li><span class="noclickable"><i class="ico "></i><span><b>Массовая загрузка</b></span></span></li>
				<?php else : ?>
				<li><a href="<?=URL::site('user/objectload')?>" class="clickable"><i class="ico "></i><span>Массовая загрузка</span></a></li>
				<?php endif; ?>

				<?php if (Request::current()->action() == 'objectunload') : ?>
				<li><span class="noclickable"><i class="ico "></i><span><b>Выгрузка объявлений</b></span></span></li>
				<?php else : ?>
				<li><a href="<?=URL::site('user/objectunload')?>" class="clickable"><i class="ico "></i><span>Выгрузка объявлений</span></a></li>
				<?php endif; ?>

				<?php if (Request::current()->action() == 'priceload') : ?>
				<li><span class="noclickable"><i class="ico "></i><span><b>Прайс-листы</b></span></span></li>
				<?php else : ?>
				<li><a href="<?=URL::site('user/priceload')?>" class="clickable"><i class="ico "></i><span>Прайс-листы</span></a></li>
				<?php endif; ?>

				<?/*Request::factory('block/massload_categories')->execute()*/?>

				<? /* ?>

				<?php if (Request::current()->action() == 'plan') : ?>
				<li><span class="noclickable"><i class="ico "></i><span><b>Тарифные планы</b></span></span></li>
				<?php else : ?>
				<li><a href="<?=URL::site('user/plan')?>" class="clickable"><i class="ico "></i><span>Тарифные планы</span></a></li>
				<?php endif; ?>

				<? */ ?>
			</ul>
		</li>
		<li class="info-tooltip" data-controller-character="index"><a href="" id="islide_profile"><i class="ico ico-profile"></i><span>Профиль</span></a>
			<ul>
				<?php /*if (Request::current()->action() == 'profile') : ?>
				<li><i class="ico "></i><span><b>Личные данные</b></span></li>
				<?php else : ?>
				<li><a href="<?=URL::site('user/profile')?>" class="clickable"><i class="ico "></i><span>Личные данные</span></a></li>
				<?php endif;*/ ?>

				<?php if (Request::current()->action() == 'userinfo') : ?>
				<li class="active"><i class="ico "></i><span><b>Данные пользователя</b></span></li>
				<?php else : ?>
				<li><a href="<?=URL::site('user/userinfo')?>" class="clickable"><i class="ico "></i><span>Данные пользователя</span></a></li>
				<?php endif; ?>

				<? if (Auth::instance()->get_user()->org_type == 2): ?>
					<?php if (Request::current()->action() == 'orginfo') : ?>
					<li class="active"><i class="ico "></i><span><b>Информация о компании</b></span></li>
					<?php else : ?>
					<li><a href="<?=URL::site('user/orginfo')?>" class="clickable"><i class="ico "></i><span>Информация о компании</span></a></li>
					<?php endif; ?>
				<? endif; ?>
				
				<?php /*if (Request::current()->action() == 'units') : ?>
				<li><i class="ico "></i><span><b>Адреса компании</b></span></li>
				<?php elseif (Auth::instance()->get_user()->org_type == 2) : ?>
				<li><a href="<?=URL::site('user/units')?>" class="clickable"><i class="ico "></i><span>Адреса компании</span></a></li>
				<?php endif; */?>				

				<?php if (Request::current()->action() == 'password') : ?>
				<li class="active"><i class="ico "></i><span><b>Смена пароля</b></span></li>
				<?php else : ?>
				<li><a href="<?=URL::site('user/password')?>" class="clickable"><i class="ico "></i><span>Смена пароля</span></a></li>
				<?php endif; ?>				

				<?php if (Auth::instance()->get_user()->org_moderate === 1 AND Auth::instance()->get_user()->org_type == 2 AND ! Auth::instance()->get_user()->linked_to_user) : ?>
					<?php if (Request::current()->action() == 'employers') : ?>
					<li class="active"><i class="ico "></i><span><b>Сотрудники</b></span></li>
					<?php else : ?>
					<li><a href="<?=URL::site('user/employers')?>" class="clickable"><i class="ico "></i><span>Сотрудники (<?=Auth::instance()->get_user()->count_employers()?>)</span></a></li>
					<?php endif; ?>
				<?php endif; ?>
					
				<?php if (Request::current()->action() == 'contacts') : ?>
					<li><i class="ico "></i><span><b>Управление контактами</b></span></li>
				<?php else : ?>
					<li><a href="<?=URL::site('user/contacts')?>" class="clickable"><i class="ico "></i><span>Управление контактами</span></a></li>
				<?php endif; ?>					
			</ul>
		</li>
	</ul>
</aside>
