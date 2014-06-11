<table class="table table-hover table-condensed articles">
	<tr>
		<th>Id</th>
		<th>Номер</th>
		<th>Ссылка</th>
		<th>Баннер</th>
		<th>Цвет</th>
		<th>Дата старта</th>
		<th>Дата окончания</th>
		<th></th>
	</tr>
	<?php foreach ($ads_list as $ads_element) : ?>
		<tr>
			<td><?=$ads_element->id?></td>
			<td><?=$ads_element->title?></td>
			<td><?=$ads_element->link?></td>
			<td>
				<?php if (is_file(DOCROOT.'uploads/banners/'.$ads_element->image)) : ?>
						<img src="<?='/uploads/banners/'.$ads_element->image?>" />
				<?php endif;?>
			</td>
			<td><?=$ads_element->class?></td>
			<td><?=$ads_element->start_date?></td>
			<td><?=$ads_element->end_date?></td>
			<td>
				<!--<a href="<?=Url::site('khbackend/reklama/edit/'.$ads_element->id)?>" class="icon-pencil"></a>-->
				<a href="<?=Url::site('khbackend/reklama/delete/'.$ads_element->id)?>" class="icon-trash delete_article"></a>
			</td>
		</tr>
	<?php endforeach; ?>
</table>