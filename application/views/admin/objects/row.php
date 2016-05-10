<?php if ($object->is_banned()) : ?>
	<tr class="error" id="<?=$object->id?>">
<?php elseif($object->to_forced_moderation) : ?>
	<tr class="warning" id="<?=$object->id?>">
<?php else : ?>
	<tr id="<?=$object->id?>">
<?php endif; ?>

	<td>
		<?=$object->id?> <br>
		<?php 
	 		$services = array();
	 		if (@$compiled["services"]) {
		 		foreach ($compiled["services"] as $key => $service) {
		 			if (count($service) > 0 ) {
		 				array_push($services, $key." (".count($service).")");
		 			}
		 		}
		 	}
	 	?>
	 	<?php if (count($services) > 0) :?>
	 	<a href="<?=URL::site('khbackend/objects/services/'.$object->id)?>" target="_blank">Услуги: <br>
			
				<?=implode(", ", $services)?>
			
		</a>
		<?php endif;?>

	</td>
	<td>
		<b><?=@$cities[$object->city_id]?> </b> <br />
		<?=@$compiled["city"]?><br />
		<?=$categories[$object->category]?>
	</td>
	<td>
	 	<?php 
	 		$contacts = array();
	 		if ( isset( $object_contacts[$object->id] ) ) {
		 		foreach ($object_contacts[$object->id] as $key => $contact) {
		 			array_push($contacts, $contact->contact_value );
		 		}
		 	}
	 	?>
		<b><?=$object->contact?></b><br />
		<?=implode(", ", $contacts)?>
		<br />
		<a href="" title="Показать только объявления этого пользователя" onClick="return set_query('user_id=<?=$object->author?>')"><?=@$users[$object->author]?></a>
	</td>
	<td>
		<b>(<?=($object->is_published) ? "Опубликовано":"Снято"?>) <a href="<?=CI::site('detail/'.$object->id)?>" target="_blank"><?=strip_tags($object->title)?></a></b>
		<a href="<?=URL::site('khbackend/objects/edit/'.$object->id)?>" class="icon-pencil" style="margin-left: 15px;" title="Редактировать текст объявления" data-toggle="modal" data-target="#myModal"></a>
		<br />
		<p>
			<div id="gallery" data-toggle="modal-gallery" data-target="#modal-gallery">
			<?php if (isset($compiled['images']['main_photo'])) : ?>
				<a href="/<?=$compiled['images']['main_photo']['original']?>" data-gallery="gallery">
					<img align="right" style="margin-top: -20px;" src="/<?=$compiled['images']['main_photo']['120x90']?>" />
				</a>
			<?php endif; ?>
			</div>

			<span class="object_text">
				<?php if (mb_strlen($object->full_text) > 200) : ?>
					<?=Text::limit_chars(strip_tags($object->user_text), 200, '...', TRUE)?>
					<a href="#" class="show_full_text" data-id="<?=$object->id?>">show full text</a>
				<?php else : ?>
					<?=strip_tags($object->user_text)?>
				<?php endif; ?>
			</span>
		</p>
		<p class="text-error">
		 	<?php 
		 		$attributes = array();
		 		if (@$compiled["attributes"]) {
			 		foreach ($compiled["attributes"] as $key => $attribute) {
			 			array_push($attributes, $attribute["title"].":".$attribute["value"]);
			 		}
			 	}
		 	?>
			<?=implode(", ", $attributes)?>
		</p>
		<address>
			<?=@$compiled["region"]?>,<?=@$compiled["city"]?>,<?=@$compiled["address"]?>
		</address>
	</td>
	<td style="width:200px;">
		<div class="btn-group">
			<button class="btn dropdown-toggle
				<?php if ( ! $object->is_moderate()) : ?>
					btn-warning
				<?php elseif ($object->is_moderate() == -1 OR $object->is_moderate() == 4) : ?>
					btn-default
				<?php elseif ($object->is_banned()) : ?>
					btn-danger
				<?php else : ?>
					btn-success
				<?php endif; ?>
			" data-toggle="dropdown">
				<span class="text">
				<?php if ( ! $object->is_moderate()) : ?>
					На модерации
				<?php elseif ($object->is_moderate() == 4) : ?>
					Массовая загрузка
				<?php elseif ($object->is_moderate() == -1) : ?>
					! Пред модерация
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
				<?php if ($object->in_archive == 1) : ?>
					<li><a href="#" data-id="<?=$object->id?>" class="btn-warning fn-archive">Разархивировать</a></li>
				<?php endif;?>
			</ul>

		</div>	
		<?php 
			$complaint = array_filter($complaints, function($item) use ($object){
				return $item->object_id == $object->id;
			});
		if (count($complaint) > 0) : ?>
			<br />
			<br />				
			<a href="<?=URL::site('khbackend/objects/complaints/'.$object->id)?>" class="btn btn-info" data-toggle="modal" data-target="#myModal">Посмотреть жалобы</a>				
		<?php endif; ?>
		<br />
		<br />	
		<?php /*if ($object->main_image_filename) : ?>
			<a title="Удалить из показов(кр) / Поместить в показы(зел.) " href="#" onclick="obj_selection(this, <?=$object->id?>);return false;" class="<?php if ($object->in_selection) : ?> in <?php endif; ?> selection"></a>				
		<?php endif;*/ ?>
		
		<div>
			<label for="not_show_on_index_<?=$object->id?>"><input type="checkbox" class="not_show_on_index" id="not_show_on_index_<?=$object->id?>"  data-id="<?=$object->id?>" <? if ($object->not_show_on_index):?>checked<? endif; ?> >не показывать на главной</label>
		</div>
		<? if ($user_role_admin): ?>
		<ul  class="">
		<li><a href="<?=URL::site('edit/'.$object->id)?>" class="btn-warning">Редактировать</a></li>
			<li><a href="#" data-id="<?=$object->id?>" class="js-service-up">Поднять</a>, 
			<a href="#" data-id="<?=$object->id?>" class="js-service-premium">Премиум</a>, 
			<a href="#" data-id="<?=$object->id?>" class="js-service-lider">Лидер</a>, 
			<a href="#" data-id="<?=$object->id?>" class="js-service-cities" data-city="<?=$object->city_id?>">Несколько городов</a></li>
			
		</ul>
		<? endif; ?>
	</td>
	<td style="width:150px;">
		<?=Date::formatted_time($object->real_date_created, 'd.m.Y H:i')?>
		/
		<?=Date::formatted_time($object->date_created, 'd.m.Y H:i')?>
		<br />
		Показывать с: <?php echo Date::formatted_time($object->date_expired, 'd.m.Y H:i'); ?>
	</td>

	<td>
		<a href="<?=CI::site('detail/'.$object->id)?>" target="_blank" title="Open object in new window" class="icon-eye-open"></a>
		<a href="<?=URL::site('edit/'.$object->id)?>" target="_blank" title="Edit object in new window" class="icon-pencil"></a>
		<a href="<?=URL::site('khbackend/objects/ajax_delete/'.$object->id)?>" title="Delete object" data-toggle="modal" data-target="#myModal" class="icon-trash"></a>			
	</td>
</tr>