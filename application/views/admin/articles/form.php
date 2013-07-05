<script type="text/javascript" src="/js/adaptive/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" charset="utf-8">
  // wisiwyg
  tinyMCE.init({
  	mode : "textareas",
  	editor_selector : "tiny",
	// theme : "simple",
	language: "ru",
	plugins : "paste",
	width: "100%",
	paste_text_sticky : true,
	setup : function(ed) {
	ed.onInit.add(function(ed) {
		ed.pasteAsPlainText = true;
	});

	//ed.onKeyUp.add(function(ed, e) {
	  //var text = tinyMCE.activeEditor.getContent({format : 'raw'});
	//});
}
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

	<div class="control-group" id="text">
		<label class="control-label" for="inputPassword">Text</label>
		<div class="controls">
			<textarea name="text" class="tiny input-block-level"><?=Arr::get($_POST, 'text', @$article->text)?></textarea>
		</div>
	</div>

	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Save</button>
		</div>
	</div>
</form>