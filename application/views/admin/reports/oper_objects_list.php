<style type="text/css" media="screen">
	.container {
		width: 98%;
	}
</style>

<?=HTML::script('bootstrap/datepicker/js/bootstrap-datepicker.js')?>
<?=HTML::style('bootstrap/datepicker/css/datepicker.css')?>

<!-- modal-gallery is the modal dialog used for the image gallery -->
<div id="modal-gallery" class="modal modal-gallery hide fade" tabindex="-1">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h3 class="modal-title"></h3>
    </div>
    <div class="modal-body"><div class="modal-image"></div></div>
    <div class="modal-footer">
        <a class="btn btn-primary modal-next">Next <i class="icon-arrow-right icon-white"></i></a>
        <a class="btn btn-info modal-prev"><i class="icon-arrow-left icon-white"></i> Previous</a>
        <a class="btn btn-success modal-play modal-slideshow" data-slideshow="5000"><i class="icon-play icon-white"></i> Slideshow</a>
        <a class="btn modal-download" target="_blank"><i class="icon-download"></i> Download</a>
    </div>
</div>
<?=HTML::script('bootstrap/image-gallery/js/load-image.js')?>
<?=HTML::script('bootstrap/image-gallery/js/bootstrap-image-gallery.js')?>
<?=HTML::style('bootstrap/image-gallery/css/bootstrap-image-gallery.css')?>

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

/*

	$(document).on('click', '.show_full_text', function(e){
		e.preventDefault();

		var obj = this;
		$.post('/ajax/get_full_text/'+$(this).data('id'), function(json){
			$(obj).parent().html(json.text);
		}, 'json');
	});

	$(document).on('click', '.moder_state', function(e){
		e.preventDefault();

		var obj = this;
		$.post('/khbackend/objects/ajax_change_moder_state/'+$(this).data('id'), {moder_state:$(obj).data('state')}, function(){
			reload_row($(obj).data('id'), $(obj).data('state'));
			// $(obj).parents('.btn-group').find('span.text').text($(obj).text());
			// $(obj).parents('.btn-group').find('.dropdown-toggle').attr('class', 'btn dropdown-toggle '+$(obj).data('class'));
		});
	});
	
*/	
	
});

//function reload_row(object_id, moder_state) {
//	var current_moder_state = $('select[name=moder_state]').val();
//
//	if (typeof moder_state != 'undefined' && current_moder_state && current_moder_state != moder_state) {
//		$('#'+object_id).remove();
//		if ($('table#objects tr').length == 1) {
//			window.location.reload();
//		}
//	} else {
//		$.post('/khbackend/objects/object_row/'+object_id, {moder_state:moder_state}, function(html){
//			var old_row = $('#'+object_id);
//			old_row.after(html);
//			old_row.remove();
//		});
//	}
//}
</script>

<form class="form-inline">
	<div class="input-prepend">
		<span class="add-on">Object id</span>
		<input class="span2" id="prependedInput" type="text" placeholder="Object id" name="object_id" value="<?=Arr::get($_GET, 'object_id')?>">
    </div>	
	<div class="input-prepend">	
		<input type="text" class="input-small dp" placeholder="date from" name="date[from]" value="<?=Arr::get(@$_GET['date'], 'from', date('Y-m-d'))?>">
		<input type="text" class="input-small dp" placeholder="date to" name="date[to]" value="<?=Arr::get(@$_GET['date'], 'to', date('Y-m-d'))?>">
	</div>
	<?=Form::select('operator_id', array('' => 'Все операторы')+$operators, Arr::get($_GET, 'operator_id'), array('class' => 'span2'))?>	

	<input type="submit" name="" value="Filter" class="btn btn-primary">
	<input type="reset" name="" value="Clear" class="btn">
</form>

<p><b>Всего:</b> <?=$total?></p>

<table class="table table-hover table-condensed" style="font-size:85%;" id="objects">
	<tr>
		<th>#</th>
		<th>Date moderation</th>
		<th>Title/Text/Status</th>
		<th>Description</th>		
		<th>Real date/Up date</th>
		<th>Author</th>
		<th>Operator</th>
		<th>Category</th>
		<th>Contacts</th>
		<th>Photo</th>		

		<?//=View::factory('admin/objects/sort_th',array('sort_by' => $sort_by, 'field_name' => 'real_date_created', 'direction' => $direction, 'name' => 'Real date/Up date'))?>


		<th></th>
	</tr>
	<?php foreach ($logs as $log) : ?>
		<?=View::factory('admin/reports/_object_row', array('log' => $log))?>
	<?php endforeach; ?>
</table>


<?php if ( $pagination->total_pages > 1 ) : ?>
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

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>