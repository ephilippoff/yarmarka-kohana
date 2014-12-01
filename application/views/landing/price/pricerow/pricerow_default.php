<?

	$config = unserialize($priceload->config);
	$columns = explode(",",$config["columns"]);
?>
<tr>
	<? foreach($columns as $column): ?>
		<? if (isset($config[$column."_type"]) AND $config[$column."_type"] == "info"): ?>
			<th><?=$config[$column."_title"]?></th>
		<? endif; ?>
	<? endforeach; ?>
	<th>Описание</th>
	<th style="width:50px;">Цена</th>				
</tr>

<? foreach ($pricerows as $pricerow):?>
	<tr>
		<? foreach($pricerow["values"] as $value): ?>
					<td><?=$value?></td>
		<? endforeach; ?>
		<td><?=$pricerow["description"]?></td>
		<td style="width:80px;"><?=$pricerow["price"]?> р</td>		
	</tr>
<? endforeach; ?>