<style type="text/css">
	.pending{background-color: orange}
	.error{background-color: red}
	.success{background-color: greenyellow}
</style>

<?=HTML::script('bootstrap/datepicker/js/bootstrap-datepicker.js')?>
<?=HTML::style('bootstrap/datepicker/css/datepicker.css')?>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		// enable datepicker
		$('.dp').datepicker({
			format:	'yyyy-mm-dd'
		}).on('changeDate', function(){
			$(this).datepicker('hide');
		});

	});
</script>

<form class="form-inline">
		<div class="input-prepend">
			<span class="add-on">Тема</span>
			<input class="span2" id="prependedInput" type="text" placeholder="" name="title" value="<?=Arr::get($search_filters, 'title')?>">
		</div>
		<div class="input-prepend">
			<span class="add-on">Получатель</span>
			<input class="span2" id="prependedInput" type="text" placeholder="" name="recipient" value="<?=Arr::get($search_filters, 'recipient')?>">
		</div>
		<input type="text" class="input-small dp" placeholder="date from" name="date[from]" value="<?=Arr::get(@$search_filters['date'], 'from')?>">
		<input type="text" class="input-small dp" placeholder="date to" name="date[to]" value="<?=Arr::get(@$search_filters['date'], 'to' )?>">

		<input type="submit" name="" value="Filter" class="btn btn-primary">
		<input type="reset" name="" value="Clear" class="btn" onclick="document.location='/khbackend/sms/emails';">
</form>

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