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

	$('.show_full_text').click(function(e){
		e.preventDefault();

		var obj = this;
		$.post('/ajax/get_full_text/'+$(this).data('id'), function(json){
			$(obj).parents('.object_text').html(json.text);
		}, 'json');
	});

	$('.moder_state').click(function(e){
		e.preventDefault();

		var obj = this;
		$.post('/khbackend/objects/ajax_change_moder_state/'+$(this).data('id'), {moder_state:$(obj).data('state')}, function(){
			$(obj).parents('.btn-group').find('span.text').text($(obj).text());
			$(obj).parents('.btn-group').find('.dropdown-toggle').attr('class', 'btn dropdown-toggle '+$(obj).data('class'));
		});
	});
});
function delete_object(obj) {
	if (confirm('Delete object?')) {
		$.post($(obj).attr('href'), {}, function(json){
			if (json.code == 200) {
				$(obj).parents('tr').remove();
			}
		}, 'json');
	}
	return false;
}
</script>

<form class="form-inline">
	<div class="input-prepend">
		<span class="add-on"><i class="icon-envelope"></i></span>
		<input class="span2" id="prependedInput" type="text" placeholder="User email or object id" name="email" value="<?=Arr::get($_GET, 'email')?>">
    </div>
	<div class="input-prepend">
		<span class="add-on">Contact</span>
		<input class="span2" id="prependedInput" type="text" placeholder="User contact or name" name="contact" value="<?=Arr::get($_GET, 'contact')?>">
    </div>
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
		<input type="text" class="input-small dp" placeholder="date from" name="date[from]" value="<?=Arr::get(@$_GET['date'], 'from')?>">
		<input type="text" class="input-small dp" placeholder="date to" name="date[to]" value="<?=Arr::get(@$_GET['date'], 'to')?>">
	</div>
	<?=Form::select('category_id', array('' => '--category--')+$categories, Arr::get($_GET, 'category_id'), array('class' => 'span2'))?>
	<?=Form::select('moder_state', 
		array(
			'' => '--moderation--',
			'0' => 'К модерации',
			'1' => 'Предмодерация',
			'2' => 'Прошло модерацию',
		), 
		Arr::get($_GET, 'moder_state'), array('class' => 'span2'))
	?>
	<input type="submit" name="" value="Filter" class="btn btn-primary">
	<input type="reset" name="" value="Clear" class="btn">
</form>


<table class="table table-hover table-condensed" style="font-size:85%;">
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
	<?php if ($object->is_banned()) : ?>
	<tr class="error">
	<?php else : ?>
	<tr>
	<?php endif; ?>
		<td><?=$object->id?></td>
		<td>
			<?=$object->city_obj->title?><br />
			<?=$object->category_obj->title?>
		</td>
		<td>
			<b><?=$object->contact?></b><br />
			<?=join(', ', $object->get_contacts()->as_array(NULL, 'contact')) ?>
			<br />
			<a href=""><?=$object->user->email?></a>
		</td>
		<td>
			<b><a href="<?=CI::site('detail/'.$object->id)?>" target="_blank"><?=$object->title?></a></b><br />
			<p>
				<div id="gallery" data-toggle="modal-gallery" data-target="#modal-gallery">
				<?php if ($object->main_image_filename) : ?>
					<a href="<?=Uploads::get_file_path($object->main_image_filename, 'orig')?>" data-gallery="gallery">
						<img align="right" style="margin-top: -20px;" src="<?=Uploads::get_file_path($object->main_image_filename, '120x90')?>" title="<?=$object->main_image_title ?>" />
					</a>
				<?php endif; ?>
				</div>

				<span class="object_text">
					<?php if (mb_strlen($object->full_text) > 200) : ?>
						<?=Text::limit_chars($object->full_text, 200, '...', TRUE)?>
						<a href="#" class="show_full_text" data-id="<?=$object->id?>">show full text</a>
					<?php else : ?>
						<?=$object->full_text?>
					<?php endif; ?>
				</span>
			</p>
			<p class="text-error"><?=join(', ', $object->get_attributes_values()) ?></p>
		</td>
		<td>
			<div class="btn-group">
				<button class="btn dropdown-toggle
					<?php if ($object->is_banned()) : ?>
						btn-danger
					<?php elseif ($object->is_moderate()) : ?>
						btn-success
					<?php else : ?>
						btn-warning
					<?php endif; ?>
				" data-toggle="dropdown">
					<span class="text">
					<?php if ($object->is_banned()) : ?>
						Заблокировано
					<?php elseif ($object->is_moderate()) : ?>
						Прошло модерацию
					<?php else : ?>
						На модерации
					<?php endif; ?>
					</span>
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li><a href="<?=URL::site('khbackend/objects/ajax_decline/'.$object->id)?>" data-toggle="modal" data-target="#myModal" class="btn-danger">На исправление</a></li>
					<li><a href="<?=URL::site('khbackend/objects/ajax_ban/'.$object->id)?>" data-toggle="modal" data-target="#myModal" class="btn-danger">Заблокировать</a></li>
					<li><a href="#" data-id="<?=$object->id?>" data-state="0" data-class="btn-warning" class="moder_state btn-warning">На модерации</a></li>
					<li><a href="#" data-id="<?=$object->id?>" data-state="1" data-class="btn-success" class="moder_state btn-success">Прошло модерацию</a></li>
				</ul>
			</div>	
		</td>
		<td>
			<?=Date::formatted_time($object->real_date_created, 'd.m.Y H:i')?>
			/
			<?=Date::formatted_time($object->date_created, 'd.m.Y H:i')?>
		</td>
		<td>
			<a href="<?=CI::site('detail/'.$object->id)?>" target="_blank" title="Open object in new windows" class="icon-eye-open"></a>
			<a href="<?=URL::site('khbackend/objects/ban/'.$object->id)?>" title="Ban object" class="icon-lock" onClick="return ban(this);"></a>
			<a href="<?=URL::site('khbackend/objects/delete/'.$object->id)?>" title="Delete object" onClick="return delete_object(this);" class="icon-trash"></a>
		</td>
	</tr>
	<?php endforeach; ?>
</table>

<?=$pagination?>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>