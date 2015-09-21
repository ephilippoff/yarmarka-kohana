<a href="/khbackend/object_reasons/add" style="margin-bottom: 20px;display: inline-block;">Добавить причину</a>

<table class="table table-hover table-condensed articles">
<thead>
	<tr>
		<th>id</th>
		<th>full_text</th>
		<th></th>
	</tr>
</thead>
<?php foreach ($list as $item) : ?>
<tr>
	<td><?=$item->id?></td>
	<td><?=$item->full_text?></td>
	<td>
		<a href="<?=URL::site('khbackend/object_reasons/edit/'.$item->id)?>" class="icon-pencil"></a>
		<a href="<?=URL::site('khbackend/object_reasons/delete/'.$item->id)?>" class="icon-trash"></a>
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