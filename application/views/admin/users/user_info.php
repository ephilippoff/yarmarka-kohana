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
	<?php foreach ($user->access->limit(30)->order_by("id","desc")->find_all() as $access) : ?>
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
			<?
				$style = "black";
				if ($object->is_published == 0)
					$style = "red";

			?>
			<div style="color:<?=$style?>">
			<small>#<b><?=$object->id?></b> <?=date('Y-m-d H:i', strtotime($object->real_date_created))?> </small>
			<a href="<?=CI::site('detail/'.$object->id)?>" target="_blank"><?=$object->title?></a>
			</div>
		</td>
	</tr>
	<?php endforeach; ?>
	</table>
	</div>

	<div class="span6">
	<table class="table table-hover table-condensed">
	<tr>
		<th>Date</th>
		<th>User invoices:</th>
		<th>State</th>
		<th>PayDate</th>
	</tr>
	<?php foreach ($user->invoices->success()->order_by('created_on', 'desc')->find_all() as $invoice) : ?>
	<tr>
		<td><?=date('Y-m-d H:i', strtotime($invoice->created_on))?></td>
		<td><?=$invoice->description?> <span class="badge"><?=$invoice->sum?>р</span></td>
		<td><?=$invoice->state?></td>
		<td><?=date('Y-m-d H:i', strtotime($invoice->payment_date))?></td>
	</tr>
	<?php endforeach; ?>
	</table>
	</div>
</div>
