<style type="text/css">
	.pending{background-color: orange}
	.error{background-color: red}
	.success{background-color: greenyellow}
</style>

<table class="table table-hover table-condensed articles">
<thead>
	<tr>
		<th>id</th>
		<th>created_on</th>
		<th>from</th>		
		<th>to</th>
		<th>title</th>
	</tr>
</thead>
<?php foreach ($list as $item) : ?>
<tr>
	<td><?=$item->id?></td>
	<td><?=$item->created_on?></td>
	<td><?=$item->sender?></td>
	<td><?=$item->recipient?></td>
	<td><a href="/khbackend/sms/email/<?=$item->id?>" target="_blank"> <?=$item->title?></a></td>
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