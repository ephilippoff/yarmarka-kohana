<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('.delete_article').click(function(e){
			e.preventDefault();

		var obj = this;

		if (confirm('Delete article?')) {
			$.post($(this).attr('href'), {}, function(json){
				if (json.code == 200) {
					$(obj).parents('tr').remove();
				}
			}, 'json');
		}

			return false;
		});
	});
</script>

<table class="table table-hover table-condensed">
<tr>
	<th>Id</th>
	<th>Seo name</th>
	<th>Title</th>
	<th>Created</th>
	<th></th>
</tr>
<?php foreach ($articles as $article) : ?>
<tr>
	<td><?=$article->id?></td>
	<td>
		<a href="<?=URL::site(Route::get('article')->uri(array('seo_name' => $article->seo_name)))?>" target="_blank"><?=$article->seo_name?></a>
	</td>
	<td>
		<?=str_repeat('&rarr;', $article->level).$article->title?>
	</td>
	<td><?=$article->created?></td>
	<td>
		<a href="<?=Url::site('khbackend/articles/edit/'.$article->id)?>" class="icon-pencil"></a>
		<a href="<?=Url::site('khbackend/articles/delete/'.$article->id)?>" class="icon-trash delete_article"></a>
	</td>
</tr>
<?php endforeach; ?>
</table>