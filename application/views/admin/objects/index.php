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

<script type="text/javascript" src="/bootstrap/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="/bootstrap/tinymce/jquery.tinymce.min.js"></script>

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
	
	$(document).on('click', '.fn-archive', function(e){
		e.preventDefault();
		$.post('/khbackend/objects/ajax_archive/'+$(this).data('id'), {}, function(){
			window.location.reload();
		});
	});	
});
function reload_row(object_id, moder_state) {
	var current_moder_state = $('select[name=moder_state]').val();

	if (typeof moder_state != 'undefined' && current_moder_state && current_moder_state != moder_state) {
		$('#'+object_id).remove();
		if ($('table#objects tr').length == 1) {
			window.location.reload();
		}
	} else {
		$.post('/khbackend/objects/object_row/'+object_id, {moder_state:moder_state}, function(html){
			var old_row = $('#'+object_id);
			old_row.after(html);
			old_row.remove();
		});
	}
}

function obj_selection(src, obj_id)
{	
	$.post('/ajax/obj_selection', {obj_id:obj_id}, function(json){

		if (json.status == 'added')
		{
			$(src).addClass('in');
		}
		else if (json.status == 'deleted')
			$(src).removeClass('in');
	}, 'json');	
	
}
</script>
<a href="/add" target="_blank">Подать объявление</a>
<form class="form-inline">
	
	<?php if ( !array_intersect(array_keys($search_filters), array('user_id','contact','id') ) ): ?>
		<div class="input-prepend">
			<span class="add-on"><i class="icon-envelope"></i></span>
			<input class="span2" id="prependedInput" type="text" placeholder="User email or object id" name="email" value="<?=Arr::get($_GET, 'email')?>">
	    </div>
    <?php endif; ?>
	
	<?php if ( !array_intersect(array_keys($search_filters), array('user_id','email','id') ) ): ?>
		<div class="input-prepend">
			<span class="add-on">Contact</span>
			<input class="span2" id="prependedInput" type="text" placeholder="User contact or name" name="contact" value="<?=Arr::get($_GET, 'contact')?>">
	    </div>
    <?php endif; ?>
    <?=Form::select('source_id', 
    	array(
    		'' => 'Все (из газеты и сайта)',
    		'1' => 'Подано на сайт',
    		'2' => 'Подано в газету'
    	), 
    	Arr::get($_GET, 'source_id', '1'), array('class' => 'span2'))
    ?>
	<?php if ( !array_intersect(array_keys($search_filters), array('user_id','email','id','contact') ) ): ?>
		<div class="input-prepend">
			<div class="btn-group">
				<button class="btn dropdown-toggle" data-toggle="dropdown">
					<span id="date_field">
						<?php if (Arr::get($_GET, 'date_field') == 'date_created') : ?>
							С учетом поднятия
						<?php else : ?>
							Реальная дата
						<?php endif; ?>
					</span>
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li><a href="#" data-field="real_date_created" class="date_field">Реальная дата</a></li>
					<li><a href="#" data-field="date_created" class="date_field">С учетом поднятия</a></li>
				</ul>
			</div>		
			<input type="hidden" name="date_field" value="<?=Arr::get($_GET, 'date_field', 'real_date_created')?>" />
			<input type="text" class="input-small dp" placeholder="date from" name="date[from]" value="<?=Arr::get(@$_GET['date'], 'from', date('Y-m-d', strtotime('-7 days')))?>">
			<input type="text" class="input-small dp" placeholder="date to" name="date[to]" value="<?=Arr::get(@$_GET['date'], 'to')?>">
		</div>

		<?=Form::select('category_id', array('' => 'Все рубрики')+$categories, Arr::get($_GET, 'category_id'), array('class' => 'span2'))?>
		<?=Form::select('city_id', array('' => 'Все города')+$cities, Arr::get($_GET, 'city_id'), array('class' => 'span2'))?>
		<?=Form::select('role_id', array('' => 'Все пользователи')+$roles, Arr::get($_GET, 'role_id', 2), array('class' => 'span2'))?>
		<?=Form::select('moder_state', 
			array(
				'' => 'Все объвления',
				'0' => 'На модерации',
				'1' => 'Прошло модерацию',
				'3' => 'Есть жалобы',
			), 
			Arr::get($_GET, 'moder_state', '0'), array('class' => 'span2'))
		?>
		

	<?php endif; ?>
	<input type="submit" name="" value="Filter" class="btn btn-primary">
	<input type="reset" name="" value="Clear" class="btn" onclick="document.location='/khbackend/objects';">
</form>

<?php if (isset($author) AND $author->loaded()) : ?>
<div class="alert">
	Объявления отфильтрованы по пользователю <strong><?=$author->fullname?></strong>
	<a href="" onClick="return set_query('');">сбросить</a>
</div>
<?php endif; ?>

<table class="table table-hover table-condensed" style="font-size:85%;" id="objects">
	<tr>
		<th>#</th>
		<th>City/Category</th>
		<th>Contacts</th>
		<th>Text</th>
		<th>Moderation</th>

		<?=View::factory('admin/objects/sort_th', 
			array('sort_by' => $sort_by, 'field_name' => 'real_date_created', 'direction' => $direction, 'name' => 'Real date/Up date'))?>

		<th></th>
	</tr>
	<?php foreach ($objects as $object) : ?>
		<?php
			$compiled = $object_compiled[$object->id]['compiled'];
			if ($compiled) {
				echo View::factory('admin/objects/row', array(
					'object' => $object, 
					'compiled' => $compiled, 
					'categories' => $categories, 
					'cities' => $cities,
					'users' => $users,
					'complaints' => $complaints,
					'object_contacts' => $object_contacts,
					'roles' => $roles
				));
			}
		?>
		
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

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>