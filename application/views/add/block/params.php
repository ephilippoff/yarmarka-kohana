<div class="fn-list-parameters">
<? foreach($data->elements as $element): ?>
	<? 
		$whitespace = "";
		if ($element["options"] == "whitespace")
			$whitespace = "whitespace";
	?>
	<div class="inp-cont-short <?=$whitespace?>" id="div_<?=$element["name"]?>">
		<div class="inp-cont <? if ($data->errors->{$element["name"]}) echo "error";?>">
		<? if ($element["is_required"]):?>
			<span class="required-label">*</span>
		<? endif; ?>
		<? $parameters = array(	
								'id' 	=> $element["name"],
								'name' 	=> $element["name"],
								'title' => $element["title"],
								'class' => "fn-param",
								'values'=> $element['values'],
								'value' => $element['value']
							); ?>

			<? if ($element["type"] == "list" OR ($element["type"] == "ilist")): ?>
				<?= View::factory( "add/element/_select", $parameters); ?>
			<? endif; ?>
		
		<? if ($data->errors->{$element["name"]}): ?>
			<span class="inform fn-error">
				<span><?=$data->errors->{$element["name"]}?></span>
			</span>
		<? endif; ?>
		</div>
	</div>

<? endforeach; ?>	
</div>
