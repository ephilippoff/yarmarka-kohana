<? if ($show_form): ?>
	<form method="post">
		<table class="table table-hover table-condensed" style="width:300px;">
			<tr>
				<th>Имя поля</th>
				<th>Название</th>
				<th>Тип</th>
			</tr>
		<? foreach($fields as $field):?>
			<tr>
				<?
					$title = $data->{$field.'_title'};
					$type = $data->{$field.'_type'};
				?>
				<td ><?=$field?></td>
				<td ><input name="<?=$field?>_title" type="text" value="<?=$title?>"/></td>
				<td >
					<select name="<?=$field?>_type">
						<? foreach ($type_fields as $name => $title) : ?>
							<option value="<?=$name?>" <? if ($type == $name) echo "selected" ?>><?=$title?></option>
						<? endforeach;?>
					</select>
				</td>
			</tr>
		<? endforeach;?>
		</table>
		<input type="submit" value="Сохранить настройки формы">
	</form>
<? endif; ?>
<table class="table table-hover table-condensed" style="width:100%">
	<tr>
		<? foreach($fields as $field):?>
			<?
				
				$field_name = $field;
				if ($fsetting->{$field.'_title'})
				{
					$field_name = $fsetting->{$field.'_title'}." (".$fsetting->{$field.'_type'}.")";
				}				
			?>
			

			<th><?=$field_name?></th>
		<? endforeach;?>
	</tr>
	<? foreach($items as $item):?>
		<tr>
		<? foreach($fields as $field):?>
			<?
				$value = $item->{$field};	
			?>
			<td><?=$value?></td>
		<? endforeach;?>
		</tr>
	<? endforeach;?>
</table>
