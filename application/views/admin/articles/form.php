<?=HTML::script('bootstrap/datepicker/js/bootstrap-datepicker.js')?>
<?=HTML::style('bootstrap/datepicker/css/datepicker.css')?>
<script type="text/javascript" src="/bootstrap/tinymce/tinymce.min.js"></script>
<script type="text/javascript" charset="utf-8">
tinymce.init({
    selector: "textarea",
    theme: "modern",
    plugins: [
        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen",
        "insertdatetime media nonbreaking save table contextmenu directionality",
        "emoticons template paste textcolor"
    ],
    toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
    toolbar2: "print preview media | forecolor backcolor emoticons",
    image_advtab: true,
    width: '100%',
   	verify_html : false
});

$(document).ready(function() {
	$('#generate_from_title').click(function(e){
		e.preventDefault();

		$.post('/ajax/transliterate_str', {str:$('#title').val()}, function(json){
			$('#seo_name').val(json.str);
		}, 'json');

		return false;
	});

	$('#is_category').click(function(e){
		if ($(this).is(':checked')) {
			$('#text').hide();
		} else {
			$('#text').show();
		}
	});
	

	// enable datepicker
	$('.dp').datepicker({
		format:	'yyyy-mm-dd'
	}).on('changeDate', function(){
		$(this).datepicker('hide');
	});

});
</script>


<form class="form-horizontal" method="post" enctype="multipart/form-data">
	<div class="control-group <?=Arr::get($errors, 'title') ? 'error' : ''?>">
		<label class="control-label">Title</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'title', @$article->title)?>" class="input-block-level" name="title" id="title"  />
			<span class="help-inline"><?=Arr::get($errors, 'title')?></span>
		</div>
	</div>

	<div class="control-group <?=Arr::get($errors, 'seo_name') ? 'error' : ''?>">
		<label class="control-label">Seo name</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'seo_name', @$article->seo_name)?>" class="input-block-level" name="seo_name" id="seo_name">
			<span class="help-inline"><?=Arr::get($errors, 'seo_name')?></span>
			<button class="btn btn-link" id="generate_from_title">Generate from title</button>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Is category?</label>
		<div class="controls">
			<input type="checkbox" name="is_category" value="1" id="is_category" <?php if (Arr::get($_POST, 'is_category', @$article->is_category)) echo 'checked' ?>>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label">Type</label>
		<div class="controls">			
			<input type="radio" name="text_type" value="1" class="text_type" <?php if (@$article->text_type == 1) echo 'checked' ?> <?php if (Request::current()->action() == 'add') : ?>checked<?php endif;?> > Статья
			<input type="radio" name="text_type" value="2" class="text_type" <?php if (@$article->text_type == 2) echo 'checked' ?>> Новость
		</div>
	</div>	
	
	<div class="control-group">
		<label class="control-label">Опубликовать</label>
		<div class="controls">
			<input type="checkbox" name="is_visible" value="1" id="is_visible" <?php if (Arr::get($_POST, 'is_visible', @$article->is_visible)) echo 'checked' ?>>
		</div>
	</div>	
	
	<div class="control-group">
		<label class="control-label">Дата начала:</label>
		<div class="controls">
			<input type="text" class="input-small dp" placeholder="date from" name="start_date" value="<?=Arr::get($_POST, 'start_date', date('Y-m-d'))?>">
		</div>
	</div>
		
	<div class="control-group">		
		<label class="control-label">Дата окончания:</label>
		<div class="controls">
			<input type="text" class="input-small dp" placeholder="date to" name="end_date" value="<?=Arr::get($_POST, 'end_date', date('Y-m-d', strtotime('+3 days')))?>">
		</div>		
	</div>		
	
	<?php if (trim(@$article->photo)) : ?>
		<div class="control-group">		
			<label class="control-label"></label>
			<div class="controls">
				<img src="<?=@Uploads::get_file_path($article->photo, '120x90')?>">
			</div>		
		</div>
	<?php endif;?>
	
	<div class="control-group">		
		<label class="control-label">Фото:</label>
		<div class="controls">
			<input type="file" class="input-small" placeholder="photo" name="photo" >
		</div>		
	</div>

	<div class="control-group">
		<label class="control-label">Parent</label>
		<div class="controls">
			<?=Form::select('parent_id', 
						array('-- ROOT --')+$articles, 
						Arr::get($_POST, 'parent_id', @$article->parent_id), 
						array('size' => count($articles)+1, 'class' => 'input-block-level')
			)?>
		</div>
	</div>

	<span id="text" <?php if (@$article->is_category) : ?>style="display:none"<?php endif; ?>>

	<div class="control-group">
		<label class="control-label">Description</label>
		<div class="controls">
			<textarea style="height:300px" name="description" class="tiny input-block-level"><?=Arr::get($_POST, 'description', @$article->description)?></textarea>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Text</label>
		<div class="controls">
			<textarea name="text" style="height:600px" class="tiny input-block-level"><?=Arr::get($_POST, 'text', @$article->text)?></textarea>
		</div>
	</div>
	</span>

	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Save</button>
		</div>
	</div>
</form>