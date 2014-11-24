<script src="http://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU" type="text/javascript"></script>

<?php $units = $user->getAllUnits(); ?>

<div class="winner">
	<section class="main-cont">
		<div class="mbanner">
			<div class="tline">
				<div class="left">
					<div class="img">
						<img src="/images/logo.png" alt="" class="ilogo navtoggle" />
						<a href="" class="nav toggle navtoggle"></a>

					</div>
				</div>

			</div>
			<div class="info-block no-photo" style="height: 275px;">
				<div class="title-add-photo" onClick="$('#banner_input').click()">Добавьте сюда<br/> свое рекламное предложение или фото</div>
				<?php if ($is_owner) : ?>
					<div class="bg-mbanner-redact">
						<div><a href="" class="reduct">Изменить</a><a href="" class="del">Удалить</a></div>
					</div>
				<?php endif; ?>
				<?php if ($user->userpage_banner) : ?>
					<img src="<?=URL::site($user->userpage_banner)?>" id="main_image" alt="" />
				<?php else : ?>
					<img src="/images/banner.png" id="main_image" alt="" />
				<?php endif; ?>

				<a href="" class="my_logo">
					<?php if ($user->filename) : ?>
						<img src="<?=URL::site(Uploads::get_file_path($user->filename, '272x203'))?>" alt="" />
					<?php else : ?>
						<img src="/images/nologo.png" alt="" />
					<?php endif; ?>
				</a>
			</div>
		</div>





		<div class="hheader persomal_room-header ta-c">
			<h1 style="font-size: 20px" class="ta-c d-in">
						<?php if (empty($user->org_name)) : ?>
								Страница компании №<?=$user->id?>
						<?php else : ?>
								<?=htmlspecialchars($user->org_name)?> 
						<?php endif; ?>					
			</h1>
			<a class="bnt-go-back" href="/" rel='nofollow'><span class="text">На главную</span></a>
			<?php if (Auth::instance()->get_user() AND $user->id == Auth::instance()->get_user()->id) : ?><a class="bnt-go-back" href="<?=URL::site('user/userinfo')?>" rel='nofollow'><span class="text">Редактировать</span></a><?php endif; ?>
		</div>
		<div class="fl100 shadow-top z1 persomal_room ie8mt-150fix filial-bl">
			<aside class="p_room-menu float-content">
				<ul class="islide-menu float-box w250">
					<li class="active">
						<span class="span_a"><i class="ico ico-iabout"></i><span>О компании</span></span>
					</li>
					<?php if ($job_adverts_count > 0) : ?>
						<li class="no-li-slide"><a href="<?=$job_category_href?>"><i class="ico ico-iadd"></i><span>Вакансии&nbsp;<span>(<?=$job_adverts_count?>)</span></span></a></li>										
					<?php endif;?>
						
					<?php if ($is_exist_objects) : ?>						
						<li class="no-li-slide"><a href="<?=$filter_href?>"><i class="ico ico-iadd"></i><span>Объявления</span></a></li>
					<?php endif;?>	
						
					<li>
						<div class="conpany_info w200">
							<header><?=$user->org_name?></header>
							<article class="cont">
								<span class="title ">Контакты:</span><br/>
								<?php foreach ($user->get_contacts(array(Model_Contact_Type::PHONE, Model_Contact_Type::MOBILE)) as $contact) : ?>
									<span><?=$contact->contact?></span><br/>
								<?php endforeach; ?>
								<br />

								<span class="title ">E-mail:</span><br/>
								<?php foreach ($user->get_contacts(Model_Contact_Type::EMAIL) as $contact) : ?>
									<span><a href="mailto:<?=$contact->contact?>">Написать письмо</a></span><br/><br/>
								<?php endforeach; ?>

								<span class="title ">Адрес:</span><br/>
								<?php if ($user->user_city->loaded() AND trim($user->org_address)) : ?>
									<span><?=$user->user_city->loaded() ? $user->user_city->title.',' : ''?> <?=$user->org_address?></span><br />
								<?php endif; ?>
								<br />
								<?php if ($user->url) : ?>
									<span><a href="<?=URL::prep_url($user->url)?>" target="_blank">Перейти на сайт</a></span>
								<?php endif; ?>
							</article>
						</div>
						<div></div>
					</li>
				</ul>

			</aside>
			<section class="p_room-inner">

				<div class="p_cont">

					<article class="person_info-bl">
						<ul class="sistem-ul">
							<?php if (!empty($user->about)) : ?>
								<li>
									<span class="title ">О компании:</span>
									<div class="cont">
										<p style="color: #808080;text-align: justify"><?=$user->about?></p>
									</div>
								</li>
							<?php endif; ?>
							<?php if (FALSE) : ?>
							<li><span class="title">Видео:</span>
								<div class="cont">
									<object width="515" height="300"><param name="wmode" value="opaque"><param name="movie" value="http://www.youtube.com/v/H7Xyboh6GpY?version=3&amp;hl=ru_RU"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed wmode="opaque" src="http://www.youtube.com/v/H7Xyboh6GpY?version=3&amp;hl=ru_RU" type="application/x-shockwave-flash" width="515" height="300" allowscriptaccess="always" allowfullscreen="true"></embed></object>
								</div>	                    							
							</li>
							<?php endif; ?>
						</ul>
					</article>
					<article class="person_info-bl person_contakt-bl contact shadow-top">
						<ul class="sistem-ul cont-info">
							<?php $contact_classes = array(
								1 => 'tel',
								2 => 'tel',
								3 => 'skype',
								4 => 'icq',
								5 => 'email',
							) ?>
							<li ><span class="title ">Контакты:</span>
								<ul>
									<?php foreach ($user->get_contacts() as $contact) : ?>
									<li>
										<span class="title"><?=$contact->name?>:</span>
										<div class="cont">
											<span class="ico <?=$contact_classes[$contact->contact_type_id]?>"></span>
											<span class="text"><?=$contact->contact?></span>
										</div>
									</li>
									<?php endforeach; ?>
									<li>
										<span class="title">Контактное лицо/ФИО:</span>
										<div class="cont"><?=$user->fullname?></div>
									</li>
									
									
									
									
								</ul>
							</li>

						</ul>
						<div style="" class="shadow-bottom fl100 pt7"></div>
					</article>
				</div>


				<?php if ( !empty($units) ) : ?>
					<section class="filials-bl mt15">
        				<h3>Адреса компании</h3>
						
						
						
																																	
							<div class="map-bl mb20">
								<?php if ($user->location->loaded()) : ?>
									<input type="hidden" name="coord" id="coord" value="<?=$user->location->lon?>,<?=$user->location->lat?>" />
								<?php else : ?>
									<input type="hidden" name="coord" id="coord" value="" />
								<?php endif; ?>
								<input type="hidden" name="org_address" id="org_address" value="<?=$user->user_city->title?>, <?=$user->org_address?>" />
								<div class="map big"><div id="ymaps-map-id" style=" height: 372px;"></div>



								<script>
										var myMap_user;
										ymaps.ready(init_<?=$user->id?>);

												function init_<?=$user->id?> () {
													//[57.153522, 65.608924]
													// '<?=$user->geo_loc?>'
													var myGeocoder = [<?=$region->geo_loc?>];
													var coords = myGeocoder; 

													myMap_user = new ymaps.Map ("ymaps-map-id", {
														center: coords,
														zoom: 5,
													});

													myMap_user.controls.add("zoomControl").add("mapTools").add(new ymaps.control.TypeSelector(["yandex#map", "yandex#satellite", "yandex#hybrid", "yandex#publicMap"]));


	//															        myMap_user.geoObjects.add(new ymaps.Placemark(coords, { 
	//															            hintContent: '<?=$user->fullname?>', 
	//															            balloonContent: '<?=$user->fullname.", ".$user->location->city.", ".$user->location->address?>' 
	//															        }));

													var myGeocoder2;

													<?php 

													foreach($units as $unit) : ?>
														myGeocoder2 = [<?=$unit->location->lat.", ".$unit->location->lon?>];
														coords = myGeocoder2; 

														myMap_user.geoObjects.add(new ymaps.Placemark(coords, { 
															hintContent: '<?=$unit->title?>', 
															balloonContent: '<?=$unit->title.", ".$unit->location->city.", ".$unit->location->address?>' 
														}));

													<?php endforeach; ?>
												}
									</script>
								</div>
							</div>
									
						
						
						
						
						<?php foreach($units as $unit) { ?>
        				<article class="article">
        					
        					<div class="visible-bl">
        						<div class="img">
									<div class="img-container ta-c">
										<?php if (!empty($unit->filename)) : ?>
											<img src="<?=Uploads::get_file_path($unit->filename, '136x136')?>" alt="" />
										<?php else : ?>
											<div class="ta-c">Фото отсутствует</div>
										<?php endif ?>
									</div>
								

								</div>
        						<div class="content">
        							
        							<p class="title"><?=$unit->title ?><span class="inf">(<?=$unit->unit->title ?>)</span></p>
        							
									<?php
									if($unit->location) : ?>
									<p class="addr"><?php echo $unit->location->city ?>
										<?php if (trim($unit->location->address) != '') : ?>
											, <?php echo $unit->location->address; ?> <span class="show-map toggle"><span class="show">на карте</span><span>свернуть карту</span></span>
										<?php endif ?>
									</p>
        							<div class="map-bl">
	                    				<div class="map"><div id="ymap_<?=$unit->id?>" style="width: 372px; height: 236px;"></div>


		                    				<script type="text/javascript">
										        ymaps.ready(init_<?=$unit->id?>);
										 
										        function init_<?=$unit->id?> () {
										            var myGeocoder = [<?=$unit->location->lat.", ".$unit->location->lon?>];
										            var myMap = new ymaps.Map('ymap_<?=$unit->id?>', {
										                    center: myGeocoder, 
										                    zoom: 12
										                });
													var myPlacemark = new ymaps.Placemark(
														myGeocoder        
													);
													myMap.geoObjects.add(myPlacemark);
										        }
										    </script>


                             			</div>
	                    			</div><?php endif; ?>
									<?php if( ! empty($unit->description)) : ?><p class="pt10">
										<?=nl2br($unit->description);?>
									</p><?php endif; ?>
									<div class="contacts oh">
										<ul>
											<li class="title">
												<label><span><i class="name">Контакты:</i></span></label>
											</li>
											<li class="add-contact-li">											
												<?php echo $unit->contacts ?>
											</li>
										</ul>
									</div>
									
									<?php if (!empty($unit->web)) : ?>
										<p class="site-link pt10">
											<a target="_blank" rel="nofollow" href="<?=URL::prep_url($unit->web)?>"><?=URL::prep_url($unit->web)?></a>	
										</p>
									<?php endif;?>									
									
        						</div>
        					</div>
        				</article>
						<?php } ?>
        			</section>
				<?php endif; ?>
			</section>	
		</div>	   

	</section>
</div>