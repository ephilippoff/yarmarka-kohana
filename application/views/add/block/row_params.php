<div class="fn-rows-parameters">
	<? foreach ($data->rows as $element): ?>
		<?
		$parameters = array(
			'id' => $element["name"],
			'name' => $element["name"],
			'title' => $element["title"],
			'class' => "fn-param",
			'values' => $element['values'],
			'value' => $element['value'],
			'is_required' => $element['is_required'],
			'custom' => ''
		);
		?>

		<? if ($element["custom"]): ?>	
			<? $parameters["errors"] = $data->errors->{$element["name"]}; ?>						
		<?= View::factory("add/element/_" . $element["custom"], $parameters); ?>
	<? else: ?>
			<?php 
				if ($element['name'] == 'param_1000' && $isAdmin) {
					$parameters['class'] .= ' ckeditor';
					$parameters['custom'] = 'data-fileupload="true"';
				}
			?>
			<div class="row mb10" id="div_<?= $element["name"] ?>">
				<div class="col-md-3 col-xs-4 labelcont">
					<label><?= $element["title"] ?><?= ($element["unit"] ? ", " . $element["unit"] . ":" : ":") ?></label>
				</div>
				<div class="col-md-9 col-xs-8">

					<div class="row">

						<?
						$shortlong_class = "col-md-6";
						if ($element["type"] == "text")
							$shortlong_class = "col-md-12";
						?>					

						<div class="<?= $shortlong_class ?> <?= $element["type"] ?> is_textarea<?= $element["is_textarea"] ?>">
							<div class="inp-cont <? if ($data->errors->{$element["name"]}) echo "error"; ?>">
								<? if ($element["is_required"]): ?>
									<span class="required-star">*</span>
								<? endif; ?>																		
								<? if ($element["type"] == "integer"): ?>
									<?= View::factory("add/element/_integer", $parameters); ?>

								<? elseif ($element["type"] == "numeric"): ?>
									<?= View::factory("add/element/_numeric", $parameters); ?>

								<? elseif ($element["type"] == "text" AND $element["is_textarea"]): ?>
									<?= View::factory("add/element/_textarea", $parameters); ?>

								<? elseif ($element["type"] == "text" AND ! $element["is_textarea"]): ?>
									<?= View::factory("add/element/_text", $parameters); ?>

									<? endif; ?>
								<? if ($data->errors->{$element["name"]}): ?>
										<span class="inform fn-error">
											<?= $data->errors->{$element["name"]} ?>
										</span>
									<? endif; ?>					
							</div>
						</div>
					</div>

				</div>
			</div>	

	<? endif; ?>					

<? endforeach; ?>
</div>