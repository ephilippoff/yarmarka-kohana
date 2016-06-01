<input type="hidden" name="default_action" id="default_action" value="<?=$data->default_action?>">
<? if ($data->edit): ?>
	<?=$data->value?>
	<input type="hidden" name="<?=$name?>" id="<?=$id?>" value="<?=$data->category_id?>"/>
<? else: ?>	
	<input type="hidden" name="<?=$name?>" id="<?=$id?>" value=""/>
	<div id="<?=$id?>" data-value="<?=$data->category_id?>" class="<?=$_class?> accordeon-menu bbn brt2 brb2 w100p">
		<div class="sign_icon">
			<i class="fa fa-angle-down" aria-hidden="true"></i>
		</div>
		<div class="current_value">---</div>
		<div class="select_wrap hidden">
			<div class="option" data-value="0">---</div>
			<? foreach($data->category_list as $key=> $item) : ?>
			<div class="optgroup">
				<div class="optgroup_value"><?=$key?></div>
				<? foreach($item as $id=>$title) : ?>
					<div class="option hidden" data-value="<?=$id?>"><?=$title?></div>
				<? endforeach; ?>
			</div>
			<? endforeach; ?>
		</div>
	</div>
<? endif; ?>
