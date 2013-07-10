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
});
</script>


<form class="form-horizontal" method="post">
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