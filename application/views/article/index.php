<div class="winner">
	<section class="main-cont ">
		<div class="m_poll">
			<div class="crumbs" style="margin: 4px 0 6px;">
				<?=Request::factory('block/articles_breadcrumbs/'.$article->id)->execute()?>
			</div>
			<div class="shadow-top fl100 pt7"></div>
			<aside class="w200 innerPage-leftAside">
				<?=Request::factory('block/articles_menu')->execute()?>
			</aside><!--end main-cont aside-->
			
			<section class="main-section iPage-leftPading">	                			
				<div class="innerPage">
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
					<div class="innerPage-cont iPage-rightPadding">
						<?=$article->text?>
					</div>
					
				</div>
			</section><!--end main-cont section-->
		</div>
		
		
	</section>
</div><!--end content winner-->