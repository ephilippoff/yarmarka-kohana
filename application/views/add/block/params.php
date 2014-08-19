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

		<? if ($element["custom"]): ?>
			<?= View::factory( "add/element/_".$element["custom"], $parameters); ?>
		<? else: ?>
			<? if ($element["type"] == "list" OR ($element["type"] == "ilist")): ?>
				<?= View::factory( "add/element/_select", $parameters); ?>
			
			<? elseif ($element["type"] == "integer"): ?>
				<?= View::factory( "add/element/_integer", $parameters); ?>

			<? elseif ($element["type"] == "numeric"): ?>
				<?= View::factory( "add/element/_numeric", $parameters); ?>

			<? elseif ($element["type"] == "text"): ?>
				<?= View::factory( "add/element/_text", $parameters); ?>

			<? endif; ?>
		<? endif; ?>
		
		<? if ($data->errors->{$element["name"]}): ?>
			<span class="inform">
				<span><?=$data->errors->{$element["name"]}?></span>
			</span>
		<? endif; ?>
		</div>
	</div>

<? endforeach; ?>	

<? foreach($data->customs as $custom): ?>
	
	<? $parameters = array(	
							'id' 	=> $element["name"],
							'name' 	=> $custom["name"],
							'title' => $custom["title"],
							'class' => "",
							'values'=> $custom['values'],
							'value' => $custom['value']
						); ?>

	<?= View::factory( "add/element/_".$custom["custom"], $parameters); ?>

<? endforeach; ?>	
