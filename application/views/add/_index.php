<div class="form-cont">
	<form method="POST"  id="element_list">	
		<?= Form::hidden('csrf', $token) ?>		
		<input type="hidden" name="object_id" id="object_id" value="<?= $params->object_id ?>">
		<input type="hidden" name="session_id" value="<?= session_id() ?>">

		<? if (property_exists($form_data, 'login')): ?>
			<div class="row mb10">
				<div class="col-md-3 col-xs-4 labelcont">
					<label>Логин:</label>
				</div>
				<div class="col-md-9 col-xs-8">
					<div class="row">
						<div class="col-md-6">
							<div class="inp-cont <? if ($form_data->login["login_error"]) echo "error"; ?>">
								<span class="required-star">*</span>																		
								<input class="w100p" type="text" name="login" value="<? if ($params->login) echo $params->login; ?>"/>	
								<? if ($form_data->login["login_error"]): ?>
									<span class="inform fn-error">
										<?= $form_data->login["login_error"] ?>
									</span>
								<? endif; ?>
							</div>
						</div>
					</div>				
				</div>
			</div>	

			<div class="row mb10">
				<div class="col-md-3 col-xs-4 labelcont">
					<label>Пароль:</label>
				</div>
				<div class="col-md-9 col-xs-8">
					<div class="row">
						<div class="col-md-6">
							<div class="inp-cont <? if ($form_data->login["pass_error"]) echo "error"; ?>">
								<span class="required-star">*</span>																		
								<input class="w100p" type="password" name="pass" value="<? if ($params->pass) echo $params->pass; ?>"/>	
								<? if ($form_data->login["pass_error"]): ?>
									<span class="inform fn-error">
										<?= $form_data->login["pass_error"] ?>
									</span>
								<? endif; ?>
							</div>
							<p><a target="_blank" href="http://<?= Kohana::$config->load('common.main_domain') ?>/user/registration">Зарегистрироваться</a></p>
							<p><a target="_blank" href="/user/forgot_password">Напомнить/сменить пароль</a></p>							
						</div>
					</div>				
				</div>
			</div>		
		<? endif; ?>

		<? if (property_exists($form_data, 'linked_company')) : ?>
			<div class="row mb10">
				<div class="col-md-3 col-xs-4 labelcont">
					<label>От компании:</label>
				</div>
				<div class="col-md-9 col-xs-8">
					<?= $company = $form_data->linked_company["company"]; ?>
					<input type="checkbox" name="link_to_company" <? if ($form_data->linked_company["value"] == "on") echo "checked"; ?>/>  <?= $company->org_name ?>
					<?= $company->org_name ?> (<?= $company->email ?>)
					<? if ($company->filename): ?>
						<div class="p10">
							<? $logo = Imageci::getSitePaths($company->filename); ?>
							<img src="<?= $logo["120x90"] ?>">
						</div>
					<? endif; ?>
					<span class="inform">
						Вы можете отвязать свою учетную запись от этой компании <a href="/user/userinfo">здесь</a>
					</span>			
				</div>
			</div>		
		<? endif; ?>

		<? if (property_exists($form_data, 'city')): ?>
			<div id="div_city">
				<div class="row mb10">
					<div class="col-md-3 col-xs-4 labelcont">
						<label>Город публикации:</label>
					</div>
					<div class="col-md-9 col-xs-8">
						<div class="row">
							<div class="col-md-6">
								<div class="inp-cont <? if ($form_data->city["city_error"]) echo "error"; ?>">
									<span class="required-star">*</span>
									<?=
									View::factory('add/block/city', array("data" => new Obj($form_data->city),
										"_class" => "",
										"name" => "city_id",
										"id" => "",
										"attributes" => ""
									));
									?>
									<? if ($form_data->city AND $form_data->city["city_error"]): ?>
										<span class="inform fn-error">
											<?= $form_data->city["city_error"] ?>
										</span>
									<? endif; ?>							
								</div>
							</div>					
						</div>				
					</div>
				</div>		

				<div class="row mb10">
					<div class="col-md-3 col-xs-4 labelcont">
						<label></label>
					</div>
					<div class="col-md-9 col-xs-8">
						<input type="checkbox" id="real_city_exists" name="real_city_exists" <? if ($form_data->city["real_city_exists"]) echo 'checked'; ?>>
						<label for="real_city_exists">Товар/услуга/продукт/вакансия находится в другом городе</label>							
					</div>
				</div>		

				<div class="row mb10 real_city_exists" <? if (!$form_data->city["real_city_exists"]) echo 'style="display:none;"'; ?>>
					<div class="col-md-3 col-xs-4 labelcont">
						<label></label>
					</div>
					<div class="col-md-9 col-xs-8">
						<div class="row">
							<div class="col-md-6">
								<div class="inp-cont ">
									<span class="required-star">*</span>																		
									<input class="real_city w100p" type="text" name="real_city" value="<?= $form_data->city["real_city"] ?>"  id="real_city"  autocomplete="off"/>
									<span class="inform fn-error">
										Укажите город расположения объекта/товара/услуги/вакансии
									</span>					
								</div>
							</div>
						</div>				
					</div>
				</div>		
			</div>

		<? endif; ?>

		<div id="div_category">
			<? if (property_exists($form_data, 'category')): ?>
				<div class="row mb10">
					<div class="col-md-3 col-xs-4 labelcont">
						<label>Раздел:</label>
					</div>
					<div class="col-md-9 col-xs-8">
						<div class="row">
							<div class="col-md-6">
								<div class="inp-cont <? if ($form_data->category["category_error"]) echo "error"; ?>">
									<span class="required-star">*</span>
									<?=
									View::factory('add/block/category', array("data" => new Obj($form_data->category),
										"_class" => "",
										"name" => "rubricid",
										"id" => "fn-category",
										"attributes" => ""
									));
									?>
								</div>
								<? if ($form_data->category AND $form_data->category["category_error"]): ?>
									<span class="inform fn-error">
										<?= $form_data->category["category_error"] ?>
									</span>
								<? endif; ?>						
							</div>
						</div>				
					</div>
				</div>

				<div class="row mb10">
					<div class="col-md-3 col-xs-4 labelcont">
						<label></label>
					</div>
					<div class="col-md-9 col-xs-8">
						<div id="div_category_description"></div>
					</div>
				</div>			

			<? endif; ?>
		</div>

		<? if (property_exists($form_data, 'advert_type')): ?>
			<div id="div_advert_type" class="row mb10">
				<div class="col-md-3 col-xs-4 labelcont">
					<label>Тип объявления:</label>
				</div>
				<div class="col-md-9 col-xs-8">
					<div class="row">
						<div class="col-md-6">
							<div class="inp-cont ">
								<span class="required-star">*</span>																		
								<?= Form::select("obj_type", $form_data->advert_type['type_list'], $form_data->advert_type['value'], array('class' => 'w100p')); ?>					
							</div>
						</div>
					</div>
				</div>
			</div>			
		<? endif; ?>

		<? if (property_exists($form_data, 'company_info')): ?>
			<div id="div_company_info">
				<? foreach ($form_data->company_info['info'] as $name => $field): ?>
					<div class="row mb10" >
						<div class="col-md-3 col-xs-4 labelcont">
							<label><?= $name ?></label>
						</div>
						<div class="col-md-9 col-xs-8">
							<div class="row">
								<div class="col-md-6">
									<div class="inp-cont ">
										<?= $field ?>					
									</div>
								</div>
							</div>				
						</div>
					</div>
				<? endforeach; ?>
			</div>		
		<? endif; ?>

		<? if (property_exists($form_data, 'additional')): ?>
			<div id="div_additional">
				<?=
				View::factory('add/additional', array(
					"errors" => $form_data->additional["errors"],
					"settings" => $form_data->additional["settings"],
					"vakancy_org_type" => $form_data->additional["vakancy_org_type"],
					"values" => $form_data->additional["values"]
				));
				?>
			</div>
		<? endif; ?>

		<div id="div_params">
			<? if (property_exists($form_data, 'params')): ?>
				<div class="fn-parameters">
					<? if (count($form_data->params['elements']) > 0): ?>
						<div class="row">
							<div class="col-md-3 col-xs-4 labelcont">
								<label>Параметры:</label>
							</div>
							<div class="col-md-9 col-xs-8">
								<div class="row bg-color-whitesmoke pt15 mb20">
									
										<?=
										View::factory('add/block/params', array("data" => new Obj($form_data->params),
											"_class" => "",
											"name" => "",
											"id" => "",
											"attributes" => ""
										));
										?>
													
								</div>				
							</div>
						</div>					
					<? endif; ?>

					<?=
					View::factory('add/block/row_params', array("data" => new Obj($form_data->params),
						"_class" => "",
						"name" => "",
						"id" => "",
						"attributes" => ""
					));
					?>	
				</div>
			<? endif; ?>
		</div>


		<div class="row mb10 hidden" id="div_map">
			<div class="col-md-3 col-xs-4 labelcont">
				<label><span class="label-geo">Укажите местоположение объекта на карте</span></label>
			</div>
			<div class="col-md-9 col-xs-8">
				<div class="inp-cont ">
					<div id="map_block_div" class="map_block_div add_form_info mb20">
						<div class="map" id="map_block" style="height:250px;width:100%;">		
							<input type="hidden" id="object_coordinates" name="object_coordinates" value="<? if ($form_data->object_coordinates) echo $form_data->object_coordinates; ?>"/>
						</div>				
					</div>					
				</div>
			</div>
		</div>		

		<div id="div_subject">
			<? if (property_exists($form_data, 'subject')): ?>
				<div class="row mb10" id="div_subject">
					<div class="col-md-3 col-xs-4 labelcont">
						<label>Заголовок:</label>
					</div>
					<div class="col-md-9 col-xs-8">
						<div class="inp-cont <? if ($form_data->subject["subject_error"]) echo "error"; ?>">
							<span class="required-star">*</span>
							<?=
							View::factory('add/block/subject', array("data" => new Obj($form_data->subject),
								"_class" => "",
								"name" => "title_adv",
								"id" => "title_adv",
								"attributes" => ""
							));
							?>							
							<? if ($form_data->subject AND $form_data->subject["subject_error"]): ?>
								<span class="inform">
									<?= $form_data->subject["subject_error"] ?>
								</span>
							<? endif; ?>					
						</div>
					</div>
				</div>			
			<? endif; ?>
		</div>

		<style type="text/css">
			.nicEdit-main ol{ list-style:inside; list-style-type: decimal; }
			.nicEdit-main ul{ list-style: inside; list-style-type: circle; }
		</style>
		<div id="div_textadv">
			<? if (property_exists($form_data, 'text')): ?>
				<div class="row mb10">
					<div class="col-md-3 col-xs-4 labelcont">
						<label>Текст объявления:</label>
					</div>
					<div class="col-md-9 col-xs-8">
						<div class="inp-cont <? if ($form_data->text["text_error"]) echo "error"; ?>">
							<? if ($form_data->text_required): ?>
								<span class="required-star">*</span>
							<? endif; ?>																		
							<div class="textarea user_text_adv_wrp">
								<?=
								View::factory('add/block/text', array("data" => new Obj($form_data->text),
									"_class" => "user_text_adv",
									"name" => "user_text_adv",
									"id" => "user_text_adv",
									"attributes" => ""
								));
								?>	
							</div>
							<? if ($form_data->text AND $form_data->text["text_error"]): ?>
								<span class="inform">
									<?= $form_data->text["text_error"] ?>
								</span>
							<? endif; ?>					
						</div>
					</div>
				</div>			
			<? endif; ?>
		</div>

		<div id="div_photo">
			<? if (property_exists($form_data, 'photo')): ?>
				<div class="row mb10">
					<div class="col-md-3 col-xs-4 labelcont">
						<label>Фото:</label>
					</div>
					<div class="col-md-9 col-xs-8">
						<div class="inp-cont">
							<input id="fileupload" type="file" name="userfile1" data-url="/add/object_upload_file" multiple>
							<div id="add-block" class="add-block fn-photo-list mt20" data-max="8">
								<?=
								View::factory('add/block/photo', array("data" => new Obj($form_data->photo),
									"_class" => "",
									"name" => "",
									"id" => "",
									"attributes" => ""
								));
								?>
							</div>
							<input type="hidden" id="active_userfile" name="active_userfile" value="<?= $params->active_userfile ?>" />
							<span class="inform">
								<span class="fn-photo-hint"></span>
							</span>
							<div class="mb10 red" id="error_userfile1"></div>
						</div>
					</div>
				</div>			
			<? endif; ?>
		</div>

		<div id="div_video">
			<? if (property_exists($form_data, 'video')): ?>
				<div class="row mb10">
					<div class="col-md-3 col-xs-4 labelcont">
						<label>Видео:</label>
					</div>
					<div class="col-md-9 col-xs-8">
						<div class="row">
							<div class="col-md-6">
								<div class="inp-cont <? if ($form_data->video["video_error"]) echo "error"; ?>">
									<?=
									View::factory('add/block/video', array(
										"data" => new Obj($form_data->video)
									));
									?>							
									<? if ($form_data->video AND $form_data->video["video_error"]): ?>
										<span class="inform">
											<?= $form_data->video["video_error"] ?>
										</span>
									<? endif; ?>
									<span class="inform">
										Короткая ссылка с youtube (Например: http://youtu.be/aQIFUD3M3Hk )
									</span>
									<? if ($form_data->video['embed']): ?>
										<div class="pb20">
											<?= $form_data->video['embed'] ?>
										</div>
									<? endif; ?>							
								</div>
							</div>
						</div>				
					</div>
				</div>			
			<? endif; ?>
		</div>

		<div id="div_price" style="display:none;">
			<? if (property_exists($form_data, 'price')): ?>
				<div class="row mb10">
					<div class="col-md-3 col-xs-4 labelcont">
						<label>Прайс-лист:</label>
					</div>
					<div class="col-md-9 col-xs-8">
						<div class="row">
							<div class="col-md-6">
								<div class="inp-cont ">
									<?=
									View::factory('add/block/price', array(
										"data" => new Obj($form_data->price)
									));
									?>							
									<span class="inform">
										Прайс лист необходимо предварительно загрузить и дождаться одобрения модератора<a href="/user/priceload">(как?)</a>
									</span>							
								</div>
							</div>
						</div>				
					</div>
				</div>			
			<? endif; ?>
		</div>

		<? if (property_exists($form_data, 'contacts')): ?>

			<div class="row mb20" id="div_contacts">
				<div class="col-md-3 labelcont">
					<label>Контакты:</label>
				</div>
				<div class="col-md-9">
					<div id="contacts2" class="contacts-cont fn-contacts-container">
						<?=
						View::factory('add/block/contacts', array("data" => new Obj($form_data->contacts),
							"_class" => "",
							"name" => "",
							"id" => "",
							"attributes" => ""
						));
						?>					
					</div>				
				</div>
			</div>	

			<div class="row mb20">
				<div class="col-md-3 labelcont">
					<label></label>
				</div>
				<div class="col-md-9">
					<span title="Добавить контакт" class="add-contact span-link fn-add-contact-button-text"><i class="ico plus-ico17 mr7"></i>Добавить еще телефон или email</span>				
				</div>
			</div>	

			<div class="row mb10">
				<div class="col-md-3 col-xs-4 labelcont">
					<label>Контактное лицо:</label>
				</div>
				<div class="col-md-9 col-xs-8">
					<div class="row">
						<div class="col-md-6">
							<div id="contacts">	                  			
								<div class="inp-cont <? if ($form_data->contacts["contact_error"]) echo "error"; ?>">
									<span class="required-star">*</span>						
									<input class="w100p" type="text" name="contact" value="<?= $form_data->contacts["contact_person"] ?>"/>						
									<? if ($form_data->contacts AND $form_data->contacts["contact_error"]): ?>
										<span class="inform">
											<?= $form_data->contacts["contact_error"] ?>
										</span>
									<? endif; ?>
								</div>
							</div>
						</div>
					</div>				
				</div>
			</div>		
		<? endif; ?>


		<?
		$prolongation_access = FALSE;
		?>  

		<? if ($form_data->_edit): ?>
			<div class="row mb10">
				<div class="col-md-3 col-xs-4 labelcont">
					<label>Состояние объявления:</label>
				</div>
				<div class="col-md-9 col-xs-8">
					<div class="row">
						<div class="col-md-6">
							<div class="inp-cont ">
								<div data-placeholder="Выберите срок жизни объявления..." class="values">
									<?
									$periods = array("2w" => "14 дней",
										"1m" => "30 дней",
										"2m" => "60 дней",
										"3m" => "90 дней");
									$default_period = "2m";
									if ($params->lifetime)
										$default_period = $params->lifetime;
									?>

									<? if ($object->is_published): ?>
										<p class="mb10">
											до <?= $object->date_expiration ?>
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
					</div>				
				</div>
			</div>		
		<? endif; ?>	

		<? if (!$form_data->_edit OR $prolongation_access): ?>
			<div class="row mb10">
				<div class="col-md-3 col-xs-4 labelcont">
					<label>Срок объявления:</label>
				</div>
				<div class="col-md-9 col-xs-8">
					<div class="row">
						<div class="col-md-6">
							<div class="inp-cont ">
								<div data-placeholder="Выберите срок жизни объявления..." class="values">
									<?
									$periods = array("2w" => "на 14 дней",
										"1m" => "на 30 дней",
										"2m" => "на 60 дней",
										"3m" => "на 90 дней");
									$default_period = "2m";
									if ($params->lifetime)
										$default_period = $params->lifetime;
									?>

									<select class="w100p" id="lifetime" name="lifetime">
										<? foreach ($periods as $key => $value): ?>
											<option value="<?= $key ?>" <? if ($default_period == $key) echo "selected"; ?>><?= $value ?></option>
										<? endforeach; ?>
									</select>
								</div>					
							</div>
						</div>
					</div>				
				</div>
			</div>		
		<? endif; ?>												


		<div class="row mb10">
			<div class="col-md-3 col-xs-4 labelcont">
				<label></label>
			</div>
			<div class="col-md-9 col-xs-8">
				<div class="inp-cont ">
					<label>
						<input type="checkbox" name="block_comments" id="block_comments" value="1" <? if ($params->block_comments) echo "checked"; ?>></input>
						<span for="block_comments" class="">Отключить возможность задавать вопросы на странице объявления</span>
					</label>				
				</div>
			</div>
		</div>				

		<? if (count($errors) > 0) : ?>
			<div class="row mb20">
				<div class="col-md-3 labelcont">				
				</div>
				<div class="col-md-9 form-errors-cont">
					<p>Исправьте ошибки в форме, чтобы продолжить:</p>
					<? foreach (array_values($errors) as $error): ?>
						<p class="error-msg"><?= $error ?></p>
					<? endforeach; ?>				
				</div>
			</div>		
		<? endif; ?>

		<div id="div_submit">
			<div class="row mb20">
				<div class="col-md-3 labelcont">
					<label></label>
				</div>
				<div class="col-md-9 ta-r">
					<? if ($form_data->_edit): ?>	
						<? if ($object->is_bad <> 2): ?>	
							<span id="submit_button" class="button button-style1 bg-color-blue btn-next"><i class="ico ico15 white-right-arrow-ico mr3"></i>Далее</span>							
						<? endif; ?>
					<? else: ?>
						<span id="submit_button" class="button button-style1 bg-color-blue btn-next"><i class="ico ico15 white-right-arrow-ico mr3"></i>Далее</span>							
					<? endif; ?>				
				</div>
			</div>			
		</div>
	</form>
</div>