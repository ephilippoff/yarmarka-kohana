<select  name="pricelist" id="price_id">
	<option value>---</option>
	<? foreach($data->prices as $price) : ?>
		<option value="<?=$price->id?>" <?if ($price->id == $data->value) { echo "selected"; } ?> ><?=$price->title?> (от <?=$price->created_on?>)</option>
	<? endforeach; ?>
</select>