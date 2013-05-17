<div class="region iLight">
	<span>Ваш регион:</span>
	<?php if ($city) : ?>
	<span class="iLight-nav thref" ><?=$city->title?></span>
	<?php else : ?>
	<span class="iLight-nav thref" ><?=$region->title?></span>
	<?php endif; ?>
	<div class="choose-your-region iLight-cont">
		<div class="col">
			<ul>
			<?php foreach ($cities as $i => $city) : ?>
				<li><a href="<?=$city->get_url()?>"><?=$city->title?></a></li>
			<?php if (($i+1)%8 == 0) : ?>
			</ul>
		</div>
		<div class="col">
			<ul>
			<?php endif; ?>
			<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>	
