<h1><?=$user->login?> (<?=$user->email?>)</h1>

<div class="row"> 

	<div class="span6">
	<table class="table table-hover table-condensed">
	<?php foreach (array_keys($user->list_columns()) as $column_name) : ?>
	<?php if ( ! in_array($column_name, array('passw', 'code'))) : ?>
	<tr>
		<td><?=$column_name?></td>
		<td><?=$user->$column_name?></td>
	</tr>
	<?php endif; ?>
	<?php endforeach; ?>
	</table>
	</div>

	<div class="span6">
	<table class="table table-hover table-condensed">
	<tr>
		<th>Access log:</th>
	</tr>
	<?php foreach ($user->access->find_all() as $access) : ?>
	<tr>
		<td><?=$access->ip?> <?=date('Y-m-d H:i:s', strtotime($access->date))?></td>
	</tr>
	<?php endforeach; ?>
	</table>
	</div>
</div>

<div class="row">
	<div class="span6">
	<table class="table table-hover table-condensed">
	<tr>
		<th>User ads:</th>
	</tr>
	<?php foreach ($user->objects->order_by('date_created', 'desc')->find_all() as $object) : ?>
	<tr>
		<td>
			<small>#<b><?=$object->id?></b> <?=date('Y-m-d H:i', strtotime($object->real_date_created))?> </small>
			<a href="<?=CI::site('detail/'.$object->id)?>" target="_blank"><?=$object->title?></a>
		</td>
	</tr>
	<?php endforeach; ?>
	</table>
	</div>

	<div class="span6">
	<table class="table table-hover table-condensed">
	<tr>
		<th>User invoices:</th>
	</tr>
	<?php foreach ($user->invoices->success()->order_by('created_on', 'desc')->find_all() as $invoice) : ?>
	<tr>
		<td><?=$invoice->description?> <span class="badge"><?=$invoice->sum?>р</span></td>
	</tr>
	<?php endforeach; ?>
	</table>
	</div>
</div>
