<style type="text/css">
	.link, .query_string{max-width: 300px; word-wrap: break-word;}
</style>

<table class="table table-hover table-condensed articles">
<thead>
	<tr>
		<th>id</th>
		<th class="link">link</th>
		<th>last_update</th>
		<th>user_id</th>
		<th>title</th>
		<th>city_id</th>
		<th>category_id</th>
		<th class="query_string">query_string</th>
		<th>region_id</th>
		<th>action_id</th>
		<th>period</th>
		<th>next_date_to_send</th>
		<th>hash</th>
		<th>created</th>
		<th>search_cache_id</th>
	</tr>
</thead>
<?php foreach ($list as $item) : ?>
<tr>
	<td><?=$item->id?></td>
	<td class="link"><?=$item->link?></td>
	<td><?=$item->last_update?></td>
	<td><?=$item->user_id?></td>
	<td><?=$item->title?></td>
	<td><?=$item->city_id?></td>
	<td><?=$item->category_id?></td>
	<td class="query_string"><?=$item->query_string?></td>
	<td><?=$item->region_id?></td>
	<td><?=$item->action_id?></td>
	<td><?=$item->period?></td>
	<td><?=$item->next_date_to_send?></td>
	<td><?=$item->hash?></td>
	<td><?=$item->created?></td>
	<td><?=$item->search_cache_id?></td>
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