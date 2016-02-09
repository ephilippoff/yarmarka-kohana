<?//=HTML::script('bootstrap/datepicker/js/bootstrap-datepicker.js')?>
<?//=HTML::style('bootstrap/datepicker/css/datepicker.css')?>

<?=HTML::script('bootstrap/datetimepicker/jquery.datetimepicker.js')?>
<?=HTML::style('bootstrap/datetimepicker/jquery.datetimepicker.css')?>

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
	
	jQuery('.dp').datetimepicker({format:'Y-m-d H:i', lang:'ru'});

	$('.fn-type-text').change(function(){		
		if ($('.fn-type-text:checked').val() == 1)
		{	
			$('.only2').hide();
			$('.only1').show();
		}
		else if ($('.fn-type-text:checked').val() == 2)
		{	
			$('.only2').show();
			$('.only1').hide();
		}
	})

});
</script>

<?php 
	$text_type = (@$article->text_type == 2 or @$text_type_default == 2 or @$_POST['text_type'] == 2) ? 2 : 1;
		
	$start_date = isset($article->start_date) ? $article->start_date : Arr::get($_POST, 'start_date', date('Y-m-d H:i:s'));	
	$weight = isset($article->weight) ? $article->weight : Arr::get($_POST, 'weight');	
	$end_date   = isset($article->end_date)   ? $article->end_date   : Arr::get($_POST, 'end_date',   date('Y-m-d H:i:s', strtotime('+3 days')));
	$img_url	= isset($article->img_url)   ? $article->img_url   : Arr::get($_POST, 'img_url', '');
?>

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
			<input type="radio" name="text_type" value="1" class="fn-type-text text_type" <?php if ($text_type == 1) echo 'checked' ?> > Статья
			<input type="radio" name="text_type" value="2" class="fn-type-text text_type" <?php if ($text_type == 2) echo 'checked' ?> > Новость
		</div>
	</div>	
		
	<div class="control-group">
		<label class="control-label">Опубликовать</label>
		<div class="controls">
			<input type="checkbox" name="is_visible" value="1" id="is_visible" <?php if (Arr::get($_POST, 'is_visible', @$article->is_visible)) echo 'checked' ?>>
		</div>
	</div>	

	<div class="control-group only2 fn-start-date-box" <?php if ($text_type == 1) : ?>style="display: none"<?php endif; ?> >
		<label class="control-label">Вес:</label>
		<div class="controls">
			<input style="width: 200px;" type="text" class="input-small" placeholder="вес" name="weight" value="<?=$weight?>">
		</div>
	</div>
		
	<div class="control-group only2 fn-start-date-box" <?php if ($text_type == 1) : ?>style="display: none"<?php endif; ?> >
		<label class="control-label">Дата начала:</label>
		<div class="controls">
			<input style="width: 200px;" type="text" class="input-small dp" placeholder="date from" name="start_date" value="<?=$start_date?>">
		</div>
	</div>

	<div class="control-group only2 fn-end-date-box" <?php if ($text_type == 1) : ?>style="display: none"<?php endif; ?> >		
		<label class="control-label">Дата окончания:</label>
		<div class="controls">
			<input style="width: 200px;" type="text" class="input-small dp" placeholder="date to" name="end_date" value="<?=$end_date?>">
		</div>		
	</div>		
	
	<div class="control-group only2 news-city fn-news-city" <?php if ($text_type == 1) : ?>style="display: none"<?php endif; ?> >
		<label class="control-label">Город:</label>
		<div class="controls">
			<?=Form::select('cities[]', 
						array('Не выбран')+$cities, 
						Arr::get($_POST, 'cities', Dbhelper::convert_pg_array(@$article->cities)), 
						array('class' => 'news_city', 'multiple', 'size' => 15) 
			)?>
		</div>
	</div>	

	<?php if ($text_type == 2 and trim(@$article->photo)) : ?>
		<div class="control-group only2">		
			<label class="control-label"></label>
			<div class="controls">
				<img src="<?=@Uploads::get_file_path($article->photo, '120x90')?>">
			</div>		
		</div>
	<?php endif;?>
	
	<div class="control-group only2 fn-photo-add-box" <?php if ($text_type == 1) : ?>style="display: none"<?php endif; ?> >		
		<label class="control-label">Фото:</label>
		<div class="controls">
			<input type="file" class="input-small" placeholder="photo" name="photo" >
		</div>		
	</div>
	
	<div class="control-group only2" <?php if ($text_type == 1) : ?>style="display: none"<?php endif; ?> >		
		<label class="control-label">URL картинки(инфографика):</label>
		<div class="controls">
			<input style="" type="text" class="input-block-level" placeholder="" name="img_url" value="<?=$img_url?>">
		</div>		
	</div>
	
	<div class="control-group only2 fn-photo-add-box" <?php if ($text_type == 1) : ?>style="display: none"<?php endif; ?> >		
		<label class="control-label">Комментарий для фото:</label>
		<div class="controls">
			<textarea name="photo_comment" class="tiny input-block-level"><?=Arr::get($_POST, 'photo_comment', @$article->photo_comment)?></textarea>
		</div>		
	</div>	

	<div class="control-group only1 articles-rubrics-box" <?php if ($text_type == 2) : ?>style="display: none"<?php endif; ?> >
		<label class="control-label">Рубрики статей:</label>
		<div class="controls">
			<?=Form::select('article_parent_id', 
						array('-- ROOT --')+$articles, 
						Arr::get($_POST, 'parent_id', @$article->parent_id), 
						array('size' => count($articles)+1, 'class' => 'input-block-level')
			)?>
		</div>
	</div>
	
	<div class="control-group only2 news-rubrics-box" <?php if ($text_type == 1) : ?>style="display: none"<?php endif; ?> >
		<label class="control-label">Рубрики новостей:</label>
		<div class="controls">
			<?=Form::select('news_parent_id', 
						array('-- ROOT --')+$news, 
						Arr::get($_POST, 'parent_id', @$article->parent_id), 
						array('size' => count($news)+1, 'class' => 'input-block-level')
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
			<!--<button type="reset"  class="btn">Reset</button>-->
		</div>
	</div>
</form>