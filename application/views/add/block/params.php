<? foreach($elements as $element): ?>
	
	<? $parameters = array(	
							'id' 	=> $element["name"],
							'name' 	=> $element["name"],
							'title' => $element["title"],
							'class' => "fn-param",
							'values'=> $element['values'],
							'value' => $element['value']
						); ?>

	<? if ($element["custom"]): ?>
		<?= View::factory( "add/element/_".$element["custom"], $parameters)->render(); ?>
	<? else: ?>
		<? if ($element["type"] == "list" OR ($element["type"] == "ilist")): ?>
			<?= View::factory( "add/element/_select", $parameters)->render(); ?>
		
		<? elseif ($element["type"] == "integer"): ?>
			<?= View::factory( "add/element/_integer", $parameters)->render(); ?>

		<? elseif ($element["type"] == "numeric"): ?>
			<?= View::factory( "add/element/_numeric", $parameters)->render(); ?>

		<? elseif ($element["type"] == "text"): ?>
			<?= View::factory( "add/element/_text", $parameters)->render(); ?>

		<? endif; ?>
	<? endif; ?>

<? endforeach; ?>	

<? foreach($customs as $custom): ?>
	
	<? $parameters = array(	
							'id' 	=> $element["name"],
							'name' 	=> $custom["name"],
							'title' => $custom["title"],
							'class' => "",
							'values'=> $custom['values'],
							'value' => $custom['value']
						); ?>

	<?= View::factory( "add/element/_".$custom["custom"], $parameters)->render(); ?>

<? endforeach; ?>	
