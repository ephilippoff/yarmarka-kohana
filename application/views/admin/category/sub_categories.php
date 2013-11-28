<?php $margin = ($level == 1) ? '0px' : $level.'%'; ?>
<?php foreach ($categories as $category) : ?>
<div class="row sub_categories_for_<?=$parent_id?>" style="border-left: 1px solid black; margin-left: <?=$margin?>">
	<div class="span1">
		<?php if ($category->sub_categories->count_all()) : ?>
		<a class="icon-plus show_sub_categories" style="cursor:pointer" data-id="<?=$category->id?>" data-level="<?=$level?>"></a>
		<a class="icon-minus hide_sub_categories" style="cursor:pointer; display:none" data-id="<?=$category->id?>"></a>
		<?php endif ?>
	</div>
	<div class="span1"><?=$category->id?></div>
	<div class="span4"><?=$category->title?></div>
	<div class="span2"><?=$category->seo_name?></div>
	<div class="span2">
		<a href="<?=URL::site('khbackend/category/edit/'.$category->id)?>" class="icon-pencil"></a>
	</div>
</div>
<?php endforeach; ?>