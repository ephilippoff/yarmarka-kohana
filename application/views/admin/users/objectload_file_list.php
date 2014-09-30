<p><?=$config["name"]?></p>
<table class="table table-hover table-condensed" style="width:100%">
	<tr>
		<? foreach($fields as $field):?>
			<?
				$field = str_replace("___", "-", $field);
				$field_name = $field;
				if (array_key_exists($field, $config['fields']))
					$field_name = $config['fields'][$field]['translate'];
			?>
			<th><?=$field_name?></th>
		<? endforeach;?>
	</tr>
	<? foreach($items as $item):?>
		<?
			$style='';
			if ($item->error)
				$style='color:red;';
		?>
		<tr style="<?=$style?>">
		<? foreach($fields as $field):?>
			<?
				$value = $item->{$field};
				
				if ($field == 'images')
				{
					$images = explode(";",$value);
					$links = array();
					$i = 1;
					foreach($images as $image)
					{
						$links[]="<a href='".$image."' target='_blank'>".$i."</a>";
						$i++;
					}
					$value = implode(", ", $links);
				}
			?>
			<td><?=$value?></td>
		<? endforeach;?>
		</tr>
	<? endforeach;?>
</table>
