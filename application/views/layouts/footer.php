<footer class="m_footer pfooter">
	<section class="winner">
		<nav class="fMenu">
			<div class="m_poll">
			<ul class="fl">
				<li><a href="http://feedback.yarmarka.biz">Техподдержка</a></li>
				<li><a href="<?=CI::site('add/step/1/0')?>">Подать объявление</a></li>
				<li><a href="/<?php echo Route::get('article')->uri(array('seo_name' => 'reklamodatelyam'))?>">Рекламодателям</a></li>
			</ul>
			<ul class="fr">
				<li><a href="<?=URL::site('user/profile')?>">Личный кабинет</a></li>
				<li><a href="<?=CI::site('user/registration')?>">Регистрация</a></li>
			</ul>
			</div>
		</nav>
		<div class="grey-footer">
			<div class="m_poll">
				<div class="left-b">
					<address>
						<div>Телефон:<span class="tel">+7(3452)492-100</span></div>
						<div class="e-mail">E-mail: info@yarmarka.biz </div>
						<div class="adr">
						   <span class="locality"></span>
						   <span class="street-address">Адрес: г. Тюмень, ул. 50 лет ВЛКСМ,49, оф.302</span>
						</div>
						<div>
						</div>
					</address>
					<div class="copy">«Ярмарка» © 2013 </div>
				</div>
				<div class="right-b">
					<div class="sosial-site">
						<a class="mr5 mb10 fl" href="https://vk.com/gazeta_yarmarka"><img src="/images/vkcom.png"></a>
						<a class="mr5 mb10 fl" href="https://twitter.com/yarmarka_biz"><img src="/images/twitter.png"></a>
						<a class="mr5 mb10 fl" href="https://www.facebook.com/Yarmarka.biz"><img src="/images/facebook.png"></a>
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
