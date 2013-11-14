<div class="cont iLight-cont">
	<div class="left">
		<ul class="top" role="menu">
			<?php foreach ($categories as $category) : ?>
			<li data-submenu-id="section1">
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
</div>
