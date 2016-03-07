<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">

	<title>Admin</title>
	<?=HTML::style('bootstrap/css/bootstrap.min.css')?>
	<?=HTML::style('bootstrap/css/bootstrap-responsive.min.css')?>
	<?=HTML::style('bootstrap/css/admin.css')?>
	<style>
		body {
			padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
		}
		input[type="text"] {
			height: auto;
		}
	</style>
</head>
<body>
	<?=View::factory('layouts/admin_head')?>
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="/">yarmarka.biz</a>
				<div class="nav-collapse collapse navbar-inverse-collapse">
					<ul class="nav">
						<?php if (Auth::instance()->have_access_to('user')) : ?>
							<li class="dropdown <?=($module_name == 'user') ? 'active' : ''?>">
								<a href="#"  class="dropdown-toggle" data-toggle="dropdown">Users <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?=URL::site('khbackend/users/index')?>">List</a></li>									
									<li><a href="<?=URL::site('khbackend/users/objectload')?>">Загрузки</a></li>
									<hr>
									<li><a href="<?=URL::site('khbackend/users/add_settings')?>">Все настройки</a></li>
									<li><a href="<?=URL::site('khbackend/users/moderation')?>">Модерация компаний</a></li>
									<?php if (Auth::instance()->have_access_to('sms')) : ?>
										<li><a href="<?=URL::site('khbackend/sms/index')?>">SMS</a></li>
									<?php endif?>									
								</ul>
							</li>
						<?php endif; ?>

						<?php if (Auth::instance()->have_access_to('articles')) : ?>
							<li class="dropdown <?=($module_name == 'articles') ? 'active' : ''?>">
								<a href="#"  class="dropdown-toggle" data-toggle="dropdown">Articles <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?=URL::site('khbackend/articles/index')?>">Articles list</a></li>
									<li><a href="<?=URL::site('khbackend/articles/news')?>">News list</a></li>
									<li><a href="<?=URL::site('khbackend/articles/add')?>">Add</a></li>
								</ul>
							</li>
						<?php endif; ?>

						<?php if (Auth::instance()->have_access_to('phones')) : ?>
							<li class="dropdown <?=($module_name == 'phones') ? 'active' : ''?>">
								<a href="#"  class="dropdown-toggle" data-toggle="dropdown">Phones <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?=URL::site('khbackend/phones/index')?>">List</a></li>
									<li><a href="<?=URL::site('khbackend/phones/moderation')?>">Moderation</a></li>
									<li><a href="<?=URL::site('khbackend/phones/followme')?>">Followme</a></li>
									<li><a href="<?=URL::site('khbackend/phones/followme_statistic')?>">Followme - Statistic</a></li>
								</ul>
							</li>
						<?php endif; ?>

						<?php if (Auth::instance()->have_access_to('object')) : ?>
						<li class="dropdown <?=($module_name == 'object') ? 'active' : ''?>">
							<a href="#"  class="dropdown-toggle" data-toggle="dropdown">Объявления <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="<?=URL::site('khbackend/objects/index')?>">Список</a></li>
								<li><a href="<?=URL::site('khbackend/landing/index')?>">Landings</a></li>
								<?php if (Auth::instance()->have_access_to('attribute')) : ?>
									<li><a href="<?=URL::site('khbackend/attributes/index')?>">Атрибуты</a></li>
								<?php endif?>	
								<?php if (Auth::instance()->have_access_to('reference')) : ?>
									<li><a href="<?=URL::site('khbackend/references/index')?>">Reference</a></li>
								<?php endif?>
								<?php if (Auth::instance()->have_access_to('object_reason')) : ?>
									<li><a href="<?=URL::site('khbackend/object_reasons/index')?>">Причины блокировки</a></li>
								<?php endif?>	
								<?php if (Auth::instance()->have_access_to('subscription')) : ?>
									<li><a href="<?=URL::site('khbackend/subscriptions/index')?>">Подписки</a></li>
								<?php endif?>
								<?php if (Auth::instance()->have_access_to('moderation_log')) : ?>
									<li><a href="<?=URL::site('khbackend/objects/moderation_log')?>">История модераций</a></li>
								<?php endif?>	
								<li><a href="<?=URL::site('khbackend/objects/csv_export')?>">CSV Export</a></li>					
							</ul>
						</li>
						<?php endif; ?>
						
						<?php if (Auth::instance()->have_access_to('reports')) : ?>
						<li class="dropdown <?=($module_name == 'reports') ? 'active' : ''?>">
							<a href="#"  class="dropdown-toggle" data-toggle="dropdown">Reports <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="<?=URL::site('khbackend/reports/operstat')?>">Количество модераций</a></li>
								<li><a href="<?=URL::site('khbackend/reports/oper_objects_list')?>">Модерации</a></li>	
								<li><a href="<?=URL::site('khbackend/orders/index')?>">Orders</a></li>
							</ul>
						</li>
						<?php endif; ?>						

						<?php if (Auth::instance()->have_access_to('category')) : ?>
						<li class="dropdown <?=($module_name == 'category') ? 'active' : ''?>">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Categories <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="<?=URL::site('khbackend/category/index')?>">List</a></li>
								<li><a href="<?=URL::site('khbackend/category/relations')?>">Настройка атрибутов</a></li>
								<li><a href="<?=URL::site('khbackend/category/structure')?>">Настройка меню</a></li>
							</ul>
						</li>
						<?php endif; ?>
						
						<?php if (Auth::instance()->have_access_to('reklama')) : ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Reklama <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="<?=URL::site('khbackend/reklama/index')?>">Контекст. ссылки</a></li>
								<li><a href="<?=URL::site('khbackend/reklama/add')?>">Добавить контекст. ссылку</a></li>
								<li><a href="<?=URL::site('khbackend/reklama/tickets')?>">Бегающие ссылки</a></li>								
								<li><a href="<?=URL::site('khbackend/reklama/menu_banners')?>">Баннеры в меню</a></li>
								<li><a href="<?=URL::site('khbackend/reklama/photocards')?>">Фото-объявления("Лидер")</a></li>
								<?php if (Auth::instance()->have_access_to('invoice')) : ?>
									<li><a href="<?=URL::site('khbackend/invoices/index')?>">Invoices</a></li>
								<?php endif?>								
							</ul>
						</li>
						<?php endif; ?>

						<?php if (Auth::instance()->have_access_to('settings')) : ?>
						<li class="dropdown <?=($module_name == 'settings') ? 'active' : ''?>">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Settings <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="<?=URL::site('khbackend/settings/index')?>">Index</a></li>
								<li><a href="<?=URL::site('khbackend/settings/cache')?>">Cache</a></li>
								<?php if (Auth::instance()->have_access_to('coreredirect')) : ?>
									<li><a href="<?=URL::site('khbackend/coreredirects/index')?>">Core redirects</a></li>
								<?php endif?>
								<?php if (Auth::instance()->have_access_to('seopattern')) : ?>
									<li><a href="<?=URL::site('khbackend/seopatterns/index')?>">Seo patterns</a></li>
								<?php endif?>									
								<li><a href="<?=URL::site('khbackend/settings/test_email')?>">Test email</a></li>
							</ul>
						</li>
						<?php endif; ?>
						
						<?php if (Auth::instance()->have_access_to('filesstorage')) : ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Файлы <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="<?=URL::site('khbackend/filesstorage/index')?>">Список</a></li>
							</ul>
						</li>
						<?php endif; ?>						
						
						<?php if (Auth::instance()->get_user()) : ?>
							<li><a href="<?=URL::site('khbackend/welcome/logout')?>">Log Out</a></li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<div id="loading" style="position:absolute; z-index:99999; background-color:red; color: white; top: 10; left: 5; display:none">
			Loading...
		</div>
		<?=$_content?>
	</div>

	<?php 
	if (Kohana::$profiling === TRUE) 
	{
		echo View::factory('profiler/stats');
	}
	?>
</body>
</html>
