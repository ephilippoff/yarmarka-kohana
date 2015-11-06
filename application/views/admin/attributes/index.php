<a href="/khbackend/attributes/add" style="margin-bottom: 20px;display: inline-block;">Добавить атрибут</a>
<script>
	$(document).ready(function(){

		$(".js-update").click(function(e){
			e.preventDefault();
			var $el = $(e.currentTarget);
			var id = $el.data("id");
			$.get('/khbackend/category/update_attribute/'+id, {}, function(result) {
				console.log(result)
			});
		});

	});
	
</script>
<table class="table table-hover table-condensed articles">
<tr>
	<th>id</th>
	<th>title</th>
	<th>solid_size</th>
	<th>frac_size</th>
	<th>prefix</th>
	<th>unit</th>
	<th>max_text_length</th>
	<th>is_textarea</th>
	<th>type</th>
	<th>comment</th>
	<th>is_prefix</th>
	<th>is_unit</th>
	<th>parent</th>
	<th>is_price</th>
	<th>is_descr</th>
	<th>id_tr</th>
	<th>seo_name</th>
	<th></th>
</tr>
<?php foreach ($list as $item) : ?>
<tr>
	<td><?=$item->id?></td>
	<td><?=$item->title?></td>
	<td><?=$item->solid_size?></td>
	<td><?=$item->frac_size?></td>
	<td><?=$item->prefix?></td>
	<td><?=$item->unit?></td>
	<td><?=$item->max_text_length?></td>
	<td><?=$item->is_textarea?></td>
	<td><?=$item->type?></td>
	<td><?=$item->comment?></td>
	<td><?=$item->is_prefix?></td>
	<td><?=$item->is_unit?></td>
	<td><?=$item->parent?></td>
	<td><?=$item->is_price?></td>	
	<td><?=$item->is_descr?></td>
	<td><?=$item->id_tr?></td>
	<td><?=$item->seo_name?></td>
	<td>
		<a href="<?=URL::site('khbackend/attributes/edit/'.$item->id)?>" class="icon-pencil"></a>
		<a href="<?=URL::site('khbackend/attributes/delete/'.$item->id)?>" class="icon-trash"></a>
	</td>
	<td>
		<? if ($item->type == 'list'): ?>
			<a class="span-link js-update" data-id="<?=$item->id?>">Обновить сео</a>
		<? endif; ?>
	</td>
</tr>
<?php endforeach; ?>
</table>

<?php if ($pagination->total_pages > 1) : ?>
<div class="row">
	<div class="span10"><?=$pagination?></div>
	<div class="span2" style="padding-top: 55px;">
		<span class="text-info">Limit:</span>
		<?php foreach (array(50, 100, 150) as $l) : ?>
			<?php if ($l == $limit) : ?>
				<span class="badge badge-info"><?=$l?></span>
			<?php else : ?>
				<a href="#" class="btn-mini" onClick="add_to_query('limit', <?=$l?>)"><?=$l?></a>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>