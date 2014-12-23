<div class="cont iLight-cont">
	<div class="left">
		<ul class="top" role="menu">
			<?php foreach ($categories1l as $category) : ?>
			<?php if ($category->seo_name == 'modulnaya-reklama') continue; ?>
			<li data-submenu-id="section<?=$category->id?>">
				<a href="<?=$category->get_url()?>">
					<span class="name"><?=$category->title?></span>
					<span class="heading"></span>
				</a>
			</li>
			<?php endforeach; ?>
		</ul>
		<ul class="bottom">
			<li><a href="/article/soveti"><span class="name">Советы от Ярмарки</span></a></li>
		</ul>		
	</div> 
	
	<div class="right">
		<?php foreach ($categories1l as $category1) : 
				$divWidth = 341;
				$ulWidth = 191;
				//$source = isset($source) ? $source : '';
		?>
				<div class="section section<?=$category1->id?>" id="section<?=$category1->id?>" style="width: <?=$divWidth;?>px">
					<ul style="position: absolute;width:<?=$ulWidth?>px">
						<li class="li-title"><span class="header3" style="width: 165px"><?=$category1->title?></span></li>					
						<?php foreach ($categories2l as $category2) : ?>
						<?php if ($category2->parent_id != $category1->id) continue; ?>
						<li>
							<a href="<?=$category2->get_url()?>" >
								<span class="name2"><?=$category2->title?></span><span class="heading"><?=$category2->caption?></span>
							</a>
						</li>					
						<?php endforeach;?>
					</ul>
				</div>
		<?php endforeach;?>
	</div>	
</div>
