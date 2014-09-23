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
	<th>Visits</th>
	<th></th>
</tr>
<?php foreach ($news as $news_one) : ?>
<?php $future_show = (strtotime($news_one['start_date']) > strtotime('now') and $news_one['is_category'] == 0) ? 'future-show' : '' ?>
<tr class="<?php if ($news_one['is_category'] == 1) : ?>is_group<?php endif;?> <?=$future_show?>"  >
	<td><?=$news_one['id']?></td>
	<td>
		<a href="<?=URL::site(Route::get('newsone')->uri(array('id' => $news_one['id'], 'seo_name' => $news_one['seo_name'])))?>" target="_blank"><?=$news_one['seo_name']?></a>
	</td>
	<td class="title"><?=$news_one['title']?></td>
	<td><?=$news_one['created']?></td>
	<td><?=$news_one['start_date']?></td>
	<td><?=$news_one['end_date']?></td>
	<td><?=$news_one['visits']?></td>
	<td>
		<a href="<?=Url::site('khbackend/articles/edit/'.$news_one['id'])?>" class="icon-pencil"></a>
		<a href="<?=Url::site('khbackend/articles/delete/'.$news_one['id'])?>" class="icon-trash delete_article"></a>
	</td>
</tr>
<?php endforeach; ?>
</table>

<?=$pagination?>