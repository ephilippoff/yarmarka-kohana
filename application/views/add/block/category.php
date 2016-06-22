<input type="hidden" name="default_action" id="default_action" value="<?=$data->default_action?>">
<? if ($data->edit): ?>
	<?=$data->value?>
	<input type="hidden" name="<?=$name?>" id="<?=$id?>" value="<?=$data->category_id?>"/>
<? else: ?>	
	<input type="hidden" name="<?=$name?>" id="<?=$id?>" value="<?=$data->category_id?>"/>
	<div id="<?=$name?>" data-value="<?=$data->category_id?>" class="<?=$_class?> accordeon-menu bbn brt2 brb2 w100p">
		<div class="sign_icon">
			<i class="fa fa-angle-down" aria-hidden="true"></i>
		</div>
		<div class="current_value">---</div>
		<div class="select_wrap hidden">
			<div class="option" data-value="0">---</div>

			

			<? foreach($data->category_list as $key=> $item) : ?>	
				<div class="optgroup">
					<div class="sign_icon">
						<i class="fa fa-plus-square-o" aria-hidden="true"></i>
					</div>
					<div class="optgroup_value"><?=$key?></div>
					<? foreach($item as $id=>$title) : ?>
						<?php if (!in_array($id, array(42,156,72))): ?>
							<div class="option hidden" data-value="<?=$id?>"><?=$title?></div>
						<?php endif ?>
					<? endforeach; ?>
				</div>
			<? endforeach; ?>

			<? foreach ($data->category_list['Другие'] as $key => $value) : ?>
				<?php if (in_array($key, array(42,156,72))): ?>
					<div class="option" data-value="<?=$key?>"><?=$value?></div>
				<?php endif ?>
			<? endforeach; ?>
		</div>
	</div>
<? endif; ?>
