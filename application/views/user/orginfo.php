<div class="winner page-addobj">
	<section class="main-cont subscriptions cabinet">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет - Информация о компании</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">				
				<header><span class="title">
						Информация о компании
				</span>
				</header>
				<div class="p_cont">
							<script type="text/javascript">
								function reset_orgtype(){
									if (confirm("Вы уверены что хотите сменить тип учетной записи на 'Частное лицо?'")) {
									  window.location ="/user/reset_orgtype";
									}
								}
							</script>
					<form method="POST" id="orginfo" enctype="multipart/form-data">
						<? 
							$date = new DateTime(); 
							if (!$inn["inn"]): 
						?>
						<div class="fl100  pt16 pb15">
							<? if ($from OR strtotime($date->format('Y-m-d H:i:s')) >= strtotime($expired)): ?>
							<div class="smallcont">
								<div class="labelcont">
									<label><span></span></label>
								</div>
								<div class="fieldscont">										
									<div class="">
										<div class="inp-cont" style=" font-size:16px;">
											<span style="color:red;">Для продолжения работы с сайтом, пожалуйста, поделитесь с нами информацией о Вашей компании</span>.
								  		</div>
									</div>
								</div>									
							</div>
							<? 
								elseif (strtotime($date->format('Y-m-d H:i:s')) <= strtotime($expired)):							
							?>
								<div class="smallcont pb10">
									<div class="labelcont">
										<label><span></span></label>
									</div>
									<div class="fieldscont">										
										<div class="">
											<div class="inp-cont" style=" font-size:16px;">
												<span style="color:red;">Для продолжения работы с сайтом, пожалуйста, поделитесь с нами информацией о Вашей компании</span>.
												Если сейчас у Вас нет возможности заполнить форму, сделайте это в любое удобное время, но не позднее чем <?=$expired?>
									  		</div>
										</div>
									</div>									
								</div>
							<? endif; ?>

			
							<div class="smallcont pt10">
								<div class="labelcont">
									<label><span>1</span></label>
								</div>
								<div class="fieldscont">										
									<div class="">
										<div class="inp-cont">
											<div class="pt4">
												Вы выбрали тип учетной записи "Компания". Для завершения регистрации с этим типом, необходимо предоставить ИНН и загрузить его скан, а также заполнить другие обязательные поля отмеченные зведочкой <span style="color:red;">*</span>.
											</div>
								  		</div>
									</div>
								</div>									
							</div>
							<div class="smallcont pt10">
								<div class="labelcont">
									<label><span>2</span></label>
								</div>
								<div class="fieldscont">										
									<div class="">
										<div class="inp-cont">
											<div class="pt4">
												Если вы представитель компании, уже имеющей официальную учетную запись на нашем сайте, и хотите подавать объявления от ее лица, без ограничений, совершите привязку учетной записи к этой компании. (Т.е. компания, в своей учетной записи должна добавить Вашу учетную запись в разделе 'Сотрудники'). Свою учетную запись нужно сменить на тип "Частное лицо"
								  			</div>
								  		</div>
									</div>
								</div>									
							</div>
							<div class="smallcont pt10">
								<div class="labelcont">
									<label><span>3</span></label>
								</div>
								<div class="fieldscont">										
									<div class="">
										<div class="inp-cont">
											<div class="pt4">
												Вы можете не заполнять форму и сбросить тип учетной записи на "Частное лицо" если перейдете по <span class="link" style="cursor:pointer;" href="/user/reset_orgtype" onclick="reset_orgtype()">ссылке</span>. При этом будут применены ограничения на количество объявлений в некоторые рубрики (Продажа квартир и комнат, Аренда квартир и комнат, Вакансии)
											</div>
								  		</div>
									</div>
								</div>									
							</div>
							<div class="smallcont pt10">
								<div class="labelcont">
									<label><span>4</span></label>
								</div>
								<div class="fieldscont">										
									<div class="">
										<div class="inp-cont">
											<div class="pt4">
												Не забудьте нажать на кнопку "Сохранить", после заполнения формы!
											</div>
								  		</div>
									</div>
								</div>									
							</div>
						</div>
						<hr/>
						<? endif; ?>
						<? if ($success OR count((array) $errors)): ?>
							<div class="fl100 pb10 pt20">
									<div class="smallcont">
									<div class="labelcont">
										<label><span></span></label>
									</div>
									<div class="fieldscont">										
										<div class="">
											<div class="inp-cont">
												<div class="pt4">
													<? if (count((array) $errors)): ?>
														<span style="color:red;">
															<? foreach((array) $errors as $error):?>
																<?=$error?></br>
															<?	endforeach; ?>
														</span>
													<? else: ?>
														<span style="color:green;">Успешно сохранено!</span>
													<? endif; ?>
												</div>	
									  		</div>
										</div>
									</div>									
								</div>
							</div>
						<? endif; ?>
						<? if (!is_null($inn_moderate["inn_moderate"])): ?>
							<? 
								$style = ""; 
								if ($inn_moderate["inn_moderate"] == 0)
									$style = "background:#FFFACC"; 
								elseif ($inn_moderate["inn_moderate"] == 1)
									$style = "background:#8DE8AB"; 
								elseif ($inn_moderate["inn_moderate"] == 2)
									$style = "background:#FFA6A6";

							?>
							<div class="fl100 pb10 pt20" style="<?=$style?>">
								<div class="smallcont">
									<div class="labelcont">
										<label><span>Состояние модерации</span></label>
									</div>
									<div class="fieldscont">										
										<div class="">
											<div class="inp-cont">
												<div class="pt4">
													<?=$org_moderate_states[$inn_moderate["inn_moderate"]]?>
												</div>
									  		</div>
										</div>
									</div>									
								</div>
						
								<? if ($inn_moderate["inn_moderate_reason"]): ?>
									<div class="smallcont">
										<div class="labelcont">
											<label><span>Причина отклонения</span></label>
										</div>
										<div class="fieldscont">										
											<div class="">
												<div class="inp-cont">
													<div class="pt4">
														<?=$inn_moderate["inn_moderate_reason"]?>
													</div>
										  		</div>
											</div>
										</div>									
									</div>							
								<? endif; ?>
							</div>
							<hr/>
						<? endif; ?>
						<? if ($inn["inn"]): ?>
							<div class="fl100 pb15">
								<div class="smallcont">
									<div class="labelcont">
										<label><span>ИНН</span></label>
									</div>
									<div class="fieldscont">										
										<div class="">
											<div class="inp-cont">
												<div class="pt4">
													<?=$inn["inn"]?>
												</div>
									  		</div>
										</div>
									</div>									
								</div>
								<div class="smallcont">
									<div class="labelcont">
										<label><span>Юридическое название</span></label>
									</div>
									<div class="fieldscont">										
										<div class="">
											<div class="inp-cont">
												<div class="pt4">
													<?=$inn["org_full_name"]?>
												</div>
									  		</div>
										</div>
									</div>									
								</div>
								<div class="smallcont">
									<div class="labelcont">
										<label><span>Скан ИНН</span></label>
									</div>
									<div class="fieldscont">										
										<div class="">
											<div class="inp-cont">
												<img src="<?=$inn["inn_skan"]?>" style="padding:10px;max-width:300px;">
									  		</div>
										</div>
									</div>									
								</div>
								
							</div>
							<hr>
						<? endif; ?>

						
						<div class="fl100 pb15 pt20">
							<script>
								function setImage(fieldname, success, data, message)
							 	{
							 		if (success){
							 			$('#'+fieldname+"_status").text(message).addClass("success");
		            					$('#'+fieldname+"_image").html("<img src='"+data.filepaths["120x90"]+"'/>");
		            					$('#'+fieldname+"_input").html("<input name='"+fieldname+"' type='hidden' value='"+data.filename+"'/>");
		            					$('#'+fieldname+"_container").show();
							 		} else {
							 			$('#'+fieldname+"_status").text(message).addClass("error");
							 			$('#'+fieldname+"_image").html("");
		            					$('#'+fieldname+"_input > input").val("");
		            					$('#'+fieldname+"_container").hide();
							 		}
							 	}
							</script>
							<? foreach ($form as $field): ?>
								<div class="smallcont">
									<div class="labelcont">
										<label><span><?=$field["title"]?></span></label>
									</div>
									<div class="fieldscont">
										<?
											$lenth_unput = "inp-cont-short";
											if ($field["type"] == "long" OR $field["type"] == "text")
												$lenth_unput = "inp-cont-long";
										?>
										<div class="<?=$lenth_unput?>">
											<div class="inp-cont <? if ($errors->{$field["name"]}) echo "error";?>">
												<? if ($field["required"]):?>
													<span class="required-label">*</span>
									    		<? endif; ?>
									    		
									    		<? if ($field["type"] == "photo"): ?>
									    			<script>
													 	$(document).ready(function() {
														 	var self = this,
														 		fieldname = '<?=$field["name"]?>';

														    new AjaxUpload(fieldname, {
														            action: '/ajax_form/upload_photofield',
														            name: fieldname,
														            data : {fieldname :fieldname},
														            autoSubmit: true,
														            onSubmit: function(filename, response){

														            },
														            onComplete: function(filename, response){
														            	if (response) 
									            							data = $.parseJSON(response);
									            						else
									            							return;

									            						setImage(fieldname, (!data.error), data, (data.error)?data.error:"Файл загружен");
														            }
														       });
														});
													</script>
									    			<?=$field["html"]?></br>
									    			<div class="p10">								    			
									    				<span id="<?=$field["name"]?>_status"></span>
									    			</div>
								    				<div class="p10" style="<? if ( !isset($field['value']) ) { echo 'display:none;';}?>" id="<?=$field['name']?>_container">								    					
								    					<span id="<?=$field["name"]?>_image">
								    						<? if ( isset($field["value"]) ): ?>
								    							<?
								    								$filepaths = Imageci::getSitePaths($field["value"]);
								    								$path = $filepaths["120x90"];
								    							?>
								    							<img src='<?=$path?>'/>
								    						<? endif; ?>
								    					</span></br>
								    					<span id="<?=$field["name"]?>_input">
								    						<? if (isset($field["value"])): ?>
								    							<input name='<?=$field["name"]?>' type='hidden' value='<?=$field["value"]?>'/>
								    						<? endif; ?>
								    					</span></br>
								    					<span class="link" style="cursor:pointer;" onclick="setImage('<?=$field["name"]?>',false, null, ''); return false;">Удалить</span>
								    				</div>
									    		<? else: ?>
									    			<?=$field["html"]?>
									    		<? endif; ?> 		

									    		<? if ($errors->{$field["name"]}): ?>
													<span class="inform fn-error">
														<span><?=$errors->{$field["name"]}?></span>
													</span>
												<? endif; ?>

												<? if ($field["description"]): ?>
									    			<span class="inform">
														<span><?=$field["description"]?></span>
													</span>
												<? endif;?>

									  		</div>
										</div>
									</div>									
								</div>
							<? endforeach; ?>
						</div>
						<div class="fl100 form-next-cont">
							<div class="smallcont">
								<div class="labelcont"></div>	
								<div class="fieldscont ta-r mb15">						
									<div onclick="$('#orginfo').submit()" class="button blue icon-arrow-r btn-next"><span>Сохранить</span></div>							
								</div><!--fieldscont-->
							</div><!--smallcont-->	
						</div>
					</form>

				</div>
				<div class="clear"></div>
				<br />
				
			</section>
		</div>	   
		  
	</section>
</div><!--end content winner-->