<div class="cont">
	<?php foreach ($breadcrumbs as $key => $crumb) : ?>
		<?php if ($key+1 == count($breadcrumbs)) : ?>
			<span class="current"><?=$crumb['anchor']?></span>
		<?php else : ?>
			<a rel="nofollow" href="<?=URL::site($crumb['url'])?>"><?=$crumb['anchor']?></a><span> ></span>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
