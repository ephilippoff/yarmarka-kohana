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
						<div>Телефон:<span class="tel">+7(3452)56-84-04</span></div>
						<div class="e-mail">E-mail: info@yarmarka.biz </div>
						<div class="adr">
						   <span class="locality"></span>
						   <span class="street-address">Адрес: г. Тюмень, ул. 50 лет ВЛКСМ,49, оф.302</span>
						</div>
						<div>
						</div>
					</address>
					<div class="copy">«Ярмарка» © <?=date('Y')?> </div>
				</div>
				<div class="right-b">
					<div class="sosial-site">
					<?php $city_id = Cookie::dget('location_city_id'); ?>
						<?php if ($city_id == 1948): ?>
							<a class="mr5 mb10 fl" href="https://vk.com/ya_vartovsk"><img src="/images/vkcom.png"></a>
						<?php else: ?>
							<a class="mr5 mb10 fl" href="https://vk.com/yarmarka_official"><img src="/images/vkcom.png"></a>
						<?php endif; ?>
						<a class="mr5 mb10 fl" href="https://twitter.com/yarmarkabiz"><img src="/images/twitter.png"></a>
						<a class="mr5 mb10 fl" href="https://www.facebook.com/yarmarka.official"><img src="/images/facebook.png"></a>
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
