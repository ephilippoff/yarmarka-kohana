<ul>
	<?php foreach ($articles as $article) : ?>
		<?php if (Request::initial()->param('seo_name') == $article->seo_name) : ?>
			<li><a><?=$article->title?></a></li>
		<?php elseif ($article->is_category) : ?>
			<li><h4><?=$article->title?></h4></li>
		<?php else : ?>
			<li><a href="<?=URL::site(Route::get('article')->uri(array('seo_name' => $article->seo_name)))?>">
				<?=$article->title?>
			</a></li>
		<?php endif; ?>
		<?php $articles = $article->articles->find_all() ?>

		<?php if ($articles->count() > 0) : ?>
			<?=View::factory('block/articles_menu_item', array('articles' => $articles))?>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>