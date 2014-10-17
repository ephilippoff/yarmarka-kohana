<div class="winner">
	<section class="main-cont">
		<div class="hheader">
			<h1 class="ta-c"><span><strong>
			<? if ($form_data->_edit):?>
				Редактирование объявления
			<? else:?>
				Новое объявление
			<? endif;?>
			</strong></span></h1>
		</div><!--hheader-->

		<? $problem = Kohana::$config->load("common.add_problem"); ?>
		<? if($problem):?>
			<div class="fl100  pt16 pb15">
					<div class="smallcont">
						<div class="labelcont">
							<label><span style="color:red;">!</span></label>
						</div>
						<div class="fieldscont">
							<div class="inp-cont-short">
								<div class="inp-cont"  style="color:red;">
									<?=$problem?>
								</div>
							</div>
						</div>									
					</div>
			</div>
		<? endif; ?>

		<form method="POST"  id="element_list">			
  			<input type="hidden" name="object_id" id="object_id" value="<?=$params->object_id?>">
  			<input type="hidden" name="session_id" value="<?=session_id()?>">

  			<? if ( property_exists($form_data, 'login') ): ?>
			<div class="fl100  pt16 pb15">
				<div class="smallcont">
					<div class="labelcont">
						<label><span>Логин</span></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<div class="inp-cont <? if ($form_data->login["login_error"]) echo "error";?>">
								<span class="required-label">*</span>
								
								<input type="text" name="login" value="<? if ($params->login) echo $params->login;?>"/>
									
								<? if ($form_data->login["login_error"]): ?>
								<span class="inform fn-error">
									<span><?=$form_data->login["login_error"]?></span>
								</span>
								<? endif; ?>
							</div>
						</div>
					</div>									
				</div>

				<div class="smallcont">
					<div class="labelcont">
						<label><span>Пароль</span></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<div class="inp-cont <? if ($form_data->login["pass_error"]) echo "error";?>">
								<span class="required-label">*</span>
								
								<input type="password" name="pass" value="<? if ($params->pass) echo $params->pass;?>"/>
									
								<? if ($form_data->login["pass_error"]): ?>
								<span class="inform fn-error">
									<span><?=$form_data->login["pass_error"]?></span>
								</span>
								<? endif; ?>
							</div>
						</div>
					</div>									
				</div>
				
				<div class="smallcont">
					<div class="labelcont">
						<label></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<a target="_blank" href="http://<?=Kohana::$config->load('common.main_domain')?>/user/registration">Зарегистрироваться</a>
						</div>
					</div>
				</div>	

				<div class="smallcont">
					<div class="labelcont">
						<label></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-short">
							<a target="_blank" href="/user/forgot_password">Напомнить/сменить пароль</a>
						</div>
					</div>
				</div>	
			</div>
			<? endif; ?>

			<? if ( property_exists($form_data, 'city') ): ?>
			<div class="fl100  pt16 pb15"  id="div_city">
					<div class="smallcont">
						<div class="labelcont">
							<label><span>Город:</span></label>
						</div>	
						<div class="fieldscont">
							<div class="inp-cont-short ">
								<div class="inp-cont <?if ($form_data->city["city_error"]) echo "error";?>">
									<span class="required-label">*</span>
									<?=View::factory('add/block/city',
											array( "data" 		=> new Obj($form_data->city),
												   "_class" 	=> "",
												   "name" 		=> "city_id",
												   "id" 		=> "",
												   "attributes" => ""
											));?>	
									<? if ($form_data->city AND $form_data->city["city_error"]): ?>
										<span class="inform fn-error">
											<span><?=$form_data->city["city_error"]?></span>
										</span>
									<? endif; ?>
								</div>
							</div><!--inp-cont-short-->
						</div><!--fieldscont-->
					</div> <!-- smallcont -->
			</div>
			<? endif; ?>

			<div id="div_category">
			<? if ( property_exists($form_data, 'category') ): ?>
				<div class="fl100" id="div_category">
					<div class="smallcont">
						<div class="labelcont">
							<label><span>Раздел:</span></label>
						</div>
						<div class="fieldscont">				 						
							<div class="inp-cont-short">
								<div class="inp-cont <?if ($form_data->category["category_error"]) echo "error";?>">
									<span class="required-label">*</span>

									<?=View::factory('add/block/category',
											array( "data" 		=> new Obj($form_data->category),
												   "_class" 	=> "",
												   "name" 		=> "rubricid",
												   "id" 		=> "fn-category",
												   "attributes" => ""
											));?>		

									<? if ($form_data->category AND $form_data->category["category_error"]): ?>
										<span class="inform fn-error">
											<span><?=$form_data->category["category_error"]?></span>
										</span>
									<? endif; ?>
								</div> <!--inp-cont -->
							</div> <!-- inp-cont-short -->


						</div> <!-- fieldscont -->		
					</div>	 <!-- smallcont --> 
				</div>  <!-- fl100 -->

				<div class="fl100" id="div_category_description">

				</div>
			<? endif; ?>
			</div>

			<? if ( property_exists($form_data, 'advert_type') ): ?>

			<div class="fl100" id="div_advert_type">
				<div class="smallcont">
					<div class="labelcont">
						<label><span>Тип объявления:</span></label>
					</div>
					<div class="fieldscont">				 						
						<div class="inp-cont-short">
							<div class="inp-cont">
								<span class="required-label">*</span>	
								<?=Form::select("obj_type", $form_data->advert_type['type_list'], $form_data->advert_type['value']); ?>
							</div> <!--inp-cont -->
						</div> <!-- inp-cont-short -->


					</div> <!-- fieldscont -->		
				</div>	 <!-- smallcont --> 
			</div>  <!-- fl100 -->
				

			<? endif; ?>

			<div id="div_params">
			<? if ( property_exists($form_data, 'params') ): ?>
				<div class="fn-parameters">
				<? if (count($form_data->params['elements'])>0):?>
					<div class="smallcont">
						<div class="labelcont">
							<label><span>Параметры:</span></label>
						</div>
						<div class="fieldscont group">
							<?=View::factory('add/block/params',
								array( "data" 		=> new Obj($form_data->params),
									   "_class" 	=> "",
									   "name" 		=> "",
									   "id" 		=> "",
									   "attributes" => ""
								));?>					
						</div>									
					</div><!--smallcont-->
				<? endif;?>

				<?=View::factory('add/block/row_params',
					array( "data" 		=> new Obj($form_data->params),
						   "_class" 	=> "",
						   "name" 		=> "",
						   "id" 		=> "",
						   "attributes" => ""
					));?>	
				</div>
			<? endif; ?>
			</div>


			<div class="smallcont hidden" id="div_map">
				<div class="labelcont"><span class="label-geo">Укажите местоположение объекта на карте</span></div>
				<div class="fieldscont">
					<div id="map_block_div" class="map_block_div add_form_info inp-cont-long mb20">
						<div class="map" id="map_block" style="height:250px;width:100%;">		
						
							<input type="hidden" id="object_coordinates" name="object_coordinates" value="<? if ($form_data->object_coordinates) echo $form_data->object_coordinates;?>"/>
						</div>				
					</div><!--#map_block_div-->
				</div><!--fieldscont-->
			</div><!--smallcont-->

			<div id="div_subject">

			<? if ( property_exists($form_data, 'subject') ): ?>
				<div class="smallcont" id="div_subject">
					<div class="labelcont">
							<label><span>Заголовок:</span></label>
					</div>
					<div class="fieldscont">
						<div class="inp-cont-long">
							<div class="inp-cont <?if ($form_data->subject["subject_error"]) echo "error";?>">
								<span class="required-label">*</span>																		
								<?=View::factory('add/block/subject',
											array( "data" 		=> new Obj($form_data->subject),
												   "_class" 	=> "",
												   "name" 		=> "title_adv",
												   "id" 		=> "title_adv",
												   "attributes" => ""
											));?>							
								<? if ($form_data->subject AND $form_data->subject["subject_error"]): ?>
									<span class="inform">
										<span><?=$form_data->subject["subject_error"]?></span>
									</span>
								<? endif; ?>
							</div>							
						</div>
					</div><!--fieldscont-->
				</div><!--smallcont-->	
			<? endif; ?>
			</div>

			<div id="div_textadv">
			<? if ( property_exists($form_data, 'text') ): ?>
				<div class="smallcont">
					<div class="labelcont">
						<label><span>Текст объявления:</span></label>
					</div>		
					<div class="fieldscont user-text mb15">
						<div class="inp-cont-long ">
							<div class="inp-cont <?if ($form_data->text["text_error"]) echo "error";?>">
								<? if ($form_data->text_required): ?>
									<span class="required-label">*</span>
								<? endif; ?>
								<div class="textarea user_text_adv_wrp">
									<?=View::factory('add/block/text',
											array( "data" 		=> new Obj($form_data->text),
												   "_class" 	=> "user_text_adv",
												   "name" 		=> "user_text_adv",
												   "id" 		=> "user_text_adv",
												   "attributes" => ""
											));?>	
								</div>
								<? if ($form_data->text AND $form_data->text["text_error"]): ?>
									<span class="inform">
										<span><?=$form_data->text["text_error"]?></span>
									</span>
								<? endif; ?>
							</div>
						</div>
					</div>
				</div>
			<? endif; ?>
			</div>

			<div id="div_photo">
			<? if ( property_exists($form_data, 'photo') ): ?>					
				<div class="smallcont add-photo">
					<div class="labelcont">
						<label><span>Фото:</span></label>
					</div>
					
					<div class="fieldscont">
						<div class="inp-cont-long">
							<div class="inp-cont">
								<div id="add-block" class="add-block fn-photo-list" data-max="8">
									<?=View::factory('add/block/photo',
										array( "data" 		=> new Obj($form_data->photo),
											   "_class" 	=> "",
											   "name" 		=> "",
											   "id" 		=> "",
											   "attributes" => ""
										));?>
									
									<div id="addbtn" class="addbtn"><span class="add"><img src="/images/plus_icon.png" /><span id="userfile_upload" class="span">Загрузите фото</span></span></div>
								</div>
								<input type="hidden" id="active_userfile" name="active_userfile" value="<?=$params->active_userfile?>" />
								<span class="inform">
									<span>Главным по умолчанию является первое фото, щелкните по любому фото, чтобы сделать его главным</span>
								</span>
								<? /*<input class="mb10" type="file" value="" id="btn-photo-load" name="photo" class="btn-photo-load">*/ ?>
								<div class="mb10 red" id="error_userfile1"></div>
							</div>
						</div>
					</div>
				</div>
			<? endif; ?>
			</div>

			<? if ( property_exists($form_data, 'contacts') ): ?>
				<div class="fl100 add-coord-cont" id="div_contacts">	                    		
						<div class="smallcont">
							<div class="labelcont">
								<label><span>Контакты:</span></label>
							</div>	
							<div class="fieldscont">

								<div id="contacts2" class="contacts-cont fn-contacts-container">
									<?=View::factory('add/block/contacts',
											array( "data" 		=> new Obj($form_data->contacts),
												   "_class" 	=> "",
												   "name" 		=> "",
												   "id" 		=> "",
												   "attributes" => ""
											));?>
								</div>

								<span title="Добавить контакт"  class="add-contact like-link fn-add-contact-button-text">Добавить еще телефон или email</span>						

							</div>
						</div>
				</div>

				<div class="fl100 add-coord-cont">	     
					<div class="smallcont">
								<div class="labelcont">
									<label><span>Контактное лицо:</span></label>
								</div>	
								<div class="fieldscont">
									<div id="contacts" class="inp-cont-short">	                  			
										<div class="inp-cont <?if ($form_data->contacts["contact_error"]) echo "error";?>">
											<span class="required-label">*</span>						
											<input type="text" name="contact" value="<?=$form_data->contacts["contact_person"]?>"/>						
											<? if ($form_data->contacts AND $form_data->contacts["contact_error"]): ?>
												<span class="inform">
													<span><?=$form_data->contacts["contact_error"]?></span>
												</span>
											<? endif; ?>
										</div>
									</div>

								</div>
					</div>
				</div>
			<? endif; ?>

			<div class="fl100">	                    					
					<div class="smallcont">
						<div class="labelcont">
							<label><span>Срок жизни объявления:</span></label>
						</div>	
						<div class="fieldscont">
							<div class="inp-cont-short">
								<div class="inp-cont">
									<div data-placeholder="Выберите срок жизни объявления..." class="values">
										<?
										 	$periods = array(	"2w" => "14 дней",
										 						"1m" => "30 дней",
										 						"2m" => "60 дней",
										 						"3m" => "90 дней");
										 	$default_period = "2m";
										 	if ($params->lifetime)
										 		$default_period = $params->lifetime;
										?>
										<? if ($form_data->_edit):?>
											до <?=$object->date_expiration?>
										<? else: ?>
											 <select id="lifetime" name="lifetime">
											 <? foreach ($periods as $key => $value): ?>
											 		<option value="<?=$key?>" <? if ($default_period == $key) echo "selected";?>><?=$value?></option>
											 <? endforeach; ?>
										<? endif; ?>
										</select>
									</div>					

								</div>						
							</div>
						</div><!--fieldscont-->
					</div><!--smallcont-->														

					<div class="smallcont inp-add-cont">
						<div class="labelcont"></div>					
						<div class="fieldscont mb15">		                    				                    			
							<div class="inp-cont-long ">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="block_comments" id="block_comments" value="1" <? if ($params->block_comments) echo "checked"; ?>></input>
											<span for="block_comments" class="">Отключить возможность задавать вопросы на странице объявления</span>
										</label>	
									</div>
							</div>
						</div>
					</div><!--smallcont inp-add-cont-->

			</div>

			<? if (count($errors)>0) :?>
			<div class="fl100 form-errors-cont">
				<div class="smallcont">
					<div class="labelcont"></div>
					<div class="fieldscont">
						<p class="error-msg">Исправьте ошибки в форме, чтобы продолжить:</p>
						<? foreach (array_values($errors) as $error):?>
							<p class="error-msg"><?=$error?></p>
						<? endforeach;?>
					</div>
				</div>
			</div>	
			<? endif; ?>

			<div id="div_submit">
				<div class="fl100 form-next-cont">
					<div class="smallcont">
						<div class="labelcont"></div>	
						<div class="fieldscont ta-r mb15">						
							<div id="submit_button" class="button blue icon-arrow-r btn-next"><span>Далее</span></div>							
						</div><!--fieldscont-->
					</div><!--smallcont-->	
				</div>
			</div>

			

		</form>

	</section><!--main-cont add-ad-->
