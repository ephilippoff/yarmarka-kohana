<div class="winner article ourservices">
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		scroll_fixed($('.fn-right-menu'));
	});
</script>	
	<section class="main-cont ">
		<div class="m_poll">
			<div class="crumbs" style="margin: 4px 0 6px;">
				<?=Request::factory('block/ourservices_breadcrumbs/'.$article->id)->execute()?>
			</div>
			<div class="shadow-top fl100 pt7"></div>
		
			<section class="main-section main-section-ourservices">	                			
				<div class="innerPage">

					<aside class="iPage-rightAside">
						<?=$menu?>
					</aside>

					<div class="innerPage-cont iPage-rightPadding">
						<article class="iPage-article">
							<h1 class="big" style="text-align: center;"><?=$article->title?></h1>
							<div class="text-cont"><?=$article->text?></div>							
						</article>
					
					</div>
					
				</div>
			</section><!--end main-cont section-->
		</div>
		
		
	</section>
</div><!--end content winner-->