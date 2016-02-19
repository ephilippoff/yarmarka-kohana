<div class="winner article">
	<section class="main-cont ">
		<div class="m_poll">
			<div class="crumbs" style="margin: 4px 0 6px;">
				<?=Request::factory('block/articles_breadcrumbs/'.$article->id)->execute()?>
			</div>
			<div class="shadow-top fl100 pt7"></div>
			<aside class="w200 innerPage-leftAside">
				<h2 style="margin-top: 8px;">Все статьи</h2>
				<br>
				<?=HTML::render_menu($articles);?>
			</aside><!--end main-cont aside-->
			
			<section class="main-section iPage-leftPading">	                			
				<div class="innerPage">
					<div class="innerPage-cont iPage-rightPadding">
						<article class="iPage-article">
							<h1 class="big" style="text-align: center;"><?=$article->title?></h1>
							<div class="text-cont"><?=$article->text?></div>							
						</article>
						<?php if ($article->is_category) : ?>
							<ul class="iPage-ul">
								<?php foreach ($article->articles->where('is_visible', '=', 1)->find_all() as $article) : ?>
									<li><a href="<?=URL::site(Route::get('article')->uri(array('seo_name' => $article->seo_name)))?>"><?=$article->title?></a></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>						
					</div>
					
				</div>
			</section><!--end main-cont section-->
		</div>
		
		
	</section>
</div><!--end content winner-->