</div><!--end content winner-->

<!--javascript templates-->

<script id="template-integer" type="text/template">
	<div class="smallcont" id="div_<%=id%>">
			<div class="labelcont">
				<label><span><%=title%><%=(unit)?", "+unit+":":":"%></span></label>
			</div>
			<div class="fieldscont">
				<div class="inp-cont-short" data-condition="">
					<div class="inp-cont">
						<% if (is_required) { %>
							<span class="required-label">*</span>
						<% } %>
						<input  id="<%=id%>" name="<%=id%>" class="<%=classes%>" type="text" value="<%=value%>"/>
					</div>
				</div>
			</div>
	</div>
</script>

<script id="template-numeric" type="text/template">
		<div class="smallcont" id="div_<%=id%>">
			<div class="labelcont">
				<label><span><%=title%><%=(unit)?", "+unit+":":":"%></span></label>
			</div>
			<div class="fieldscont">
				<div class="inp-cont-short" data-condition="">
					<div class="inp-cont">
						<% if (is_required) { %>
							<span class="required-label">*</span>
						<% } %>
						<input  id="<%=id%>" name="<%=id%>" class="<%=classes%>" type="text" value="<%=value%>"/>

					</div>
				</div>
			</div>
	</div>
</script>

<script id="template-text" type="text/template">
	<div class="smallcont" id="div_<%=id%>">
			<div class="labelcont">
				<label><span><%=title%><%=(unit)?", "+unit+":":":"%></span></label>
			</div>
			<div class="fieldscont">
				<div class="inp-cont-long" data-condition="">
					<div class="inp-cont">
						<% if (is_required) { %>
							<span class="required-label">*</span>
						<% } %>
						<input id="<%=id%>" type="text" name="<%=name%>" value="<%=value%>"/>
					</div>
				</div>
			</div>
	</div>
