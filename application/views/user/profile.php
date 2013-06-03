<script type="text/javascript" src="/js/adaptive/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" charset="utf-8">
	// wisiwyg
	tinyMCE.init({
			mode : "textareas",
			editor_selector : "tiny",
			theme : "simple",
			language: "ru",
			plugins : "paste",
			paste_text_sticky : true,
			setup : function(ed) {
				ed.onInit.add(function(ed) {
				  ed.pasteAsPlainText = true;
				});

				//ed.onKeyUp.add(function(ed, e) {
					//var text = tinyMCE.activeEditor.getContent({format : 'raw'});
				//});
			}
	});
</script>
<div class="winner">
	<section class="main-cont">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<aside class="p_room-menu">
				<ul class="islide-menu">
					<li><a href=""><i class="ico ico-myadd"></i><span>Мои объявления</span></a>
						<ul class="no_text-decoration">
							<li class="active"><a href=""><i class="ico "></i><span>Активные</span></a>
								<ul>
									<li><a href=""><i class="ico "></i><span>Знакомства</span></a></li>
									<li><a href=""><i class="ico "></i><span>Автомобили</span></a></li>
									<li><a href=""><i class="ico "></i><span>Домашние любимцы</span></a></li>
								</ul>
							</li>
							<li><a href=""><i class="ico "></i><span>Неактивные</span></a>
								<ul>
									<li><a href=""><i class="ico "></i><span>Знакомства</span></a></li>
									<li><a href=""><i class="ico "></i><span>Автомобили</span></a></li>
									<li><a href=""><i class="ico "></i><span>Домашние любимцы</span></a></li>
								</ul>
							</li>
							<li><a href=""><i class="ico "></i><span>На модерации</span></a></li>
							<li><a href=""><i class="ico "></i><span>Черновики</span></a></li>
							<li><a href=""><i class="ico "></i><span>Удаленные</span></a></li>
							<li class="mt31"><a href=""><i class="ico ico-favorites"></i><span>Избранные</span></a></li>
						</ul>
					</li>
					<li><a href=""><i class="ico ico-mysub"></i><span>Мои подписки</span></a></li>
					<li><a href=""><i class="ico ico-myserv"></i><span>Сервисы</span></a>
						<ul>
							<li><a href=""><i class="ico "></i><span>Размещенные в нескольких городах</span></a></li>
							<li><a href=""><i class="ico "></i><span>«Ярмарка +»</span></a></li>
							<li><a href=""><i class="ico "></i><span>Безопасность</span></a></li>
							<li><a href=""><i class="ico "></i><span>Счета</span></a></li>
							<li><a href=""><i class="ico "></i><span>Безопасность</span></a></li>
						</ul>
					</li>
					<li><a href="" id="islide_profile"><i class="ico ico-profile"></i><span>Профиль</span></a>
						<ul>
							<li>
								<i class="ico "></i><span>
									Личные данные
								</span>
							</li>
							<li><a href=""><i class="ico "></i><span>Счета</span></a></li>
							<li><a href=""><i class="ico "></i><span>Безопасность</span></a></li>
						</ul>
					</li>
				</ul>
			</aside>
			<section class="p_room-inner">
				<header><span class="title">Личные данные</span></header>
				<div class="p_cont secure-bl myinfo">
					<article class="iinput-bl">
					<?php if ($user->org_type == 2) : ?>
						<? include '_profile_org.php'?>
					<?php else : ?>
						<? include '_profile_user.php'?>
					<?php endif; ?>
					</article>
					<article class="iinput-bl shadow-top smallcont mb100">
						<ul>
							<li class="title">
								<label><span><i class="name">Контакты:</i></span></label>
								<div class="help-bl">
									<div class="baloon">
										<div class="alert-bl">
											<div class="cont">
												<div class="img"></div><div class="arr"></div>							                							
												<p class="text"><span>Важно заполнить поле e-mail правильно, иначе вы не сможете активировать свой аккаунт следовательно лишитесь всяких фишек и плющек, а еще... &nbsp;  <a href="">>>></a></span></p>
											</div>
										</div>
									</div>
									<span class="href fr mr12"><i class="ico"></i></span>
								</div>
							</li>
							<li class="add-contact-li">
								
							<?php $contact_classes = array(
								1 => 'tel',
								2 => 'tel',
								3 => 'skype',
								4 => 'icq',
								5 => 'email',
							) ?>
							
							<span id="user_contacts">
							<?=Request::factory('block/user_profile_contacts')->execute()?>
							</span>

							<div class="input style2 inp-add-cont">
																	
								<div class="inp-cont-bl ">
									<label class="label">
										<div class="select chzn-container chzn-container-single">
											<a href="" class="chzn-single chzn-single-with-drop">
												<div class="ico tel"></div>
												<span class="text">Телефон</span>
												<div class="btn-select"><b></b></div>
											</a>
											<div class="select-cont chzn-drop">
												<ul class="chzn-results">
													<?php foreach ($contact_types as $ct) : ?>
														<li data-id="<?=$ct->id?>" id="<?=$contact_classes[$ct->id]?>" class="active-result"><span class="ico <?=$contact_classes[$ct->id]?>"></span><?=$ct->name?></li>
													<?php endforeach; ?>
												</ul>
											</div>
										</div>
										
									</label>
									<div class="inp-cont z1">
										<div class="contact-bl">		                    						
											<span class="add-contact contact">
												<span class="cont-info">
													<span class="ico tel findme"></span>
													<div class="inp" id="new_contact">
														<input type="hidden" name="contact_type" id="contact_type" value="2" />
														<input class="" type="text" name="contact">
													</div>
													<span class="btn-act apply add_contact"></span>
													<span class="inform"><span>Вы можете добавить несколько контактов для оперативной связи с вами</span></span>
												</span>		                    						
										</div>
									</div>
								</div>
								<div class="inp-cont-bl mt8">
									<div class="inp-cont">
										<div class="contact-bl">		                    						
											<!--<span class="add-contact"><span class="href"><span class="ico"></span><span class="text">добавить еще контакты</span></span><span class="info"></span></span>-->
										</div>
									</div>
								</div>
								
							</div>
							</li>
						</ul>	
					</article>
				</div>
			</section>
		</div>	   
		  
	</section>
</div><!--end content winner-->
