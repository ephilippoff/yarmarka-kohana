<footer class="m_footer pfooter">
	<section class="winner">
		<nav class="fMenu">
			<div class="m_poll">
			<ul class="fl">
				<li><a href="http://feedback.yarmarka.biz">Техподдержка</a></li>
				<li><a href="<?=CI::site('contacts')?>">Контакты</a></li>
				<li><a href="<?=CI::site('job')?>">Наши вакансии</a></li>
				<li><a href="<?=CI::site('sitemap')?>">Карта сайта</a></li>
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
						<div>Телефон:<span class="tel">+7(3452)49-21-21</span></div>
						<div class="e-mail">E-mail: info@tmn.yarmarka.biz </div>
						<div class="adr">
						   <span class="locality"></span>
						   <span class="street-address">Адрес: ул. 50 лет ВЛКСМ,49, оф.302</span>
						</div>
						<div><a href="<?=CI::site('contacts')?>" class="see-on-map">Как нас найти</a></div>
					</address>
					<div class="copy">«Ярмарка» © 2013 </div>
				</div>
				<div class="right-b">
				<?php if (FALSE) : ?>
					<div class="sosial-site"><img src="<?=URL::site('img/ss.png')?>" alt=""></div>
					<div class="statistic"><img src="<?=URL::site('img/statistic.png')?>" alt=""></div>
				<?php endif; ?>
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
