	<?php if ($object->is_banned()) : ?>
	<tr class="error" id="<?=$object->id?>">
	<?php elseif($object->to_forced_moderation) : ?>
	<tr class="warning" id="<?=$object->id?>">
	<?php else : ?>
	<tr id="<?=$object->id?>">
	<?php endif; ?>
		<td><?=$object->id?></td>
		<td>
			<?=$object->city_obj->title?><br />
			<?=$object->category_obj->title?>
		</td>
		<td>
			<b><?=$object->contact?></b><br />
			<?=join(', ', $object->get_contacts()->as_array(NULL, 'contact')) ?>
			<br />
			<a href="" title="Показать только объявления этого пользователя" onClick="return set_query('user_id=<?=$object->user->id?>')"><?=$object->user->email?></a>
		</td>
		<td>
			<b><a href="<?=CI::site('detail/'.$object->id)?>" target="_blank"><?=$object->title?></a></b>
			<a href="<?=URL::site('khbackend/objects/edit/'.$object->id)?>" class="icon-pencil" style="margin-left: 15px;" title="Редактировать текст объявления" data-toggle="modal" data-target="#myModal"></a>
			<br />
			<p>
				<div id="gallery" data-toggle="modal-gallery" data-target="#modal-gallery">
				<?php if ($object->main_image_filename) : ?>
					<a href="<?=Uploads::get_file_path($object->main_image_filename, 'orig')?>" data-gallery="gallery">
						<img align="right" style="margin-top: -20px;" src="<?=Uploads::get_file_path($object->main_image_filename, '120x90')?>" title="<?=$object->main_image_title ?>" />
					</a>
				<?php endif; ?>
				</div>

				<span class="object_text">
					<?php if (mb_strlen($object->full_text) > 200) : ?>
						<?=Text::limit_chars(strip_tags($object->user_text), 200, '...', TRUE)?>
						<a href="#" class="show_full_text" data-id="<?=$object->id?>">show full text</a>
					<?php else : ?>
						<?=$object->user_text?>
					<?php endif; ?>
				</span>
			</p>
			<p class="text-error"><?=join(', ', $object->get_attributes_values()) ?></p>
			<?php if ($object->location_obj) : ?>
				<address>
					<?php if ($object->location_obj->kladr_id) : ?>
						<a class="icon-ok" title="Адрес выбран из КЛАДР"></a>
					<?php endif ?>
					<?=$object->location_obj->region?>,<?=$object->location_obj->city?>,<?=$object->location_obj->address?>
				</address>
			<?php endif ?>
		</td>
		<td>
			<div class="btn-group">
				<button class="btn dropdown-toggle
					<?php if ( ! $object->is_moderate()) : ?>
						btn-warning
					<?php elseif ($object->is_banned()) : ?>
						btn-danger
					<?php else : ?>
						btn-success
					<?php endif; ?>
				" data-toggle="dropdown">
					<span class="text">
					<?php if ( ! $object->is_moderate()) : ?>
						На модерации
					<?php elseif ($object->is_banned()) : ?>
						<?php if ($object->is_bad == 1) : ?>
							На исправлении
						<?php else : ?>
							Заблокировано
						<?php endif; ?>
					<?php else : ?>
						Прошло модерацию
					<?php endif; ?>
					</span>
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li><a href="#" data-id="<?=$object->id?>" data-state="1" data-class="btn-success" class="moder_state btn-success">Прошло модерацию</a></li>
					<li><a href="#" data-id="<?=$object->id?>" data-state="0" data-class="btn-warning" class="moder_state btn-warning">На модерации</a></li>
					<li><a href="<?=URL::site('khbackend/objects/ajax_decline/'.$object->id)?>" data-toggle="modal" data-target="#myModal" class="btn-danger">На исправление</a></li>
					<li><a href="<?=URL::site('khbackend/objects/ajax_ban/'.$object->id)?>" data-toggle="modal" data-target="#myModal" class="btn-danger">Заблокировать</a></li>
				</ul>
			</div>	
			<?php if ($object->complaints->count_all()) : ?>
				<br />
				<br />
				<a href="<?=URL::site('khbackend/objects/complaints/'.$object->id)?>" class="btn btn-info" data-toggle="modal" data-target="#myModal">Посмотреть жалобы</a>
			<?php endif; ?>
		</td>
		<td>
			<?=Date::formatted_time($object->real_date_created, 'd.m.Y H:i')?>
			/
			<?=Date::formatted_time($object->date_created, 'd.m.Y H:i')?>
		</td>
		<td>
			<a href="<?=CI::site('detail/'.$object->id)?>" target="_blank" title="Open object in new window" class="icon-eye-open"></a>
			<a href="<?=CI::site('user/edit_ad/'.$object->id)?>" target="_blank" title="Edit object in new window" class="icon-pencil"></a>
			<a href="<?=URL::site('khbackend/objects/ajax_delete/'.$object->id)?>" title="Delete object" data-toggle="modal" data-target="#myModal" class="icon-trash"></a>
		</td>
	</tr>
