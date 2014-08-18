
<?//$this->carabiner->display('validate') ?>
    <div id="container">
		<div id="container2">
			
					
			
			<div id="add-center">
		<table><tr><td id="center-column" class="main-right2">
		  

			<div id="rubric-menu">
			<div id="add-menu">    
			<div class="steps">
			
				<form method="POST">

					<? if ( property_exists($form_data, 'city') ): ?>
				 		<?=$form_data->city;?>
					<? endif; ?>
					<br/>
					<? if ( property_exists($form_data, 'other_cities') ): ?>
				 		<?=$form_data->other_cities;?>
					<? endif; ?>
					<br/>
				 	<? if ( property_exists($form_data, 'category') ): ?>
				 		<?=$form_data->category;?>
					<? endif; ?>
					<br/>
					<div class="fn-parameters">
					 	<? if ( property_exists($form_data, 'params') ): ?>
					 		<?=$form_data->params;?>
						<? endif; ?>
					</div>
					<br/>
					<? if ( property_exists($form_data, 'map') ): ?>
				 		<?=$form_data->map;?>
					<? endif; ?>
					<br/>
					<? if ( property_exists($form_data, 'subject') ): ?>
				 		<?=$form_data->subject;?>
					<? endif; ?>
					<br/>
					<? if ( property_exists($form_data, 'text') ): ?>
				 		<?=$form_data->text;?>
					<? endif; ?>
					<br/>
					<? if ( property_exists($form_data, 'photo') ): ?>
				 		<?=$form_data->photo;?>
					<? endif; ?>
					<br/>
					<? if ( property_exists($form_data, 'video') ): ?>
				 		<?=$form_data->video;?>
					<? endif; ?>
					<br/>
					<? if ( property_exists($form_data, 'contacts') ): ?>
				 		<?=$form_data->contacts;?>
					<? endif; ?>
					<br/>
					<? if ( property_exists($form_data, 'options') ): ?>
				 		<?=$form_data->contacts;?>
					<? endif; ?>

					
					<input type="submit" value="Далее"/>
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
	  

	<script id="template-integer" type="text/template">
		<%=title%><input  id="<%=id%>" name="<%=id%>" class="<%=classes%>" type="text" value="<%=value%>"/>
	</script>

	<script id="template-numeric" type="text/template">
		<%=title%><input  id="<%=id%>" name="<%=id%>" class="<%=classes%>" type="text" value="<%=value%>"/>
	</script>

	<script id="template-text" type="text/template">
		<%=title%><input  id="<%=id%>" name="<%=id%>" class="<%=classes%>" type="text" value="<%=value%>"/>
	</script>	

	<script id="template-list" type="text/template">
		<select id="<%=id%>" name="<%=id%>" class="<%=classes%>">
	  		<option>--<%=title%>--</option>
	  	</select> 
	</script>		  
	  
      <div class="clear-float"></div><!-- Сброс обтекания -->
<?=HTML::script('js/adaptive/addapp.js')?>