</script>

<script id="template-textarea" type="text/template">
	<div class="smallcont" id="div_<%=id%>">
			<div class="labelcont">
				<label><span><%=title%></span></label>
			</div>
			<div class="fieldscont">
				<div class="inp-cont-long" data-condition="">
					<div class="inp-cont">
						<% if (is_required) { %>
							<span class="required-label">*</span>
						<% } %>
						<textarea id="<%=id%>" name="<%=id%>" class="<%=classes%>"><%=value%></textarea>
					</div>
				</div>
			</div>
	</div>
</script>	

<script id="template-list" type="text/template">
	<div class="inp-cont-short" id="div_<%=id%>">
		<div class="inp-cont">
		<% if (is_required) { %>
			<span class="required-label">*</span>
		<% } %>
			<select id="<%=id%>" name="<%=id%>" class="<%=classes%>">
		  		<option value="">--<%=title%><%=(unit)?", "+unit:""%>--</option>
		  		<% _.each(data, function(item, key){ %>
		  			<% if (key == 0) return; %>
		  			<%  if (typeof item == "object") item = item[0].title %>


		  			<option value="<%=key%>"><%=item%></option>
		  		<% }); %>
		  	</select> 
  		</div>
	</div>
</script>

<script id="template-custom-address" type="text/template">
	<div class="smallcont" id="div_<%=id%>">
				<div class="labelcont">
					<label><span><%=title%><%=(unit)?", "+unit+":":":"%></span></label>
				</div>
				<div class="fieldscont">
					<div class="inp-cont">
						<div class="inp-cont-long">
							<% if (is_required) { %>
								<span class="required-label">*</span>
							<% } %>
							
							<input id="<%=id%>" type="text" name="<%=id%>" value="<%=value%>"/>

							<span class="inform">
								<span>Например: ул. Мельникайте, д. 44, корп. 2</span>
							</span>
						</div>
				</div>
			</div>									
	</div>
