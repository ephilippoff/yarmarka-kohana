<footer class="m_footer pfooter">
	<section class="winner">
		<nav class="fMenu">
			<div class="m_poll">
			<ul class="fl">
				<li><a href="http://feedback.yarmarka.biz">Техподдержка</a></li>
				<li><a href="<?=CI::site('add/step/1/0')?>">Подать объявление</a></li>
				<li><a href="http://job.yarmarka.biz/">Вакансии</a></li>
			</ul>
			<ul class="fr">
				<li><a href="<?=URL::site('user/userinfo')?>">Личный кабинет</a></li>
				<li><a href="<?=CI::site('user/registration')?>">Регистрация</a></li>
			</ul>
			</div>
		</nav>
		<div class="grey-footer">
			<div class="m_poll">
				<div class="left-b">
					<address>
						<div><?=Kohana::$config->load("info.tm.title")?></div>
						<div>Телефон: <span class="tel"><?=Kohana::$config->load("info.tm.phone")?></span></div>
						<div class="e-mail">E-mail: <?=Kohana::$config->load("info.tm.email")?></div>
						<div class="adr">
						   <span class="locality"></span>
						   <span class="street-address">Адрес: <?=Kohana::$config->load("info.tm.address")?></span>
						</div>
						<div>
						</div>
					</address>
					<div class="copy">«Ярмарка» © <?=date('Y')?> </div>
				</div>
				<div class="left-b">
					<address>
						<div><?=Kohana::$config->load("info.sg.title")?></div>
						<div>Телефон: <span class="tel"><?=Kohana::$config->load("info.sg.phone")?></span></div>
						<div class="e-mail">E-mail: <?=Kohana::$config->load("info.sg.email")?></div>
						<div class="adr">
						   <span class="locality"></span>
						   <span class="street-address">Адрес: <?=Kohana::$config->load("info.sg.address")?></span>
						</div>
						<div>
						</div>
					</address>
				</div>
				<div class="left-b">
					<address>
						<div><?=Kohana::$config->load("info.nv.title")?></div>
						<div>Телефон: <span class="tel"><?=Kohana::$config->load("info.nv.phone")?></span></div>
						<div class="e-mail">E-mail: <?=Kohana::$config->load("info.nv.email")?></div>
						<div class="adr">
						   <span class="locality"></span>
						   <span class="street-address">Адрес: <?=Kohana::$config->load("info.nv.address")?></span>
						</div>
						<div>
						</div>
					</address>
				</div>				
				<div class="right-b">
					<div class="sosial-site">
						<?php $city_id = Cookie::dget('location_city_id'); ?>
						<?php if ($city_id == 1948): ?>
							<a class="mr5 mb10 fl" href="https://vk.com/ya_vartovsk"><img src="/images/vkcom.png"></a>
						<?php else: ?>
							<a class="mr5 mb10 fl" href="https://vk.com/yarmarkasurgut"><img src="/images/vkcom.png"></a>
						<?php endif; ?>
						
						<a class="mr5 mb10 fl" href="https://twitter.com/yarmarka_biz"><img src="/images/twitter.png"></a>
						<a class="mr5 mb10 fl" href="https://www.facebook.com/Yarmarka.biz"><img src="/images/facebook.png"></a>
					</div>
					<div class="statistic">
					  <?=View::factory('layouts/counters')?>
					</div>
				</div>
				<div class="center-b">
				<?php if (FALSE) : ?>
					<div class="cont">
						<div class="col">
							<ul>
								<li><a href="">Бизнес, услуги, образование</a></li>
								<li><a href="">Домашние любимцы и растения</a></li>
								<li><a href="">Знакомства и общение</a></li>
							</ul>
						</div>
						<div class="col">
							<ul>
								<li>Бизнес, услуги, образование</li>
								<li>Домашние любимцы и растения</li>
								<li>Знакомства и общение</li>
							</ul>
						</div>
						<div class="col">
							<ul>
								<li>Бизнес, услуги, образование</li>
								<li>Домашние любимцы и растения</li>
								<li>Знакомства и общение</li>
							</ul>
						</div>
					</div>
				<?php endif; ?>
				</div>
				
			</div>
		</div>
		
	</section><!--end footer winner-->
</footer>
<?php 
if (Kohana::$profiling === TRUE) 
{
	echo View::factory('profiler/stats');
}
?>
