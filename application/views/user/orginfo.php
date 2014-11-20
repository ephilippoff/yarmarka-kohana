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

					<form method="POST" id="orginfo" enctype="multipart/form-data">
						<div class="fl100  pt16 pb15">
							<div class="smallcont">
								<div class="labelcont">
									<label><span>1</span></label>
								</div>
								<div class="fieldscont">										
									<div class="">
										<div class="inp-cont">
											Вы выбрали тип учетной записи "Компания". Для завершения регистрации с этим типом, необходимо предоставить ИНН, а также заполнить другие обязательные поля отмеченные зведочкой <span style="color:red;">*</span>.
								  		</div>
									</div>
								</div>									
							</div>
							<div class="smallcont">
								<div class="labelcont">
									<label><span>2</span></label>
								</div>
								<div class="fieldscont">										
									<div class="">
										<div class="inp-cont">
											Если вы представитель компании, уже имеющей учетную запись на нашем сайте, и хотите подавать объявления от ее лица, совершите <a href="/user/office">привязку</a> учетной записи к этой компании. (Компания, в своей учетной записи должна подтвердить привязку)
								  		</div>
									</div>
								</div>									
							</div>
							<div class="smallcont">
								<div class="labelcont">
									<label><span>3</span></label>
								</div>
								<div class="fieldscont">										
									<div class="">
										<div class="inp-cont">
											Вы можете не заполнять форму и сбросить тип учетной записи на "Частное лицо" если перейдете по <a href="/user/reset_orgtype">ссылке</a>. При этом будут применены ограничения на количество объявлений в некоторые рубрики (Продажа квартир и комнат, Аренда квартир и комнат, Вакансии)
								  		</div>
									</div>
								</div>									
							</div>
							<div class="smallcont">
								<div class="labelcont">
									<label><span>4</span></label>
								</div>
								<div class="fieldscont">										
									<div class="">
										<div class="inp-cont">
											Не забудьте нажать на кнопку "Сохранить", после заполнения формы!
								  		</div>
									</div>
								</div>									
							</div>
						</div>
						<? if ($success): ?>
							<div class="fl100 pb15">
									<div class="smallcont">
									<div class="labelcont">
										<label><span></span></label>
									</div>
									<div class="fieldscont">										
										<div class="">
											<div class="inp-cont">
												<span style="color:green;">Успешно сохранено!</span>
									  		</div>
										</div>
									</div>									
								</div>
							</div>
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
												<?=$inn["inn"]?>
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
								<? if (!is_null($inn["inn_moderate"])): ?>
									<div class="smallcont">
										<div class="labelcont">
											<label><span>Состояние модерации</span></label>
										</div>
										<div class="fieldscont">										
											<div class="">
												<div class="inp-cont">
													<?=$org_moderate_states[$inn["inn_moderate"]]?>
										  		</div>
											</div>
										</div>									
									</div>
								<? endif; ?>
								<? if ($inn["inn_moderate_reason"]): ?>
									<div class="smallcont">
										<div class="labelcont">
											<label><span>!</span></label>
										</div>
										<div class="fieldscont">										
											<div class="">
												<div class="inp-cont">
													<?=$inn["inn_moderate_reason"]?>
										  		</div>
											</div>
										</div>									
									</div>
								<? endif; ?>
							</div>
						<? endif; ?>
						<div class="fl100 pb15">
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
										    				$("#<?=$field['name']?>").change(function(e){
										    					$(".<?=$field['name']?>_hidden").remove();
										    					$( "<input class='<?=$field['name']?>_hidden' type='hidden' name='<?=$field['name']?>' value='1'/>" ).insertAfter( e.target );
										    				});
										    			});
									    			</script>
									    			<?=$field["html"]?>
									    			
									    			<? if ($field["path"] AND $field["path"] <> "1"):?>
									    				<div  style="padding:10px;">
									    					<img src="<?=$field["path"]?>" style="padding:10px;max-width:300px;"></br>
									    					<? if (!$field["required"]):?>
									    						<input type="checkbox" name="delete_<?=$field['name']?>" <? if ($data->{"delete_".$field['name']} == "on") echo "checked"; ?> />Удалить
									    					<? endif; ?>
									    				</div>
									    			<? elseif ($field["path"] == "1"): ?>
									    				<div  style="padding:10px;">
									    					<span style="color:green;">Файл загружен</span>
									    				</div>
									    			<? endif;?>
									    		<? else: ?>
									    			<?=$field["html"]?>
									    		<? endif; ?>

									    		<? if ($field["description"]): ?>
									    			<span class="inform">
														<span><?=$field["description"]?></span>
													</span>
												<? endif;?>

									    		<? if ($errors->{$field["name"]}): ?>
													<span class="inform fn-error">
														<span><?=$errors->{$field["name"]}?></span>
													</span>
												<? endif; ?>

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