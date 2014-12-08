<? foreach ($pricerows as $pricerow):?>
	<tr>
		<? foreach($pricerow["values"] as $value): ?>
					<td><?=$value?></td>
		<? endforeach; ?>
		<td><?=$pricerow["description"]?></td>
		<td><?=$pricerow["price"]?> р</td>		
	</tr>
<? endforeach; ?>