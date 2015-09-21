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

<a href="/khbackend/category/add" style="margin-bottom: 20px;display: inline-block;">Добавить рубрику</a>

<table class="table table-hover table-condensed articles">
	<thead>
		<tr>
			<th></th>
			<th>#</th>
			<th>title</th>
			<th>parent_id</th>
			<th>is_ready</th>
			<th>weight</th>
			<th>template</th>
			<th>use_template</th>
			<th>is_main</th>
			<th>main_menu_icon</th>
			<th>sinonim</th>
			<th>seo_name</th>
			<th>description</th>
			<th>max_count_for_user</th>
			<th>max_count_for_contact</th>
			<th>is_main_for_seo</th>
			<th>title_auto_fill</th>
			<th>text_required</th>
			<th>nophoto</th>
			<th>novideo</th>
			<th>main_menu_image</th>
			<th>submenu_template</th>
			<th>caption</th>
			<th>text_name</th>
			<th>rule</th>
			<th>show_map</th>			
			<th>address_required</th>
			<th>plan_name</th>
			<th>through_weight</th>
			<th>url</th>
			
			<th></th>
		</tr>
	</thead>

	<?php foreach ($categories as $category) : ?>
		<tr <?php if ($category->is_ready == 0) : ?> style="background-color: red;" <?php endif;?> >
			<td>
				<?php if ($count = $category->sub_categories->count_all()) : ?>
					<a class="icon-plus show_sub_categories" style="cursor:pointer" data-id="<?=$category->id?>" data-level="0"></a>
					<a class="icon-minus hide_sub_categories" style="cursor:pointer; display:none" data-id="<?=$category->id?>"></a>
				<?php endif ?>
			</td>
			<td><?=$category->id?></td>
			<td><?=$category->title?></td>
			<td><?=$category->parent_id?></td>
			<td><?=$category->is_ready?></td>
			<td><?=$category->weight?></td>
			<td><?=$category->template?></td>
			<td><?=$category->use_template?></td>
			<td><?=$category->is_main?></td>
			<td><?=$category->main_menu_icon?></td>
			<td><?=$category->sinonim?></td>
			<td><?=$category->seo_name?></td>
			<td><?=$category->description?></td>
			<td><?=$category->max_count_for_user?></td>
			<td><?=$category->max_count_for_contact?></td>
			<td><?=$category->is_main_for_seo?></td>
			<td><?=$category->title_auto_fill?></td>
			<td><?=$category->text_required?></td>
			<td><?=$category->nophoto?></td>
			<td><?=$category->novideo?></td>
			<td><?=$category->main_menu_image?></td>
			<td><?=$category->submenu_template?></td>
			<td><?=$category->caption?></td>
			<td><?=$category->text_name?></td>
			<td><?=$category->rule?></td>
			<td><?=$category->show_map?></td>
			<td><?=$category->address_required?></td>
			<td><?=$category->plan_name?></td>
			<td><?=$category->through_weight?></td>
			<td><?=$category->url?></td>
			
			<td>
				<a href="<?=URL::site('khbackend/category/edit/'.$category->id)?>" class="icon-pencil"></a>
				<?php if (!$count) : ?>
					<a href="<?=URL::site('khbackend/category/delete/'.$category->id)?>" class="icon-trash"></a>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>