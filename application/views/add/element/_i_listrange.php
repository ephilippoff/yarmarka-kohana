<div class="range">
<? $parameters = array(	
							'name' 	=> $name."_min",
							'title' => $title,
							'class' => $class,
							'values'=> $values,
							'value' => $value
						); ?>

	от <?= View::factory( "add/element/_select", $parameters)->render(); ?>
- 
<? $parameters = array(	
							'name' 	=> $name."_max",
							'title' => $title,
							'class' => $class,
							'values'=> $values,
							'value' => $value
						); ?>
	до <?= View::factory( "add/element/_select", $parameters)->render(); ?>
<div>