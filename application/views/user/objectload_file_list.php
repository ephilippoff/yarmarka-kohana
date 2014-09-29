<p><?=$config["name"]?></p>
<table class="table table-hover table-condensed" style="width:100%">
	<tr>
		<? $statexist = FALSE;?>
		<? foreach($fields as $field):?>
			<?
				$field = str_replace("___", "-", $field);
				$field_name = $field;
				if (array_key_exists($field, $config['fields']))
					$field_name = $config['fields'][$field]['translate'];
			?>
			<?
				$field_visible = TRUE;
				if (in_array($field_name, $service_fields))
					$field_visible = FALSE;

				if (!$field_visible AND !$statexist)
				{
					$field_name = 'Сост.';
					$statexist =  TRUE;
					$field_visible = TRUE;
				}				

				if ($field_name == 'text_error')
					$field_name = "текст ош.";
				elseif ($field_name == 'object_id')
					$field_name = "ссылка на объявл.";

				if (!$field_visible)
					continue;
			?>

			<th><?=$field_name?></th>
		<? endforeach;?>
	</tr>
	<? foreach($items as $item):?>
		<?
			$style='';
			if ($item->error)
				$style='color:red;';
			if ($item->loaded)
				$style='color:green;';
		?>
		<tr style="<?=$style?>">
		<? $statexist = FALSE;?>
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
				elseif ($field == 'object_id' AND $value)
				{

					$value = "<a target='_blank' href='http://".Kohana::$config->load('common.main_domain')."/detail/".$value."'>Код:".$value."</a>";
				}
				elseif ($field == 'loaded')
				{
					$value = "";
					if (!$item->{"edited"} AND !$item->{"nochange"} AND !$item->{"error"})
						$value = 1;
				}

				$field_visible = TRUE;
				if (in_array($field, $service_fields))
					$field_visible = FALSE;

				if (!$field_visible AND !$statexist)
				{
					$statexist =  TRUE;
					$field_visible = TRUE;

					if (!$item->{"edited"} AND !$item->{"nochange"} AND !$item->{"error"})
						$value = "Создано новое";
					elseif ($item->{"edited"})
						$value = "Обновлено";
					elseif ($item->{"nochange"})
						$value = "Без изменений";
					elseif ($item->{"error"})
						$value = "Ошибка";
				}	

				if (!$field_visible)
					continue;	
			?>
			<td><?=$value?></td>
		<? endforeach;?>
		</tr>
	<? endforeach;?>
</table>
