<?php $pl = 40 * $level ?>

<tr class="sub_categories_for_<?=$parent_id?>" >
	<td colspan="31" style="padding-left: <?=$pl?>px">
		<table class="table table-hover table-condensed">
			<?php foreach ($categories as $category) : ?>
				<tr <?php if ($category->is_ready == 0) : ?> style="background-color: red;" <?php endif;?> >
					<td>
					<?php if ($count = $category->sub_categories->count_all()) : ?>
						<a class="icon-plus show_sub_categories" style="cursor:pointer" data-id="<?=$category->id?>" data-level="<?=$level?>"></a>
						<a class="icon-minus hide_sub_categories" style="cursor:pointer; display:none" data-id="<?=$category->id?>"></a>
					<?php endif ?>
					</td>
					<td><?=$category->id?></td>
					<td><?=$category->title?></td>
					<td><?=$category->parent_id?></td>
					<td><?=$category->is_ready?></td>
					<td><?=$category->weight?></td>
					<td><?=$category->template?></td>
					<td><?=$category->use_template?></td>
					<td><?=$category->is_main?></td>
					<td><?=$category->main_menu_icon?></td>
					<td><?=$category->sinonim?></td>
					<td><?=$category->seo_name?></td>
					<td><?=$category->description?></td>
					<td><?=$category->max_count_for_user?></td>
					<td><?=$category->max_count_for_contact?></td>
					<td><?=$category->is_main_for_seo?></td>
					<td><?=$category->title_auto_fill?></td>
					<td><?=$category->text_required?></td>
					<td><?=$category->nophoto?></td>
					<td><?=$category->novideo?></td>
					<td><?=$category->main_menu_image?></td>
					<td><?=$category->submenu_template?></td>
					<td><?=$category->caption?></td>
					<td><?=$category->text_name?></td>
					<td><?=$category->rule?></td>
					<td><?=$category->show_map?></td>
					<td><?=$category->address_required?></td>
					<td><?=$category->plan_name?></td>
					<td><?=$category->through_weight?></td>
					<td><?=$category->url?></td>
					<td>
						<a href="<?=URL::site('khbackend/category/edit/'.$category->id)?>" class="icon-pencil"></a>
						<?php if (!$count) : ?>
							<a href="<?=URL::site('khbackend/category/delete/'.$category->id)?>" class="icon-trash"></a>
						<?php endif; ?>						
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
	</td>		
</tr>