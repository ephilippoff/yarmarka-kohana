<?php foreach ($articles as $article) : ?>
	<h2 style="margin-top: 8px;"><?=$article->title?></h2><br/>

	<article class="rubric">
	<?php foreach ($article->articles->find_all() as $article) : ?>
		<?php if ($article->is_category) : ?>
			<h4><?=$article->title?></h4>
		<?php else : ?>
			<h4><a href="<?=URL::site(Route::get('article')->uri(array('seo_name' => $article->seo_name)))?>"><?=$article->title?></a></h4>
		<?php endif; ?>

		<ul>
		<?php foreach ($article->articles->find_all() as $article) : ?>
			<?php if ($article->is_category) : ?>
				<li><?=$article->title?></li>
			<?php else : ?>
				<li><a href="<?=URL::site(Route::get('article')->uri(array('seo_name' => $article->seo_name)))?>"><?=$article->title?></a></li>
			<?php endif; ?>
		<?php endforeach; ?>
		</ul>

	<?php endforeach; ?>
	</article>

<?php endforeach; ?>