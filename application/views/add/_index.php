<form method="POST"  id="element_list">	
	<?=Form::hidden('csrf', $token)?>		
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

	<? if ( property_exists($form_data, 'org_info') ): ?>
	<div class="fl100  pt16 pb15"  id="div_org_info">
			<div class="smallcont">
				<div class="labelcont">
					<label><span>Компания:</span></label>
				</div>	
				<div class="fieldscont">
					<div class="inp-cont-long ">
						<div class="inp-cont">
							<div class="pt4">
								<?=$form_data->org_info["title"]?>
								<? if ($form_data->org_info["logo"]): ?>
									<div class="p10">
										<? $logo = Imageci::getSitePaths($form_data->org_info["logo"]);?>
										<img src="<?=$logo["120x90"]?>">
									</div>
								<? endif; ?>
								<div style="height:30px;overflow:hidden;">
									<?=$form_data->org_info["about"]?>...
								</div>
								<span class="inform">
									<span>Изменить информацию о компании можно <a href="/user/orginfo">здесь</a></span>
								</span>
							</div>
						</div>
					</div><!--inp-cont-short-->
					
				</div><!--fieldscont-->
			</div> <!-- smallcont -->
	</div>
	<? endif; ?>

	<? if ( property_exists($form_data, 'linked_company') ): ?>
	<div class="fl100  pt16 pb15"  id="div_linked_company">
			<div class="smallcont">
				<div class="labelcont">
					<label><span>От компании:</span></label>
				</div>	
				<div class="fieldscont">
					<div class="inp-cont-long ">
						<div class="inp-cont">
							<div class="pt4">
								<? 
									$company = $form_data->linked_company["company"];
								?>
								<input type="checkbox" name="link_to_company" <? if ($form_data->linked_company["value"] == "on") echo "checked"; ?>/>  <?=$company->org_name?>
								<?=$company->org_name?> (<?=$company->email?>)
								<? if ($company->filename): ?>
									<div class="p10">
										<? $logo = Imageci::getSitePaths($company->filename);?>
										<img src="<?=$logo["120x90"]?>">
									</div>
								<? endif; ?>
								<span class="inform">
									<span>Вы можете отвязать свою учетную запись от этой компании <a href="/user/userinfo">здесь</a></span>
								</span>
							</div>
						</div>
					</div><!--inp-cont-short-->
					
				</div><!--fieldscont-->
			</div> <!-- smallcont -->
	</div>
	<? endif; ?>

	<? if ( property_exists($form_data, 'city') ): ?>
		<div class="fl100  pt16 pb10"  id="div_city">
				<div class="smallcont">
					<div class="labelcont">
						<label><span>Город публикации:</span></label>
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
			<div class="smallcont">
				<div class="labelcont">
					<label><span></span></label>
				</div>	
				<div class="fieldscont">
					<div class="inp-cont-long ">								
						<input type="checkbox" id="real_city_exists" name="real_city_exists" <? if ($form_data->city["real_city_exists"]) echo 'checked';?>>
						<label for="real_city_exists" style="cursor:pointer">Товар/услуга/продукт/вакансия находится в другом городе</label>
					</div><!--inp-cont-short-->							
				</div><!--fieldscont-->
			</div> <!-- smallcont -->

			<div class="smallcont real_city_exists" <? if (!$form_data->city["real_city_exists"]) echo 'style="display:none;"';?>>
				<div class="labelcont">
					<label><span></span></label>
				</div>	
				<div class="fieldscont">
					<div class="inp-cont-short ">
						<div class="inp-cont ">
							<span class="required-label">*</span>
							<input class="real_city" type="text" name="real_city" value="<?=$form_data->city["real_city"]?>"  id="real_city"  autocomplete="off"/>
								<span class="inform fn-error">
									<span>Укажите город расположения объекта/товара/услуги/вакансии</span>
								</span>
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
					
					<div class="inp-cont-short notice"><div class="cont">Внимание! В разделе "Вакансии" поднятие объявления доступно 1 раз в сутки. В остальных разделах поднятие объявления доступно 1 раз в 3 суток. УСЛУГА БЕСПЛАТНАЯ!</div></div>

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

	<? if ( property_exists($form_data, 'company_info') ): ?>

		<div class="fl100" id="div_company_info">
			<? foreach ($form_data->company_info['info'] as $name => $field): ?>
			
				<div class="smallcont">
					<div class="labelcont">
						<label><span><?=$name?></span></label>
					</div>
					<div class="fieldscont">				 						
						<div class="inp-cont-short">
							<div class="inp-cont">
								<?=$field?>
							</div> <!--inp-cont -->
						</div> <!-- inp-cont-short -->
					</div> <!-- fieldscont -->		
				</div>	 <!-- smallcont --> 
			<? endforeach; ?>
		</div>  <!-- fl100 -->

	<? endif; ?>

	<? if ( property_exists($form_data, 'additional') ): ?>
		<div id="div_additional">
				<?=View::factory('add/additional', array(
									"errors" 		   => $form_data->additional["errors"],
									"settings" 		   => $form_data->additional["settings"],
									"vakancy_org_type" => $form_data->additional["vakancy_org_type"], 
									"values" 		   => $form_data->additional["values"]
								));?>
		</div>
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

	<style type="text/css">
	   .nicEdit-main ol{ list-style:inside; list-style-type: decimal; }
		.nicEdit-main ul{ list-style: inside; list-style-type: circle; }
	</style>
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
						<input id="fileupload" type="file" name="userfile1" data-url="/add/object_upload_file" multiple>
						<div id="add-block" class="add-block fn-photo-list mt20" data-max="8">
							<?=View::factory('add/block/photo',
								array( "data" 		=> new Obj($form_data->photo),
									   "_class" 	=> "",
									   "name" 		=> "",
									   "id" 		=> "",
									   "attributes" => ""
								));?>
							
							<!-- <div id="addbtn" class="addbtn"><span class="add"><img src="/images/plus_icon.png"><span id="" class="span">Загрузите фото</span></span></div> -->
						</div>
						<input type="hidden" id="active_userfile" name="active_userfile" value="<?=$params->active_userfile?>" />
						<span class="inform">
							<span class="fn-photo-hint"></span>
						</span>
						<? /*<input class="mb10" type="file" value="" id="btn-photo-load" name="photo" class="btn-photo-load">*/ ?>
						<div class="mb10 red" id="error_userfile1"></div>
					</div>
				</div>
			</div>
		</div>
	<? endif; ?>
	</div>

	<div id="div_video">
		<? if ( property_exists($form_data, 'video') ): ?>
			<div class="smallcont" id="div_video">
				<div class="labelcont">
						<label><span>Видео:</span></label>
				</div>
				<div class="fieldscont">
					<div class="inp-cont-short">
						<div class="inp-cont <?if ($form_data->video["video_error"]) echo "error";?>">																		
							<?=View::factory('add/block/video',
										array( 
											"data" 	=> new Obj($form_data->video)												  
										));?>							
							<? if ($form_data->video AND $form_data->video["video_error"]): ?>
								<span class="inform">
									<span><?=$form_data->video["video_error"]?></span>
								</span>
							<? endif; ?>
							<span class="inform">
								<span>Короткая ссылка с youtube (Например: http://youtu.be/aQIFUD3M3Hk )</span>
							</span>
							<?if ($form_data->video['embed']):?>
							<div style="padding-bottom:20px;">
								<?=$form_data->video['embed']?>
							</div>
							<?endif;?>
						</div>							
					</div>
				</div><!--fieldscont-->
			</div><!--smallcont-->	
		<? endif; ?>
	</div>

	<div id="div_price" style="display:none;">
		<? if ( property_exists($form_data, 'price') ): ?>
			<div class="smallcont" id="div_video">
				<div class="labelcont">
						<label><span>Прайс-лист:</span></label>
				</div>
				<div class="fieldscont">
					<div class="inp-cont-short">
						<div class="inp-cont">																		
							<?=View::factory('add/block/price',
										array( 
											"data" 	=> new Obj($form_data->price)												  
										));?>							
							<span class="inform">
								<span>Прайс лист необходимо предварительно загрузить и дождаться одобрения модератора<a href="/user/priceload">(как?)</a></span>
							</span>
						</div>							
					</div>
				</div><!--fieldscont-->
			</div><!--smallcont-->	
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
			<?
				$prolongation_access = FALSE;
			?>  

			<? if ($form_data->_edit):?>
				<div class="smallcont">
					<div class="labelcont">
						<label><span>Состояние объявления:</span></label>
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
									
										<? if ($object->is_published): ?>
											<p class="mb10">
												до <?=$object->date_expiration?>
											</p>
											<p class="mb10">
													<span style="color:green;">Опубликовано, доступно в поиске</span>														
											</p>
										<? else: ?>
											<p class="mb10">
												<? if ($object->in_archive): ?>
													В архиве
												<? endif; ?>
											</p>
											<p class="mb10">
													<span style="color:red;">Объявление не публикуется!</span>
											</p>
											<p class="mb10">
												<? if ($object->is_bad == 1): ?>
													Заблокировано до исправления. </br>
													После правки, объявление снова будет проверено модератором
												<? elseif ($object->is_bad == 2): ?>
													Заблокировано окончательно.
												<? elseif ($object->is_bad == 0): ?>															
													<input id="publish_and_prolonge" type="checkbox" name="publish_and_prolonge" value="1"  <? if ($params->publish_and_prolonge) echo "checked"; ?>/>
													<label for="publish_and_prolonge">Опубликовать объявление
														<? if ($object->in_archive): ?>
															(будет продлено на 90 дней и поднято в поиске)
														<? endif; ?>
													</label>
												<? endif; ?>
											</p>
										<? endif; ?>
									</select>
								</div>					

							</div>						
						</div>
					</div><!--fieldscont-->
				</div><!--smallcont-->	
			<? endif; ?>	

			<? if (!$form_data->_edit OR $prolongation_access):?>                  					
				<div class="smallcont">
					<div class="labelcont">
						<label><span>Срок объявления:</span></label>
					</div>	
					<div class="fieldscont">
						<div class="inp-cont-short">
							<div class="inp-cont">
								<div data-placeholder="Выберите срок жизни объявления..." class="values">
									<?
									 	$periods = array(	"2w" => "на 14 дней",
									 						"1m" => "на 30 дней",
									 						"2m" => "на 60 дней",
									 						"3m" => "на 90 дней");
									 	$default_period = "2m";
									 	if ($params->lifetime)
									 		$default_period = $params->lifetime;
									?>
									
									 <select id="lifetime" name="lifetime">
									 <? foreach ($periods as $key => $value): ?>
									 		<option value="<?=$key?>" <? if ($default_period == $key) echo "selected";?>><?=$value?></option>
									 <? endforeach; ?>
									</select>
								</div>	
							</div>						
						</div>
					</div><!--fieldscont-->
				</div><!--smallcont-->	
			<? endif; ?>												

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
					<? if ($form_data->_edit):?>	
						<? if ($object->is_bad <> 2): ?>	
							<div id="submit_button" class="button blue icon-arrow-r btn-next"><span>Далее</span></div>							
						<? endif; ?>
					<? else: ?>
						<div id="submit_button" class="button blue icon-arrow-r btn-next"><span>Далее</span></div>							
					<? endif; ?>
				</div><!--fieldscont-->
			</div><!--smallcont-->	
		</div>
	</div>

	

</form>