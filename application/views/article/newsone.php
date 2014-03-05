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
									<li><span><?=date('d.m', strtotime($other->created))?></span> <a rel="nofollow" href="<?=URL::site(Route::get('newsone')->uri(array('id' => $other->id, 'seo_name' => $other->seo_name))).$query_uri?>"><?=$other->title?></a></li>
							<?php endforeach; ?>
							
						</ul>
					</aside>
					<?php endif; ?>
					<div class="innerPage-cont iPage-rightPadding">
						<article class="iPage-article">
							<?php if ($newsone->is_category == 0) : ?><span class="news-created"><?=date('d.m.Y', strtotime($newsone->created))?></span><?php endif; ?>
							<h1 class="big" style="text-align: left;"><?=$newsone->title?></h1>							
								<?php if (!empty($real_photo)) : ?>
									<div class="photo-cont">							
										<img class="news-photo" src="<?=$real_photo?>" alt="<?=strip_tags($newsone->photo_comment)?>" title="<?=strip_tags($newsone->photo_comment)?>" >
										<div class="photo-comment"><?=strip_tags($newsone->photo_comment, '<p><br>')?></div>
									</div>
								<?php endif; ?>									
							<div class="text-cont"><?=$newsone->text?></div>
						</article>
						<?php if ($newsone->is_category) : ?>
							<ul class="iPage-ul news-list">
								<?php foreach ($newsone->articles->order_by('is_category', 'desc')->order_by('created', 'desc')->find_all() as $article) : ?>
									<?php $query_uri = '?em_client_email=noreply@yarmarka.biz&em_campaign_id=4&em_campaign_name=newsone_'.$article->id ?>
									<li><?php if ($article->is_category == 0) : ?><span><?=date('d.m', strtotime($article->created))?></span><?php endif; ?> <a rel="nofollow" href="<?=URL::site(Route::get('newsone')->uri(array('id' => $article->id, 'seo_name' => $article->seo_name))).$query_uri?>"><?=$article->title?></a></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>						
					</div>
					
				</div>
			</section><!--end main-cont section-->
					
		</div>
		
		
	</section>
</div><!--end content winner-->