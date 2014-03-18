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
					<div class="innerPage-cont iPage-rightPadding">
						<article class="iPage-article">
							<h1 class="big" style="text-align: center;"><?=$article->title?></h1>
							<div class="text-cont"><?=$article->text?></div>
								<?php if ($article->is_category == 0) : ?>			
											<div id="hypercomments_widget"></div>
											<script type="text/javascript">
											_hcwp = window._hcwp || [];
											_hcwp.push({widget:"Stream", widget_id:16227});
											(function() {
											if("HC_LOAD_INIT" in window)return;
											HC_LOAD_INIT = true;
											var lang = (navigator.language || navigator.systemLanguage || navigator.userLanguage || "en").substr(0, 2).toLowerCase();
											var hcc = document.createElement("script"); hcc.type = "text/javascript"; hcc.async = true;
											hcc.src = ("https:" == document.location.protocol ? "https" : "http")+"://w.hypercomments.com/widget/hc/16227/"+lang+"/widget.js";
											var s = document.getElementsByTagName("script")[0];
											s.parentNode.insertBefore(hcc, s.nextSibling);
											})();
											</script>
											<a href="http://hypercomments.com" class="hc-link" title="comments widget">comments powered by HyperComments</a>							
								<?php endif; ?>								
						</article>
						<?php if ($article->is_category) : ?>
							<ul class="iPage-ul">
								<?php foreach ($article->articles->find_all() as $article) : ?>
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