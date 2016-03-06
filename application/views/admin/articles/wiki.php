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


<a href="/khbackend/articles/wiki_add">Добавить</a>
<table class="table table-hover table-condensed articles">
<tr>
	<th>Id</th>
	<th>Url</th>
	<th>City</th>
	<th></th>
</tr>
<?php foreach ($wiki as $article) : ?>
<tr>
	<td><?=$article['id']?></td>
	<td>
		<a href="/khbackend/articles/wiki_edit/<?=$article['id']?>" target="_blank"><?=$article['url']?></a>
	</td>
	<td>
		<?=$article['city']?>
	</td>
	<td>
		<a href="<?=URL::site('khbackend/articles/delete_wiki/'.$article['id'])?>" class="icon-trash delete_article"></a>
	</td>

</tr>
<?php endforeach; ?>
</table>