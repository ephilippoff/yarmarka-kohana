<a href="/khbackend/seopatterns/add" style="margin-bottom: 20px;display: inline-block;">Добавить pattern</a>

<table class="table table-hover table-condensed articles">
<tr>
	<th>id</th>
	<th>category</th>
	<th>params</th>
	<th>h1</th>
	<th>title</th>
	<th>description</th>
	<th>footer</th>
	<th>keywords</th>
	<th>anchor</th>
	<th></th>
</tr>
<?php foreach ($list as $item) : ?>
<tr>
	<td><?=$item->id?></td>
	<td><?=$item->category_title?> (<?=$item->category_id?>)</td>
	<td><?=htmlspecialchars($item->params)?></td>
	<td><?=htmlspecialchars($item->h1)?></td>
	<td><?=htmlspecialchars($item->title)?></td>
	<td><?=htmlspecialchars($item->description)?></td>
	<td><?=htmlspecialchars($item->footer)?></td>
	<td><?=htmlspecialchars($item->keywords)?></td>
	<td><?=htmlspecialchars($item->anchor)?></td>
	<td>
		<a href="<?=URL::site('khbackend/seopatterns/edit/'.$item->id)?>" class="icon-pencil"></a>
		<a href="<?=URL::site('khbackend/seopatterns/delete/'.$item->id)?>" class="icon-trash"></a>
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