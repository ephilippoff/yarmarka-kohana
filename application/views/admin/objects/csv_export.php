<?=HTML::script('bootstrap/datepicker/js/bootstrap-datepicker.js')?>
<?=HTML::style('bootstrap/datepicker/css/datepicker.css')?>

<form class="inline" method="POST">
	<div class="form-group">
		<label>Дата создания</label>
	</div>
	<div class="form-group">
		<input type="text" class="form-control" placeholder="От" name="date_start" />
	</div>
	<div class="form-group">
		<input type="text" class="form-control" placeholder="До" name="date_end" />
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-default">Выгрузить</button>
	</div>
</form>

<script type="text/javascript">
	$('[name=date_start],[name=date_end]').datepicker({
		format: 'mm-dd-yyyy'
	});
</script>