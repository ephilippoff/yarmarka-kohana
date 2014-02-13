<div class="winner article newsone">
	<section class="main-cont ">
		<div class="m_poll">
			<div class="crumbs" style="margin: 4px 0 6px;">
				<?=Request::factory('block/newsone_breadcrumbs/'.$newsone->id)->execute()?>
			</div>
			<div class="shadow-top fl100 pt7"></div>
			<aside class="w200 innerPage-leftAside">
				<h2 style="margin-top: 8px;">Рубрики новостей</h2>
				<br>
				<?php foreach ($news_rubrics as $key => $rubric) : ?>
						<p><a href="<?=URL::site(Route::get('newsone')->uri(array('seo_name' => $rubric->seo_name)))?>"><?=$rubric->title?></a></p>
				<?php endforeach;?>
			</aside><!--end main-cont aside-->
			
			<section class="main-section iPage-leftPading">	                			
				<div class="innerPage">
					<?php if ($count_other_news) : ?>
					<aside class="iPage-rightAside other-news">
						<h2>Другие новости</h2>
						<br/>
						<ul>
							<?php foreach ($other_news as $key => $other) : ?>
									<li><span><?=date('d.m', strtotime($other->created))?></span> <a href="<?=URL::site(Route::get('newsone')->uri(array('seo_name' => $other->seo_name)))?>"><?=$other->title?></a></li>
							<?php endforeach; ?>
							
						</ul>
					</aside>
					<?php endif; ?>
					<div class="innerPage-cont iPage-rightPadding">
						<article class="iPage-article">
							<h1 class="big" style="text-align: center;"><?=$newsone->title?></h1>							
								<?php if (!empty($real_photo)) : ?>
									<div class="photo-cont">							
										<img class="news-photo" src="<?=$real_photo?>" alt="<?=strip_tags($newsone->photo_comment)?>" title="<?=strip_tags($newsone->photo_comment)?>" >
										<div class="photo-comment"><?=strip_tags($newsone->photo_comment, '<p><br>')?></div>
									</div>
								<?php endif; ?>									
							<?=$newsone->text?>
						</article>
						<?php if ($newsone->is_category) : ?>
							<ul class="iPage-ul">
								<?php foreach ($newsone->articles->order_by('is_category', 'desc')->order_by('created', 'desc')->find_all() as $article) : ?>
									<li><a href="<?=URL::site(Route::get('newsone')->uri(array('seo_name' => $article->seo_name)))?>"><?=$article->title?></a></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>						
					</div>
					
				</div>
			</section><!--end main-cont section-->
					
		</div>
		
		
	</section>
</div><!--end content winner-->