</script>

<script id="template-custom-multiselect" type="text/template">
	<div class="smallcont" id="div_<%=id%>">
				<div class="labelcont">
					<label><span><%=title%><%=(unit)?", "+unit+":":":"%></span></label>
				</div>
				<div class="fieldscont">
					<div class="inp-cont">
						<div class="inp-cont-long">
							<% if (is_required) { %>
								<span class="required-label">*</span>
							<% } %>
							<select id="<%=id%>" name="<%=id%>[]" class="<%=classes%>" multiple style="height:<%=(_.values(data).length*15)%>px;">
								<% if (!is_required) { %>
						  			<option value="">--нет--</option>
						  		<% } %>
						  		<% _.each(data, function(item, key){ %>
						  			<% if (key == 0) return; %>
						  			<%  if (typeof item == "object") item = item[0].title %>


						  			<option value="<%=key%>"><%=item%></option>
						  		<% }); %>
						  	</select> 

							<span class="inform">
								<span>Используйте Ctrl чтобы выбрать несколько значений</span>
							</span>
						</div>
				</div>
			</div>									
	</div>
</script>


<script id="template-parameters" type="text/template">
	<div class="fn-parameters">
		<div class="smallcont">
			<div class="labelcont">
				<label><span>Параметры:</span></label>
			</div>
			<div class="fieldscont group">
				<div class="fn-list-parameters"></div>
									
			</div>									
		</div><!--smallcont-->
		<div class="fn-rows-parameters"></div>
	</div>
