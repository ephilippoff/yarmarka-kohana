<tr <?php if ($log['is_published']) : ?>style="background-color: #FFECF2;"<?php endif;?>>
		<td><?=$log['object_id']?></td>
		<td><?=$log['createdon']?></td>
		<td>
			<b><a href="<?=CI::site('detail/'.$log['object_id'])?>" target="_blank"><?=$log['title']?></a></b>
			<p>
				<span class="object_text">
					<?php if (mb_strlen($log['user_text']) > 200) : ?>
						<?=Text::limit_chars($log['user_text'], 200, '...', TRUE)?>
					<?php else : ?>
						<?=$log['user_text']?>
					<?php endif; ?>
				</span>
			</p>
			<p><b>Статус: </b><?=$log['is_bad']?></p>
		</td>
		
		<td><?=$log['description']?></td>		

		<td>
			<?=Date::formatted_time($log['real_date_created'], 'd.m.Y H:i')?>
			/
			<?=Date::formatted_time($log['date_created'], 'd.m.Y H:i')?>
		</td>
		<td>#<?=$log['user_id']?> <br/> <?=$log['author_email']?>  </td>
		<td>#<?=$log['action_by']?> <br> <?=$log['op_email']?> <br/> <?=$log['op_fullname']?> </td>
		
		<td><?=$log['category_title']?></td>
		<td>
			<?=join(', ', DB::select('contacts.contact')
					->from('object_contacts')
					->join('contacts', 'LEFT')->on('object_contacts.contact_id', '=', 'contacts.id')
					->where('object_id', '=', (int)$log['object_id'])
					->execute()
					->as_array(NULL, 'contact')) ?>		
		</td>
		<td>
			<?php if ($log['object_main_photo']) : ?>
				<img src="<?=Uploads::get_file_path($log['object_main_photo'], '120x90')?>" />
			<?php endif;?>
		</td>
</tr>
