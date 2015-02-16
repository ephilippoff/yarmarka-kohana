<div class="winner article newsone">
	<section class="main-cont ">
		<div class="m_poll">
			<div class="crumbs" style="margin: 4px 0 15px;">
				<?=Request::factory('block/newsone_breadcrumbs/'.$newsone->id)->execute()?>
			</div>
			<aside class="w200 innerPage-leftAside news-rubrics">
				<?php foreach ($news_rubrics as $key => $rubric) : ?>
						<p><a rel="nofollow" href="<?=URL::site(Route::get('newsone')->uri(array('id' => $rubric->id, 'seo_name' => $rubric->seo_name)))?>"><?=$rubric->title?></a></p>
				<?php endforeach;?>
			</aside><!--end main-cont aside-->
			
			<section class="main-section iPage-leftPading">	                			
				<div class="innerPage">
					<?php if ($count_other_news) : ?>
					<aside class="iPage-rightAside other-news">
						<h2 style="margin-top:3px;">Другие новости</h2>
						<br/>
						<ul>
							<?php foreach ($other_news as $key => $other) : ?>
									<?php $query_uri = '?em_client_email=noreply@yarmarka.biz&em_campaign_id=4&em_campaign_name=newsone_'.$other->id ?>
									<li><span><?=date('d.m', strtotime($other->start_date))?></span> <a rel="nofollow" href="<?=URL::site(Route::get('newsone')->uri(array('id' => $other->id, 'seo_name' => $other->seo_name))).$query_uri?>"><?=$other->title?></a></li>
							<?php endforeach; ?>
							
						</ul>
					</aside>
					<?php endif; ?>
					<div class="innerPage-cont iPage-rightPadding">
						<article class="iPage-article">
							<?php if ($newsone->is_category == 0) : ?><span class="news-created"><?php if ($newsone->start_date) : ?><?=date('d.m.Y', strtotime($newsone->start_date))?><?php endif; ?></span><?php endif; ?>
							<h1 class="big" style="text-align: left;"><?=$newsone->title?></h1>							
								<?php if (!empty($real_photo)) : ?>
									<div class="photo-cont">							
										<img class="news-photo" src="<?=$real_photo?>" alt="<?=strip_tags($newsone->photo_comment)?>" title="<?=strip_tags($newsone->photo_comment)?>" >
										<div class="photo-comment"><?=strip_tags($newsone->photo_comment, '<p><br>')?></div>
									</div>
								<?php endif; ?>									
							<div class="text-cont"><?=$newsone->text?></div>
								<?php if ($newsone->is_category == 0) : ?>			
											<div id="hypercomments_widget"></div>
											<script type="text/javascript">
											_hcwp = window._hcwp || [];
											_hcwp.push({widget:"Stream", widget_id:16227, callback: function(app, init){$('#hypercomments_widget .hc_count_user_online').remove();} });
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
						<?php if ($newsone->is_category) : ?>
							<ul class="iPage-ul news-list">
								<?php 
								$news_list = ORM::factory('Article')
												->where('text_type', '=', 2)
												->where('is_category', '=', 0)
												->where('is_visible', '=', 1)
												->where('parent_id', '=', $newsone->id)
												->where('start_date', '<', DB::expr('now()'))
												->where('end_date', '>', DB::expr('now()'))
												->order_by('start_date', 'desc')
												->find_all();
								?>
								<?php foreach ($news_list as $article) : ?>
									<?php $query_uri = '?em_client_email=noreply@yarmarka.biz&em_campaign_id=4&em_campaign_name=newsone_'.$article->id ?>
									<li><?php if ($article->is_category == 0) : ?><span><?=date('d.m', strtotime($article->start_date))?></span><?php endif; ?> <a rel="nofollow" href="<?=URL::site(Route::get('newsone')->uri(array('id' => $article->id, 'seo_name' => $article->seo_name))).$query_uri?>"><?=$article->title?></a></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>						
					</div>
					
				</div>
			</section><!--end main-cont section-->
					
		</div>
		
		
	</section>
</div><!--end content winner-->