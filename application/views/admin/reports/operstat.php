<style type="text/css" media="screen">
	.container {
		width: 98%;
	}
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

	// enable tooltips
	$('a').tooltip();

	$('.date_field').click(function(e){
		e.preventDefault();


		$('#date_field').text($(this).text());
		$('input[name=date_field]').val($(this).data('field'));
	});


});

</script>

<form class="form-inline">
	<div class="input-prepend">	
		<!--<input type="hidden" name="date_field" value="<?=Arr::get($_GET, 'date_field', 'real_date_created')?>" />-->
		<input type="text" class="input-small dp" placeholder="date from" name="date[from]" value="<?=Arr::get(@$_GET['date'], 'from', date('Y-m-d'))?>">
		<input type="text" class="input-small dp" placeholder="date to" name="date[to]" value="<?=Arr::get(@$_GET['date'], 'to', date('Y-m-d') + strtolower('+3 days'))?>">
	</div>
	
	<input type="submit" name="" value="Filter" class="btn btn-primary">
	<input type="reset" name="" value="Clear" class="btn">	
</form>

<table class="tbl-operstat table">
	<thead><th>Email</th><th>ФИО</th><th>Количество</th></thead>	
	<?php foreach ($operstat as $operstat_row) : ?>
			<tr>
				<td><?=$operstat_row['email']?></td>
				<td><?=$operstat_row['fullname']?></td>
				<td><?=$operstat_row['count']?></td>
			</tr>
	<?php endforeach; ?>
	
</table>


<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>