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

		$.get('/khbackend/category/sub_structure/'+$(this).data('id'), {level:$(this).data('level')}, function(html) {
			$this.parents('tr').after(html);
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

<a href="/khbackend/category/addstructure" style="margin-bottom: 20px;display: inline-block;">Добавить рубрику</a>

<table class="table table-hover table-condensed articles">
	<thead>
		<tr>
			<th></th>
			<th>#</th>
			<th>title</th>
			<th>url</th>
			<th></th>
		</tr>
	</thead>

	<?php foreach ($categories as $category) : ?>
		<tr >
			<td>
				<?php if ($count = $category->sub_structure->count_all()) : ?>
					<a class="icon-plus show_sub_categories" style="cursor:pointer" data-id="<?=$category->id?>" data-level="0"></a>
					<a class="icon-minus hide_sub_categories" style="cursor:pointer; display:none" data-id="<?=$category->id?>"></a>
				<?php endif ?>
			</td>
			<td>#<?=$category->id?>, вес <?=$category->weight?></td>
			<td><?=$category->title?></td>
			<td><a href="/<?=$category->url?>" target="_blank"> <?=$category->url?></a></td>
			<td>

				<a href="/khbackend/category/addstructure?parent_id=<?=$category->id?>" class="icon-plus"></a>
				<a href="<?=URL::site('khbackend/category/editstructure/'.$category->id)?>" class="icon-pencil"></a>
				<?php if (!$count) : ?>
					<a href="<?=URL::site('khbackend/category/deletestructure/'.$category->id)?>" class="icon-trash"></a>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>