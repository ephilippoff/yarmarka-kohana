<script type="text/javascript" src="/js/adaptive/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" charset="utf-8">
	// wisiwyg
	tinyMCE.init({
			mode : "textareas",
			editor_selector : "tiny",
			theme : "simple",
			language: "ru",
			plugins : "paste",
			width: "335px",
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
	
$(document).ready(function() {
	//Всё для ие8
//	$('.filebutton img').click(function(e){
//		$('#avatar_input').click();
//		e.preventDefault();
//		e.stopPropagation();
//	})
})		
</script>
<div class="winner cabinet profile">
	<section class="main-cont">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">
				<header><span class="title">Личные данные</span></header>
				<div class="p_cont secure-bl myinfo">
					<article class="iinput-bl">

					<span id="link_requests_block">
					<?=Request::factory('block/user_link_requests')->execute()?>
					</span>

					<span class="linked_to_block" id="linked_to_block">
					<?=Request::factory('block/user_linked_to')->execute()?>
					</span>
					

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
								1 => 'mobtel',
								2 => 'tel',
								3 => 'skype',
								4 => 'icq',
								5 => 'email',
							) ?>
							
							<span id="verified_user_contacts">
							<?=Request::factory('block/verified_profile_contacts')->execute()?>
							</span>

							<span id="user_contacts">
							<?=Request::factory('block/user_profile_contacts')->execute()?>
							</span>

							<div class="input style2 inp-add-cont">
																	
								<div class="inp-cont-bl ">
									<label class="label">
										<div class="select chzn-container chzn-container-single">
											<a href="" class="chzn-single chzn-single-with-drop">
												<div class="ico tel"></div>
												<span class="text">Телефон (моб.)</span>
												<div class="btn-select"><b></b></div>
											</a>
											<div class="select-cont chzn-drop">
												<ul class="chzn-results">
													<?php foreach ($contact_types as $ct) : ?>
														<li data-id="<?=$ct->id?>" 
															id="<?=$contact_classes[$ct->id]?>" 
															data-format="<?=$ct->format?>"
															class="active-result">
															<span class="ico <?=$contact_classes[$ct->id]?>"></span>
															<?=$ct->name?>
														</li>
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
														<input type="hidden" name="contact_type" data-id="contact_type" id="contact_type" value="1" />
														<input class="" type="text" name="contact" data-id="contact" id="contact">
													</div>
													<span class="btn-act apply add_contact"></span>
													<span class="inform"><span>Вы можете добавить несколько контактов для оперативной связи с вами</span></span>
												</span>		                    						
										</div>
									</div>

									<div class="alert-bl contact-alert">
										<div class="cont">
											<div class="img"></div>
											<div class="arr"></div>
											<p class="text"><span></span></p>
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
