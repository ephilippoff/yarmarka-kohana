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
			<li><a href="/add"><span class="name">Подать объявление</span></a></li> 
			<li><a href="/article/soveti"><span class="name">Советы от Ярмарки</span></a></li>
		</ul>		
	</div> 
	
	<div class="right">
		<?php foreach ($categories1l as $category1) : 
				$divWidth = (isset($banners[$category1->id]) and (int)$banners[$category1->id]->menu_width) ? (int)$banners[$category1->id]->menu_width : 341;
				$ulWidth = 191;
				//$source = isset($source) ? $source : '';
		?>
				<?php if (in_array($category1->id, $parents_ids)) : ?>
		
					<div class="section section<?=$category1->id?>" id="section<?=$category1->id?>" style="width: <?=$divWidth;?>px">

					<?php  if (isset($banners[$category1->id]) and is_file($_SERVER['DOCUMENT_ROOT'].'/uploads/banners/menu/'.$banners[$category1->id]->image)) : //Если есть баннер, то выводим его ?>

								<img hidefocus="true" style="position: absolute;right:<?=$banners[$category1->id]->x?>px ; bottom:<?=$banners[$category1->id]->y?>px;" usemap="#map<?=$category1->id?>" src="/uploads/banners/menu/<?=$banners[$category1->id]->image?>"/>				

								<map name="map<?=$category1->id?>" id="map<?=$category1->id?>">
									<area shape="poly" coords="<?=$banners[$category1->id]->map_params?>" class="" href="<?=URL::site('redirect/ref_cb').'?id='.$banners[$category1->id]->id ?>" target="_blank" />
								</map>	

					<?php	elseif (is_file($_SERVER['DOCUMENT_ROOT'].$category1->main_menu_image)) : //иначе картинка по умолчанию, если есть ?>

								<span class="img">
									<img src="<?=$category1->main_menu_image?>"/>
								</span>

					<?php	endif; ?>						

						<ul style="position: absolute;width:<?=$ulWidth?>px">
							<li class="li-title"><span class="header3" style="width: 165px"><?=$category1->title?></span></li>					
							<?php foreach ($categories2l as $category2) : ?>
							<?php if ($category2->parent_id != $category1->id) continue; ?>
							<li>
								<span class="menu-span-link" onclick="window.location='<?=$category2->get_url()?>'">
									<span class="name2"><?=$category2->title?></span><span class="heading"><?=$category2->caption?></span>
								</span>
							</li>					
							<?php endforeach;?>
						</ul>
					</div>
		
				<?php endif; ?>
		
		<?php endforeach;?>
	</div>	
</div>
