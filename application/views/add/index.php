
<?//$this->carabiner->display('validate') ?>
    <div id="container">
		<div id="container2">
			
					
			
			<div id="add-center">
		<table><tr><td id="center-column" class="main-right2">
		  

			<div id="rubric-menu">
			<div id="add-menu">    
			<div class="steps">
			
				<form>

					<? if ( property_exists($form_data, 'city') ): ?>
				 		<?=$form_data->city;?>
					<? endif; ?>

				 	<? if ( property_exists($form_data, 'category') ): ?>
				 		<?=$form_data->category;?>
					<? endif; ?>

					<? if ( property_exists($form_data, 'subject') ): ?>
				 		<?=$form_data->subject;?>
					<? endif; ?>

					<? if ( property_exists($form_data, 'text') ): ?>
				 		<?=$form_data->text;?>
					<? endif; ?>

					

				</form>
			</div>
			</div>
			</div><!-- /rubric-body -->

		  </td>
		   </tr>
		 </table>
			</div>
		</div>

	</div>
	  

		  
	  
      <div class="clear-float"></div><!-- Сброс обтекания -->

