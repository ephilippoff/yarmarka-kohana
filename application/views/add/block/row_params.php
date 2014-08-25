<div class="fn-rows-parameters">
	<? foreach($data->rows as $element): ?>
		
		<? $parameters = array(	
								'id' 	=> $element["name"],
								'name' 	=> $element["name"],
								'title' => $element["title"],
								'class' => "fn-param",
								'values'=> $element['values'],
								'value' => $element['value'],
								'is_required' => $element['is_required']
							); ?>
		<? if ($element["custom"]): ?>	
			<? $parameters["errors"] =  $data->errors->{$element["name"]}; ?>						
			<?= View::factory( "add/element/_".$element["custom"], $parameters); ?>
		<? else: ?>
			<div class="smallcont" id="div_<?=$element["name"]?>">
				<div class="labelcont">
					<label><span><?=$element["title"]?></span></label>
				</div>
				<div class="fieldscont">
					<? $shortlong_class = "inp-cont-short"; 
						if ($element["type"] == "text")
							$shortlong_class = "inp-cont-long";
					?>
					<div class="<?=$shortlong_class?>">
						<div class="inp-cont <? if ($data->errors->{$element["name"]}) echo "error";?>">
							<? if ($element["is_required"]):?>
							<span class="required-label">*</span>
							<? endif; ?>
							
								<? if ($element["type"] == "integer"): ?>
									<?= View::factory( "add/element/_integer", $parameters); ?>

								<? elseif ($element["type"] == "numeric"): ?>
									<?= View::factory( "add/element/_numeric", $parameters); ?>

								<? elseif ($element["type"] == "text" AND $element["is_textarea"]): ?>
									<?= View::factory( "add/element/_textarea", $parameters); ?>

								<? elseif ($element["type"] == "text" AND !$element["is_textarea"]): ?>
									<?= View::factory( "add/element/_text", $parameters); ?>

								<? endif; ?>
							<? if ($data->errors->{$element["name"]}): ?>
								<span class="inform fn-error">
									<span><?=$data->errors->{$element["name"]}?></span>
								</span>
							<? endif; ?>
						</div>
					</div>
				</div>									
			</div>

		<? endif; ?>					
						
	<? endforeach; ?>
</div>