</script>	

<script id="template-subject" type="text/template">
	<div class="smallcont" id="div_subject">
		<div class="labelcont">
				<label><span>Заголовок:</span></label>
		</div>
		<div class="fieldscont">
			<div class="inp-cont-long">
				<div class="inp-cont <% if (error) {%> error <%}%>">
					<span class="required-label">*</span>																		
					<input type="text" maxlength="75"  id="title_adv" name="title_adv" value="<%=value%>"/>	

					<% if (error) { %>
					<span class="inform">
						<span><?=$form_data->subject["subject_error"]?></span>
					</span>
					<% } %>
				</div>							
			</div>
		</div><!--fieldscont-->
	</div><!--smallcont-->		  
</script>

<script id="template-textadv" type="text/template">
	<div class="smallcont" id="div_text">
		<div class="labelcont">
			<label><span>Текст объявления:</span></label>
		</div>		
		<div class="fieldscont user-text mb15">
			<div class="inp-cont-long ">
				<div class="inp-cont">
					<% if (text_required) { %>
						<span class="required-label">*</span>
					<% } %>
					<div class="textarea user_text_adv_wrp">
					<textarea name="user_text_adv" id="user_text_adv" class="user_text_adv"><%=value%></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>  
</script>

<script id="template-category-description" type="text/template">
	<div class="smallcont" id="div_text">
		<div class="labelcont">
			<label><span>!</span></label>
		</div>		
		<div class="fieldscont user-text mb15">
			<div class="inp-cont">
				<div class="inp-cont">
					<%=text%>
				</div>
			</div>
		</div>
	</div>  
