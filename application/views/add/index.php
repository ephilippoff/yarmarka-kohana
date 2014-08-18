<div class="winner">
	<section class="main-cont">
		<div class="hheader">
			<h1 class="ta-c"><span><strong>Подача объявления:</strong> Шаг 2. Редактирование</span></h1>
		</div><!--hheader-->


		<form method="POST"  id="element_list">

			<? if ( property_exists($form_data, 'city') ): ?>
			<div class="fl100  pt16 pb15">
					<div class="smallcont">
						<div class="labelcont">
							<label><span>Город:</span></label>
						</div>	
						<div class="fieldscont">
							<div class="inp-cont-short ">
								<div class="inp-cont">
									<span class="required-label">*</span>
									<?=View::factory('add/block/city',
											array( "data" 		=> new Obj($form_data->city),
												   "_class" 	=> "",
												   "name" 		=> "city_id",
												   "id" 		=> "",
												   "attributes" => ""
											));?>	
									<span class="inform" style="display: none;">
										<span><?/*inform message */?>
										</span>
									</span>
								</div>
							</div><!--inp-cont-short-->
						</div><!--fieldscont-->
					</div> <!-- smallcont -->
			</div>
			<? endif; ?>

			<? if ( property_exists($form_data, 'category') ): ?>
				<div class="fl100">
					<div class="smallcont">
						<div class="labelcont">
							<label><span>Раздел:</span></label>
						</div>
						<div class="fieldscont">				 						
							<div class="inp-cont-short">
								<div class="inp-cont">
									<span class="required-label">*</span>

									<?=View::factory('add/block/category',
											array( "data" 		=> new Obj($form_data->category),
												   "_class" 	=> "",
												   "name" 		=> "rubricid",
												   "id" 		=> "fn-category",
												   "attributes" => ""
											));?>		

									<span class="inform" style="display: none;">
										<span><?/*inform message */?>
										</span>
									</span>
								</div> <!--inp-cont -->
							</div> <!-- inp-cont-short -->


						</div> <!-- fieldscont -->		
					</div>	 <!-- smallcont --> 
				</div>  <!-- fl100 -->
			<? endif; ?>

			<? if ( property_exists($form_data, 'params') ): ?>
				<div class="smallcont">
					<div class="labelcont">
						<label><span>Параметры:</span></label>
					</div>
					<div class="fieldscont group fn-parameters">
						<?=View::factory('add/block/params',
							array( "data" 		=> new Obj($form_data->params),
								   "_class" 	=> "",
								   "name" 		=> "",
								   "id" 		=> "",
								   "attributes" => ""
							));?>					
					</div>									
				</div><!--smallcont-->
			<? endif; ?>

			<? if ( property_exists($form_data, 'map') ): ?>

				<div class="smallcont">
					<div class="labelcont">
							<label><span>Адрес:</span></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-long">
							<div class="inp-cont"><span class="required-label">*</span>
																		
								<input type="text" class="for_map" name="address" id="address_selector" value=""/>
							
								<span class="inform" style="display: none;">
									<span><?/*inform message */?>
									</span>
								</span>							
							</div>							
						</div>
					</div><!--fieldscont-->
				</div><!--smallcont-->	

			
		 		<?=$form_data->map;?>
			<? endif; ?>

			<? if ( property_exists($form_data, 'subject') ): ?>
				<div class="smallcont">
					<div class="labelcont">
							<label><span>Заголовок:</span></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-long">
							<div class="inp-cont"><span class="required-label">*</span>																		
								<?=View::factory('add/block/subject',
											array( "data" 		=> new Obj($form_data->subject),
												   "_class" 	=> "",
												   "name" 		=> "title_adv",
												   "id" 		=> "title_adv",
												   "attributes" => ""
											));?>							
								<span class="inform" style="display: none;">
									<span><?/*inform message */?>
									</span>
								</span>
							</div>							
						</div>
					</div><!--fieldscont-->
				</div><!--smallcont-->	
			<? endif; ?>

			<? if ( property_exists($form_data, 'text') ): ?>
				<div class="smallcont">
					<div class="labelcont">
						<label><span>Текст объявления:</span></label>
					</div>		
					<div class="fieldscont user-text mb15">
						<div class="inp-cont-long ">
							<div class="inp-cont star">
								<span class="required-label">*</span>
								<div class="textarea user_text_adv_wrp">
									<?=View::factory('add/block/text',
											array( "data" 		=> new Obj($form_data->text),
												   "_class" 	=> "",
												   "name" 		=> "user_text_adv",
												   "id" 		=> "user_text_adv",
												   "attributes" => ""
											));?>	
								</div>
								<span class="inform">
									<span id="charleft_user_text_adv" class="charleft"></span>
								</span>
								<span class="inform" style="display: none;">
									<span><?/*inform message */?>
									</span>
								</span>
							</div>
						</div>
					</div>
				</div>
			<? endif; ?>

			<? if ( property_exists($form_data, 'photo') ): ?>					
				<div class="smallcont add-photo">
					<div class="labelcont">
						<label><span>Фото:</span></label>
					</div>
					
					<div class="fieldscont">
						<div class="inp-cont-long">
							<div class="inp-cont">
								<div id="add-block" class="add-block" data-max="8">
									<?=View::factory('add/block/photo',
										array( "data" 		=> new Obj($form_data->photo),
											   "_class" 	=> "",
											   "name" 		=> "",
											   "id" 		=> "",
											   "attributes" => ""
										));?>
									<? /*
									<div id="addbtn" class="addbtn"><span class="add">Нажмите <span id="userfile_upload" class="span">сюда</span><br/> чтобы добавить новые фото</span></div>
									*/?>
									<input type="hidden" name="active_userfile" value="" />
								</div>
								
								<span class="inform">
									<span>Главным по умолчанию является первое фото, щелкните по любому фото, чтобы сделать его главным</span>
								</span>
								<input class="mb10" type="file" value="" id="btn-photo-load" name="photo" class="btn-photo-load">
								<div class="mb10 red" id="error_userfile1"></div>
							</div>
						</div>
					</div>
				</div>
			<? endif; ?>

			<? if ( property_exists($form_data, 'contacts') ): ?>
				<div class="fl100 add-coord-cont">	                    		
						<div class="smallcont">
							<div class="labelcont">
								<label><span>Контакты:</span></label>
							</div>	
							<div class="fieldscont">

								<div id="contacts" class="inp-cont-short">	                  			
									<div class="inp-cont star">
										<span class="required-label">*</span>						
										<input type="text" name="contact" placeholder="Контактное лицо" />						
										<span class="inform" style="display: none;">
											<span><?/*inform message */?>
											</span>
										</span>
									</div>
								</div>

								<div id="contacts2" class="contacts-cont fn-contacts-container">
									<?=View::factory('add/block/contacts',
											array( "data" 		=> new Obj($form_data->contacts),
												   "_class" 	=> "",
												   "name" 		=> "",
												   "id" 		=> "",
												   "attributes" => ""
											));?>
								</div>


							</div>
						</div>
				</div>
			<? endif; ?>

			<div class="fl100 form-next-cont">
				<div class="smallcont">
					<div class="labelcont"></div>	
					<div class="fieldscont ta-r mb15">						
						<div onclick="$('#element_list').submit()" class="button blue icon-arrow-r btn-next"><span>Далее</span></div>							
					</div><!--fieldscont-->
				</div><!--smallcont-->	
			</div>

		</form>

	</section><!--main-cont add-ad-->
