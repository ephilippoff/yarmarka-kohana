<a href="/khbackend/references/add" style="margin-bottom: 20px;display: inline-block;">Добавить reference</a>

<table class="table table-hover table-condensed articles">
<tr>
	<th>id</th>
	<th>category</th>
	<th>attribute</th>
	<th>weight</th>
	<th>is_required</th>
	<th>is_title</th>
	<th>is_main</th>
	<th>attribute_cols_count</th>
	<th>is_seo_used</th>
	<th>is_selectable</th>
	<th></th>
</tr>
<?php foreach ($list as $item) : ?>
<tr>
	<td><?=$item->id?></td>
	<td><?=$item->category_title?> (<?=$item->category?>)</td>
	<td><?=$item->attribute_title?> (<?=$item->attribute?>)</td>
	<td><?=$item->weight?></td>
	<td><?=$item->is_required?></td>
	<td><?=$item->is_title?></td>
	<td><?=$item->is_main?></td>
	<td><?=$item->attribute_cols_count?></td>
	<td><?=$item->is_seo_used?></td>
	<td><?=$item->is_selectable?></td>
	<td>
		<a href="<?=URL::site('khbackend/references/edit/'.$item->id)?>" class="icon-pencil"></a>
		<a href="<?=URL::site('khbackend/references/delete/'.$item->id)?>" class="icon-trash"></a>
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