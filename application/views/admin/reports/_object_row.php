<tr <?php if ($log['is_published']) : ?>style="background-color: #FFECF2;"<?php endif;?>>
		<td><?=$log['object_id']?></td>
		<td><?=$log['createdon']?></td>
		<td><b><a href="<?=CI::site('detail/'.$log['object_id'])?>" target="_blank"><?=$log['title']?></a></b></td>
		<td>			
			<p>
				<span class="object_text">
					<?php if (mb_strlen($log['user_text']) > 200) : ?>
						<?=Text::limit_chars($log['user_text'], 200, '...', TRUE)?>
					<?php else : ?>
						<?=$log['user_text']?>
					<?php endif; ?>
				</span>
			</p>
		</td>
		
		<td><?=$log['description']?></td>		

		<td>
			<?=Date::formatted_time($log['real_date_created'], 'd.m.Y H:i')?>
			/
			<?=Date::formatted_time($log['date_created'], 'd.m.Y H:i')?>
		</td>
		<td>#<?=$log['user_id']?> <br/> <?=$log['author_email']?>  </td>
		<td>#<?=$log['action_by']?> <br> <?=$log['op_email']?> <br/> <?=$log['op_fullname']?> </td>
		<td><?=$log['is_bad']?></td>
</tr>
