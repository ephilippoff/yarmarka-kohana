<a href="/khbackend/landing/add" style="margin-bottom: 20px;display: inline-block;">Добавить посадку</a>

<table class="table table-hover table-condensed promo">
	<tr>
		<th>Id</th>
		<th>Домен</th>
		<th>Object ID</th>		
		<th>Заголовок</th>		
		<th>Добавлен</th>
		<th></th>
	</tr>
	<?php foreach ($landing_list as $ads_element) : ?>		
		<tr>			
			<td><?=$ads_element->id?></td>
			<td><?=$ads_element->domain?></td>
			<td><?=$ads_element->object->id?></td>
			<td><?=strip_tags($ads_element->object->title)?></td>
			<td><?=$ads_element->created_on?></td>		
			<td>				
				<a href="<?=Url::site('khbackend/landing/delete/'.$ads_element->id)?>" class="icon-trash delete_article"></a>				
			</td>
		</tr>
	<?php endforeach; ?>
</table>

<?php if ($pagination->total_pages > 1) : ?>
<div class="row">
	<div class="span10"><?=$pagination?></div>
	<div class="span2" style="padding-top: 55px;">
		<span class="text-info">Limit:</span>
		<?php foreach (array(50, 100, 150) as $l) : ?>
			<?php if ($l == $limit) : ?>
				<span class="badge badge-info"><?=$l?></span>
			<?php else : ?>
				<a href="#" class="btn-mini" onClick="add_to_query('limit', <?=$l?>)"><?=$l?></a>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>