</script>

<script id="template-photo" type="text/template">
		<div class="img <%=((active)?'active':'')%>"><img src="<%=filepath%>"/></div> 
		<div class="href-bl"><span href="" class="remove fn-remove">Удалить</span></div>
		<input type="hidden" name="userfile[]" value="<%=filename%>"/>
</script>

<script id="template-contact" type="text/template">
	<div class="cont-left">
		<select class="sl-contact-type fn-contact-type" name="contact_<%=_id%>_type">
			<option value="1" data-comment="Введите номер" data-validation-type="phone" data-format="+7(999)999-99-99" <% if (type == "1") {%> selected <%}%> >Мобильный тел.</option>
			<option value="2" data-comment="Введите номер" data-validation-type="phone" data-format="+7(9999)99-99-99" <% if (type == "2") {%> selected <%}%> >Городской тел.</option>
			<option value="5" data-comment="Введите ваш почтовый ящик" data-validation-type="email" data-format="" <% if (type == "5") {%> selected <%}%> >Email</option>
		</select>
		<input class='inp_contact fn-contact-value' type="text" name="contact_<%=_id%>_value" value="<%=value%>">
		<span class="inform"><span class="fn-contact-inform">
			<% if (status == "verified") { %>
				Контакт подтвержден
			<% } else if (status == "noverified"){ %>
				Контакт не подтвержден
			<% } %>
		</span></span>
	</div>
	<div class="cont-right">
		<span title="Верифицировать" class="button apply fn-contact-verify-button">Подтвердить контакт</span>
		<span class="cansel like-link fn-contact-delete-button" title="Удалить">Удалить</span>
	</div><!--contact-right-->			
</script>

<script type="text/template" id="template-verify-contact-window">
	<div class="popup-cont confirm-contact-win">
		<span class="close fn-verify-contact-win-close"></span>
		<p class="title pb10">Проверка контакта <%=value%></p>										
		<input class="inp-confirm-code fn-input-code" type="text" placeholder="Введите код" />
		<p class="msg pt5 pb25 fn-error-block"></p>
		<? /*<div class='fn-btn-re-send'><span style="font-size:12px;">Отправить еще раз</span></div> */?>
		<div class="ta-r"><div class="button blue  fn-verify-contact-win-submit">Готово</div></div>
	</div>
</script>

<?=Assets::factory('main')->js("addapp.js")?>