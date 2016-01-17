<?php $pl = 40 * $level ?>
<tr  class="sub_categories_for_<?=$parent_id?>"><td colspan="10" style="height:3px;background:gray;"></td></tr>
<?php foreach ($categories as $category) : ?>
	<? $color = ($category->for_admin)? "red" : ""; ?>
	<tr class="sub_categories_for_<?=$category->parent_id?> <?=$color?>">
		<?php for ($i = 0; $i<=$level-1;$i++) { ?>
			<td></td>
		<?php } ?>
		<td>
		<?php if ($count = $category->sub_structure->count_all()) : ?>
			<a class="icon-plus show_sub_categories" style="cursor:pointer" data-id="<?=$category->id?>" data-level="<?=$level?>"></a>
			<a class="icon-minus hide_sub_categories" style="cursor:pointer; display:none" data-id="<?=$category->id?>"></a>
		<?php endif ?>
		</td>
		<td><?=$category->id?></td>
		<td><?=$category->title?></td>
		<td><a href="/<?=$category->url?>" target="_blank"> <?=$category->url?></a></td>
		<td>
			<a href="/khbackend/category/addstructure?parent_id=<?=$category->id?>" class="icon-plus"></a>
			<a href="<?=URL::site('khbackend/category/editstructure/'.$category->id)?>" class="icon-pencil"></a>
			<?php if (!$count) : ?>
				<a href="<?=URL::site('khbackend/category/deletestructure/'.$category->id)?>" class="icon-trash"></a>
			<?php endif; ?>						
		</td>
	</tr>
<?php endforeach; ?>
<tr  class="sub_categories_for_<?=$parent_id?>"><td colspan="10" style="height:3px;background:gray;"></td></tr>
