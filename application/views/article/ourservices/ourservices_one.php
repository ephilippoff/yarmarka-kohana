<div class="winner article">
	<section class="main-cont ">
		<div class="m_poll">
			<div class="crumbs" style="margin: 4px 0 6px;">
				<?=Request::factory('block/ourservices_breadcrumbs/'.$article->id)->execute()?>
			</div>
			<div class="shadow-top fl100 pt7"></div>
		
			<section class="main-section main-section-ourservices">	                			
				<div class="innerPage">
					<?php if (FALSE) : ?>
					<aside class="iPage-rightAside">
						<h2>Полезные ссылки</h2>
						<br/>
						<ul>
							<li><a href="">FAQ</a></li>
							<li><a href="">Помощь по сайту</a></li>
							<li><a href="">Зарегистрироваться</a></li>
							<li><a href="">ДОНАТ ДОНАТ</a></li>
							<li><a href="">Мы любим индууууур</a></li>
						</ul>
					</aside>
					<?php endif; ?>
					<div class="innerPage-cont">
						<article class="iPage-article">
							<h1 class="big" style="text-align: center;"><?=$article->title?></h1>
							<div class="text-cont">
								<?=$article->text?>
							</div>							
						</article>
					
					</div>
					
				</div>
			</section><!--end main-cont section-->
		</div>
		
		
	</section>
</div><!--end content winner-->