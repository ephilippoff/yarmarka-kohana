<? foreach($data->elements as $element): ?>
	<div class="inp-cont-short" data-condition="">
		<div class="inp-cont <? if ($data->errors->{$element["name"]}) echo "error";?>">
		<span class="required-label">*</span>
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
			<span class="inform">
				<span><?=$data->errors->{$element["name"]}?></span>
			</span>
		<? endif; ?>
		</div>
	</div>

<? endforeach; ?>	
