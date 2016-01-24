<div class="winner page-addobj">
	<section class="main-cont subscriptions cabinet">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет - Данные пользователя</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">				
				<header><span class="title">
						Данные пользователя
				</span>
				</header>
				<div class="p_cont">

					<form method="POST" id="userinfo" enctype="multipart/form-data">
						<div class="fl100  pt16 pb15">
							<div class="smallcont">
								<div class="labelcont">
									<label><span>Идентификатор</span></label>
								</div>
								<div class="fieldscont">										
									<div class="">
										<div class="inp-cont">
											<div class="pt4">
												<?=$user->id?>
											</div>
								  		</div>
									</div>
								</div>									
							</div>
							<div class="smallcont">
								<div class="labelcont">
									<label><span>Email</span></label>
								</div>
								<div class="fieldscont">										
									<div class="">
										<div class="inp-cont">
											<div class="pt4">
												<?=$user->email?>
											</div>
								  		</div>
									</div>
								</div>									
							</div>
							<script type="text/javascript">
								function reset_orgtype(){
									if (confirm("Вы уверены что хотите сменить тип учетной записи на 'Частное лицо?'")) {
									  window.location ="/user/reset_orgtype";
									}
								}
							</script>
							<div class="smallcont">
								<div class="labelcont">
									<label><span>Тип учетной записи</span></label>
								</div>
								<div class="fieldscont">										
									<div class="">
										<div class="inp-cont">
											<div class="pt4">
												<?=$types[$user->org_type]?>
											</div>
											<? if ($user->org_type == 2): ?>
												<span class="inform">
													<span>Необходимо заполнить информацию о компании <a href="/user/orginfo">здесь</a></br>
															Вы можете сбросить тип учетной записи на "Частное лицо" если перейдете по <span class="link" style="cursor:pointer;" href="/user/reset_orgtype" onclick="reset_orgtype()">ссылке</span>. Учитывайте что объявления сверх лимитов будут сняты с публикации , все привязки из раздела "Сотрудники" отменятся.
													</span>
												</span>
											<? endif; ?>
											<? if ($user->org_type == 1): ?>
												<span class="inform">
													<span>Вы можете сменить тип учетной записи на "Компания" если перейдете по <a href="/user/reset_to_company">ссылке</a></br>
															Для подтверждения потребуется загрузить скан оригинала или копии ИНН.</br>
															<? if ($parent_user->loaded()): ?>
																При смене типа учетной записи, привязка к компании <?=$parent_user->org_name?> отменится.
															<? endif; ?>
													</span>
												</span>
											<? endif; ?>
								  		</div>
									</div>
								</div>									
							</div>
							

							<script type="text/javascript">
								function reset_parent_user(){
									if (confirm("Вы уверены что хотите отменить привязку к компании?")) {
									  window.location ="/user/reset_parent_user";
									}
								}

								function isValidEmail(email)
								{
								    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
								    return regex.test(email);
								}

								function request_to_company()
								{
									var email = $('#request_email').val();
									if (isValidEmail(email)){
										window.location ="/user/user_link_request?email="+email;
									}
								}
							</script>
							<div class="smallcont">
								<div class="labelcont">
									<label><span>Привязка к компании</span></label>
								</div>
								<div class="fieldscont">										
									<div class="">
										<div class="inp-cont">
											<? if (!$request_company->loaded()):?>
												<div class="pt4">
													
														<? if ($parent_user->loaded()): ?>
															<?=$parent_user->org_name?> (<?=$parent_user->email?>) | <span class="link" style="cursor:pointer;" href="/user/reset_parent_user" onclick="reset_parent_user()">отменить привязку</span>
															<? if ($parent_user->filename): ?>
																<div class="p10">
																	<? $logo = Imageci::getSitePaths($parent_user->filename);?>
																	<img src="<?=$logo["120x90"]?>">
																</div>
															<? endif; ?>
														<? else: ?>
															<div class="pb4">
																Нет привязки
															</div>

															
																<input type="text" style="width:350px" id="request_email" placeholder="Введите Email компании"/>
																<div onclick="request_to_company();" class="button blue"><span>Отправить запрос на привязку</span></div>
														<? endif; ?>
													
												</div>
												<? if (!$parent_user->loaded()): ?>
													<span class="inform">
														<span>Вы можете размещать объявления от лица какойлибо общей учетной записи компании, если компания подтвердила свой ИНН и добавила Вашу учетную запись в разделе "Сотрудники". Отправьте запрос на привязку.</span>
													</span>
												<? endif; ?>

											<? else: ?>
												<div class="pt4">
													Нет привязки. Отправлен запрос |  <a href="/user/user_link_request?method=delete_request">Отменить</a> </br>
													<?=$request_company->user->org_name?> (<?=$request_company->user->email?>)
													<? if ($request_company->user->filename): ?>
														<div class="p10">
															<? $logo = Imageci::getSitePaths($request_company->user->filename);?>
															<img src="<?=$logo["120x90"]?>">
														</div>
													<? endif; ?>
												</div>
											<? endif; ?>
								  		</div>
									</div>
								</div>									
							</div>
							
							<? if ($user->org_type == 1 AND !$parent_user->loaded()): ?>
								<div class="smallcont">
									<div class="labelcont">
										<label><span>Рубрики с ограничениями</span></label>
									</div>
									<div class="fieldscont">										
										<div class="">
											<div class="inp-cont">
												<div class="pt4">
												<? foreach($categories_limit as $category):?>
													<?=$category->title?> (<?=$category->max_count_for_user?>),
												<? endforeach; ?>
												</div>
												<span class="inform">
													<span>В эти рубрики можно подать не более указанного количества объявлений.</br>
															Для большего количества нужно сменить тип учетной записи на "Компания". Либо привязать свою учетную запись к компании. </br>
															Для некоторых случаев, мы можем расширить лимиты индивидуально, обратитесь в техподдержку.
													</span>
												</span>
									  		</div>
										</div>
									</div>									
								</div>
							<? endif; ?>
							<? if (!$parent_user->loaded() AND count($individual_limit)>0): ?>
								<div class="smallcont">
									<div class="labelcont">
										<label><span style="color:red">Индивидуальные ограничения</span></label>
									</div>
									<div class="fieldscont">										
										<div class="">
											<div class="inp-cont">
												<div class="pt4">
												<? foreach($individual_limit as $category):?>
													<?=$category['title']?> (<?=$category['individual_limit']?>),
												<? endforeach; ?>
												</div>
												<span class="inform">
													<? if ($user->org_type == 1): ?>
														<span>Для этих рубрик, установлены расширенные лимиты на количество объявлений индивидуально для Вас</span>
													<? else: ?>
														<span><?=Kohana::message('validation/object_form', 'max_objects_company')?></span>
													<? endif; ?>
												</span>
									  		</div>
										</div>
									</div>									
								</div>
							<? endif; ?>
						</div>
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
									    			<?=$field["html"]?>
									    			<? if ($field["path"]):?>
									    				<img src="<?=$field["path"]?>" style="padding:10px;">
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
									<div onclick="$('#userinfo').submit()" class="button blue icon-arrow-r btn-next"><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Сохранить</span></div>							
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