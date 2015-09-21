<table class="table table-hover table-condensed articles">
<tr>
	<th>id</th>
	<th>created_on</th>
	<th>description</th>
	<th>state</th>
	<th>user_id</th>
	<th>sum</th>
	<th>discount</th>
	<th>total_sum</th>
	<th>transact_id</th>
	<th>payment_date</th>
	<th>date_expiration</th>
	<th>source</th>
	<th>user_data</th>
	<th>code_request</th>
	<th>request_date</th>
	<th>changed_code_request_date</th>
	<th>payment_method_code</th>
	<th>payment_method_description</th>
</tr>
<?php foreach ($list as $item) : ?>
<tr>
	<td><?=$item->id?></td>
	<td><?=$item->created_on?></td>
	<td><?=$item->description?></td>
	<td><?=$item->state?></td>
	<td><?=$item->user_id?></td>
	<td><?=$item->sum?></td>
	<td><?=$item->discount?></td>
	<td><?=$item->total_sum?></td>
	<td><?=$item->transact_id?></td>
	<td><?=$item->payment_date?></td>
	<td><?=$item->date_expiration?></td>
	<td><?=$item->source?></td>
	<td><?=$item->user_data?></td>
	<td><?=$item->code_request?></td>
	<td><?=$item->request_date?></td>
	<td><?=$item->changed_code_request_date?></td>
	<td><?=$item->payment_method_code?></td>
	<td><?=$item->payment_method_description?></td>
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