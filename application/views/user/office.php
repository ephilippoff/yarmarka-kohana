<?php $contact_classes = array(
	1 => 'tel',
	2 => 'tel',
	3 => 'skype',
	4 => 'icq',
	5 => 'email',
	) ?>

	<div class="winner cabinet office">
		<section class="main-cont">
			<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
			<div class="fl100 shadow-top z1 persomal_room ie8mt-150fix filial-bl">

				<?=View::factory('user/_left_menu')?>

				<section class="p_room-inner">
					<header><span class="title"></span></header>
					<div class="p_cont">
						<section class="filials-bl reducting">
							<article class="informator">
								<p class="title"><span>Что это такое?</span>
									<!--<a href="" class="toggle"><span class="show">свернуть</span><span>развернуть</span></a>-->
								</p>
								<div class="cont">
									<p style="text-align: justify">
										А тут должно быть много текста,G чтобы вертска к херам не свернулась.
										Ещееееее бооольше текста! Еще! Еще!Еще!Еще!Еще!Еще! Еще!Еще!Еще!Еще!Еще!Еще!Еще!Еще!Еще!Еще! И ни за что не нажимай на кнопку СВЕРНУТЬ!
									</p>
								</div>
							</article>

							<?php foreach ($users as $user) : ?>
								<article class="article">
									<div class="visible-bl">
										
											<div class="img">
												<?php if ($user->filename) : ?>
													<img src="<?=Uploads::get_file_path($user->filename, '272x203')?>" />
												<?php else : ?>	
													<img src="/images/nophoto2.png">
												<?php endif ?>
												<div class="number">#<?=$user->id?></div>
											</div>
										
										<div class="content">
											<div class="right-b">
												<div class="publish">
													<a href="#" id="remove_link" data-user_id="<?=$user->id?>">
														<span class="cont">Удалить</span>
														<span class="remove"></span>
													</a>
												</div>
											</div>
											<p class="title"><?=$user->get_user_name()?>
												<span class="inf"><!--(Cотрудник компании)--></span></p>
												<p class="addr"><?=$user->address?></p>
												<div class="map-bl">
													<div class="map">
														<div id="ymaps-map-id_1352895717894414938722" style="width: 372px; height: 236px;">
														</div>
													</div>
												</div>
<!--												<p class="tags">
													<a href="">Недвижимость,</a><a href="">ипотека,</a><a href="">ссуда,</a><a href="">ломбард</a>
												</p>-->
												<div class="contacts ">
													<ul>
														<li class="title">
															<label><span><i class="name">Контакты:</i></span></label>
														</li>
														<li class="add-contact-li">


															<span id="user_contacts">
																<?=Request::factory('block/user_contacts/'.$user->id)->execute()?>
															</span>
														</li>
													</ul>
												</div>
											</div>
										</div>	
									</article>
								<?php endforeach ?>

						<h2 class="mt10 mb10 ml20">Отправленные запросы:</h2>
						<?php foreach ($links as $link) : ?>
							<article class="article">
								<div class="visible-bl">
									<div class="img">
										<?php if ($user->filename) : ?>
											<img src="<?=Uploads::get_file_path($user->filename, '272x203')?>" />
										<?php else : ?>	
											<img src="/images/nophoto2.png">
										<?php endif ?>
										<div class="number">#<?=$user->id?></div>
									</div>
									<div class="content">
										<div class="right-b">
											<div class="publish">
												<a href="#" id="cancel_link" data-id="<?=$link->id?>">
													<span class="cont">Отменить</span>
													<span class="remove"></span>
												</a>
											</div>
										</div>
										<p class="title"><?=$link->linked_user->get_user_name()?>
											<span class="inf"><!--(Cотрудник компании)--></span></p>
											<p class="addr"><?=$link->linked_user->address?></p>
											<div class="map-bl">
												<div class="map">
													<div id="ymaps-map-id_1352895717894414938722" style="width: 372px; height: 236px;">
													</div>
												</div>
											</div>
<!--											<p class="tags">
												<a href="">Недвижимость,</a><a href="">ипотека,</a><a href="">ссуда,</a><a href="">ломбард</a>
											</p>-->
											<div class="contacts ">
												<ul>
													<li class="title">
														<label><span><i class="name">Контакты:</i></span></label>
													</li>
													<li class="add-contact-li">


														<span id="user_contacts">
															<?=Request::factory('block/user_contacts/'.$link->linked_user->id)->execute()?>
														</span>
													</li>
												</ul>
											</div>
										</div>
									</div>	
								</article>
							<?php endforeach ?>
						</section>
				</div>


				<div class="clear"></div>

				<article class="iinput-bl">
					<ul><li>
						<div class="inp-cont-bl">
							<div class="inp-cont">
								<div class="contact-bl">
									<div class="inp">
										<input type="text" name="link_to" id="link_to" placeholder="Логин или email" />
									</div>
									<span class="add-contact mt10" id="link_to_company"><span class="href"><span class="ico"></span>
									<span class="text">добавить сотрудника</span></span></span>
								</div>

								<div class="alert-bl profile-alert" id="error_block" style="display:none">
									<div class="cont">
										<div class="img"></div>
										<div class="arr"></div>
										<p class="text"><span></span></p>
									</div>
								</div>
							</div>

						</div>						                    			
					</li></ul>
				</article>
			</section>
		</div>	   

	</section>
</div><!--end content winner-->
