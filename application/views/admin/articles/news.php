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

<table class="table table-hover table-condensed news articles">
<tr>
	<th>Id</th>
	<th>Seo name</th>
	<th>Title</th>
	<th>Created</th>
	<th>Start date</th>
	<th>End date</th>
	<th></th>
</tr>
<?php foreach ($news as $news_one) : ?>
<tr>
	<td><?=$news_one->id?></td>
	<td>
		<a href="<?=URL::site(Route::get('news')->uri(array('id' => $news_one->id)))?>" target="_blank"><?=$news_one->seo_name?></a>
	</td>
	<td>
		<?=$news_one->title?>
	</td>
	<td><?=$news_one->created?></td>
	<td><?=$news_one->start_date?></td>
	<td><?=$news_one->end_date?></td>
	<td>
		<a href="<?=Url::site('khbackend/articles/edit/'.$news_one->id)?>" class="icon-pencil"></a>
		<a href="<?=Url::site('khbackend/articles/delete/'.$news_one->id)?>" class="icon-trash delete_article"></a>
	</td>
</tr>
<?php endforeach; ?>
</table>

<?=$pagination?>