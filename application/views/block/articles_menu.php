	<h4 style="margin-top: 8px;"><?=$top_parent->title?></h2><br/>

	<article class="rubric">
	<?php foreach ($top_parent->articles->find_all() as $article) : ?>
		<?php if ($article->is_category OR $article->seo_name == Request::initial()->param('seo_name')) : ?>
			<h4><?=$article->title?></h4>
		<?php else : ?>
			<h4><a href="<?=URL::site(Route::get('article')->uri(array('seo_name' => $article->seo_name)))?>"><?=$article->title?></a></h4>
		<?php endif; ?>

		<ul>
		<?php foreach ($article->articles->find_all() as $article) : ?>
			<?php if ($article->is_category OR $article->seo_name == Request::initial()->param('seo_name')) : ?>
				<li><?=$article->title?></li>
			<?php else : ?>
				<li><a href="<?=URL::site(Route::get('article')->uri(array('seo_name' => $article->seo_name)))?>"><?=$article->title?></a></li>
			<?php endif; ?>
		<?php endforeach; ?>
		</ul>

	<?php endforeach; ?>
	</article>