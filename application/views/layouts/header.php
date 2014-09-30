<header class="m_header header_image"  <?php if ( ! empty($style)) echo "style='$style'" ?>>
	<section class="winner">
		<?php echo View::factory('block/_user_message')?>
		
	
		
		<div class="mt25 z3">
			<div class="top-line ">
				<div class="cont cf">
				<div class="logo">
					<a href="http://<?=Region::get_current_domain()?>"><img src="<?=URL::site('images/logo.png')?>" alt=""></a>
					<span class="domen"><?=Region::get_current_domain()?></span>
				</div>
				
				<div class="btn-red big add-advert"><a href="<?=CI::site('add')?>"><span>Подать объявление</span></a></div>				
												
				<!--noindex-->
				
				<div class="menu-cont">
					<!--<a class="lk menu iLight-nav" href="#">Личный кабинет</a>-->

				<?php if (Auth::instance()->get_user()) : ?>	

					<div class="enter-bl logon iLight lk menu">
						<span class="iLight-nav link cur_p">Личный кабинет</span>
						<div class="proom-list iLight-cont">
							<ul>
								<li><a href="<?=URL::site('user/published')?>"><div class="img"><img src="<?=URL::site("/images/pr1.png")?>" alt="" /></div>Мои объявления</a></li>
								<li><a href="<?=URL::site('user/subscriptions')?>"><div class="img"><!--<img src="" alt="" />--></div>Мои подписки</a></li>				
								<li><a href="<?=URL::site('user/favorites')?>"><div class="img"><!--<img src="" alt="" />--></div>Мои избранные</a></li>
								<li><a href="<?=URL::site('user/profile')?>"><div class="img"><img src="<?=URL::site("/images/pr2.png")?>" alt="" /></div>Личные данные</a></li>
								<li><a href="<?=URL::site('user/services_history')?>"><div class="img"><!--<img src="" alt="" />--></div>История услуг</a></li>
								<li><a href="<?=URL::site('user/newspapers')?>"><div class="img"><!--<img src="" alt="" />--></div>Купленные газеты</a></li>
								<li><a href="<?=URL::site('user/objectload')?>" class="red"><div class="img"><!--<img src="" alt="" />--></div>Массовая загрузка</a></li>

								<?php if (Request::current()->action() != 'userpage') : ?>
									<?php if (Auth::instance()->get_user()->org_type == 2) : ?><li class="last"><a class="green" href="/users/<?=Auth::instance()->get_user()->login?>"><div class="img"><!--<img src="" alt="" />--></div>Страница компании</a></li><?php endif; ?>
								<?php endif; ?>

								<li class="last"><a href="<?=CI::site('user/logout')?>"><div class="img"><img src="<?=URL::site('images/pr3.png')?>" alt="" /></div>Выход</a></li>												
							</ul>
						</div>
					</div>	

				<?php endif ?>

					<span onclick="window.location='/article/help'" class="man menu cur_p">Помощь</span>

					<span onclick="window.location='http://feedback.yarmarka.biz/'" class="menu g1 cur_p">Техподдержка</span>
					<span onclick="window.location='http://job.yarmarka.biz/'" class="menu g1 cur_p">Вакансии</span>
					<span onclick="window.location='/article/reklama-na-saite-yarmarkabiz'" class="menu g1 cur_p">Реклама на сайте</span>
					<span onclick="window.location='/article/pravila-razmeshcheniya-obyavlenii'" class="menu g1 cur_p">Правила</span>

					<!--<a class="favorites menu" href="#">Избранное (0)</a>-->
				</div>	
				
				<!--/noindex-->
				
					</div>		
			</div>
			<? //Request::factory('block/plan_info')->execute()?>

		</div>
		
		<div class="active-bl z2"><div class="active-bl-bg">
			<div class="m_menu  iLight ">
				<span  class="choose iLight-nav"></span>
				<?=Request::factory('block/header_left_menu')->execute()?>
			</div>
			<?=Request::factory('block/header_search')->execute()?>
		</div></div>       
	</section><!--end header winner-->
</header>

