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

					<form method="POST" id="orginfo" enctype="multipart/form-data">
						<div class="fl100  pt16 pb15">
							<div class="smallcont">
								<div class="labelcont">
									<label><span>Email</span></label>
								</div>
								<div class="fieldscont">										
									<div class="">
										<div class="inp-cont">
											<?=$user->email?>
											<span class="inform">
												<span>Можно сменить через техподдержку</span>
											</span>
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
											<?=$types[$user->org_type]?>
											<? if ($user->org_type == 2): ?>
												<span class="inform">
													<span>Необходимо заполнить информацию о компании <a href="/user/orginfo">здесь</a></br>
															Вы можете сбросить тип учетной записи на "Частное лицо" если перейдете по <span class="link" style="cursor:pointer;" href="/user/reset_orgtype" onclick="reset_orgtype()">ссылке</span>. Учитывайте что объявления сверх лимитов будут сняты с публикации
													</span>
												</span>
											<? endif; ?>
											<? if ($user->org_type == 1): ?>
												<span class="inform">
													<span>Вы можете сменить тип учетной записи на "Компания" если перейдете по <a href="/user/reset_to_company">ссылке</a></br>
															Для подтверждения потребуется загрузить скан оригинала или копии ИНН.
													</span>
												</span>
											<? endif; ?>
								  		</div>
									</div>
								</div>									
							</div>
							<? if ($user->org_type == 1): ?>
								<div class="smallcont">
									<div class="labelcont">
										<label><span>Рубрики с ограничениями</span></label>
									</div>
									<div class="fieldscont">										
										<div class="">
											<div class="inp-cont">
												<? foreach($categories_limit as $category):?>
													<?=$category->title?> (<?=$category->max_count_for_user?>),
												<? endforeach; ?>
												<span class="inform">
													<span>В эти рубрики можно подать не более указанного количества объявлений.</br>
															Для большего количества нужно сменить тип учетной записи на "Компания". </br>
													</span>
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