</div><!--end content winner-->

<?/*

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
	  
*/?>

<script id="template-integer" type="text/template">
	<div class="inp-cont-short" data-condition="">
		<div class="inp-cont">
			<input  id="<%=id%>" name="<%=id%>" class="<%=classes%>" type="text" value="<%=value%>" placeholder="<%=title%>"/>
		</div>
	</div>
</script>

<script id="template-numeric" type="text/template">
	<div class="inp-cont-short" data-condition="">
		<div class="inp-cont">
			<input  id="<%=id%>" name="<%=id%>" class="<%=classes%>" type="text" value="<%=value%>" placeholder="<%=title%>"/>
		</div>
	</div>
</script>

<script id="template-text" type="text/template">
	<div class="inp-cont-short" data-condition="">
		<div class="inp-cont">
		<span class="required-label">*</span>
			<input  id="<%=id%>" name="<%=id%>" class="<%=classes%>" type="text" value="<%=value%>"  placeholder="<%=title%>"/>
		</div>
	</div>
</script>	

<script id="template-list" type="text/template">
	<div class="inp-cont-short" data-condition="">
		<div class="inp-cont">
		<span class="required-label">*</span>
			<select id="<%=id%>" name="<%=id%>" class="<%=classes%>">
		  		<option>--<%=title%>--</option>
		  	</select> 
  		</div>
	</div>
</script>		  


<?=HTML::script('js/adaptive/addapp.js')?>