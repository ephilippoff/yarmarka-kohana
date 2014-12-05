<div class="level">
	<ul>
		<? foreach ($hierarchy_filters as $filter) : ?>
			<li>
				+ <span class="link" onclick="_pricecontrol.price_search_hierarchy(this, <?=$filter->id?>,<?=$filter->priceload_attribute_id?>)"><?=$filter->title?> (<?=$filter->count?>)</span>
			</li>
		<? endforeach; ?>						
	</ul>
</div>