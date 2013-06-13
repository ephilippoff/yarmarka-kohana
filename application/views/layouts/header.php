<header class="m_header">
	<section class="winner">
		<div class="m_poll z3">
			<div class="top-line ">
				<div class="enter-bl logon iLight"><span class="personl-room iLight-nav"><span>Личный кабинет</span></span>
					<div class="proom-list iLight-cont">
						
							<ul>
								<li><a href="<?=CI::site('user/myads')?>"><div class="img"><img src="<?=URL::site('images/pr1.png')?>" alt="" /></div>Мои объявления</a></li>
								<li><a href="<?=CI::site('user/subscriptions')?>"><div class="img"><img src="" alt="" /></div>Мои подписки</a></li>
								<li><a href="<?=URL::site('user/profile')?>"><div class="img"><img src="<?=URL::site('images/pr2.png')?>" alt="" /></div>Профиль</a></li>
								<li><a href="<?=CI::site('user/favorites')?>"><div class="img"><img src="" alt="" /></div>Избранное</a></li>
								<li><a href="<?=CI::site('user/services_history')?>"><div class="img"><img src="" alt="" /></div>Счета</a></li>
								<li class="last"><a href="<?=CI::site('user/logout')?>"><div class="img"><img src="<?=URL::site('images/pr3.png')?>" alt="" /></div>Выход</a></li>
							</ul>
						
					</div>
				</div>
				
				<?=Request::factory('block/header_region')->execute()?>
				<?php if ($user = Auth::instance()->get_user()) : ?>
				<div class="who-are-yor"><span><?=$user->email?></span></div>	                    
				<?php endif; ?>
			</div>
			<div class="logo">
				<a href="http://<?=Region::get_current_domain()?>">
					<img src="<?=URL::site('images/logo.png')?>" alt="">
				</a>
				<span class="domen"><?=Region::get_current_domain()?></span>
			</div>
			<div class="btn-red big add-advert"><a href="<?=CI::site('add')?>"><span>Подать объявление</span></a></div>
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

