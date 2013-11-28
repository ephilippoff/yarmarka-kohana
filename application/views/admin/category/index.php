<style>
.striped > div:nth-child(odd){
	background-color: #f9f9f9;
}
div.row {
	border-bottom: 1px dotted gray;
}

div.row:hover {
	background-color: #f5f5f5;
}
</style>

<script>
$(document).ready(function() {
	$('a').tooltip();

	$(document.body).on('click', '.show_sub_categories', function(e){
		e.preventDefault();
		$this = $(this);

		$.get('/khbackend/category/sub_categories/'+$(this).data('id'), {level:$(this).data('level')}, function(html) {
			$this.parents('.row').after(html);
			$this.hide();
			$this.parent().find('.hide_sub_categories').show();
		});
	});

	$(document.body).on('click', '.hide_sub_categories', function(e){
		e.preventDefault();

		$('.sub_categories_for_'+$(this).data('id')).remove();
		$(this).hide();
		$(this).parent().find('.show_sub_categories').show();
	});
});
</script>

<div class="row">
	<div class="span1"></div>
	<div class="span1"><b>#</b></div>
	<div class="span4"><b>Title</b></div>
	<div class="span4"><b>Seo name</b></div>
</div>
<div class="striped">
<?php foreach ($categories as $category) : ?>
<div class="row">
	<div class="span1">
		<?php if ($category->sub_categories->count_all()) : ?>
		<a class="icon-plus show_sub_categories" style="cursor:pointer" data-id="<?=$category->id?>" data-level="0"></a>
		<a class="icon-minus hide_sub_categories" style="cursor:pointer; display:none" data-id="<?=$category->id?>"></a>
		<?php endif ?>
	</div>
	<div class="span1"><?=$category->id?></div>
	<div class="span4"><?=$category->title?></div>
	<div class="span4"><?=$category->seo_name?></div>
	<div class="span2">
		<a href="<?=URL::site('khbackend/category/edit/'.$category->id)?>" class="icon-pencil"></a>
	</div>
</div>
<?php endforeach; ?>
</div>