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
			<div class="info-block" style="height: 275px;">
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
						<img src="/images/mylogo.png" alt="" />
					<?php endif; ?>
				</a>
			</div>
		</div>
		<div class="hheader persomal_room-header"><h1 style="font-size: 20px" class="ta-c"><?=$user->org_name?></h1></div>
		<div class="fl100 shadow-top z1 persomal_room ie8mt-150fix filial-bl">
			<aside class="p_room-menu float-content">
				<ul class="islide-menu float-box w250">
					<li class="active">
						<span class="span_a"><i class="ico ico-iabout"></i><span>О компании</span></span>
					</li>
					
					<li ><a href="<?=$filter_href?>"><i class="ico ico-iadd"></i><span>Объявления</span></a>
					</li>
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
							<li>
								<span class="title ">О компании:</span>
								<div class="cont">
									<p style="color: #808080;text-align: justify"><?=$user->about?></p>
								</div>
							</li>
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
									<?php if ($user->org_address) : ?>
									<li class="article">
										<span class="title">Адрес/Местоположение:</span>
										<div class="cont">
											<?php if ( ! $user->user_city->loaded() AND ! trim($user->org_address)) : ?>
											<span>Не указано</span>
											<?php else : ?>
											<span><?=$user->user_city->loaded() ? $user->user_city->title.',' : ''?> <?=$user->org_address?></span>
											<?php endif; ?>
											<span class="show-map toggle"><br/><br/>
												<span class="show">На карте</span>
												<span class="">свернуть карту</span>
											</span>
											<div class="map-bl">
												<?php if ($user->location->loaded()) : ?>
													<input type="hidden" name="coord" id="coord" value="<?=$user->location->lon?>,<?=$user->location->lat?>" />
												<?php else : ?>
													<input type="hidden" name="coord" id="coord" value="" />
												<?php endif ?>
												<input type="hidden" name="org_address" id="org_address" value="<?=$user->user_city->title?>, <?=$user->org_address?>" />
												<div class="map"><div id="ymaps-map-id" style="width: 372px; height: 372px;"></div>
												<script type="text/javascript" src="//api-maps.yandex.ru/2.0-stable/?load=package.standard,package.geocode,package.geoQuery,package.clusters&coordorder=longlat&lang=ru-RU&onload=init_userpage_map"></script>
												</div>
											</div>
										</div>
									</li>
									<?php endif; ?>
								</ul>
							</li>

						</ul>
						<div style="" class="shadow-bottom fl100 pt7"></div>
					</article>
				</div>
			</section>
		</div>	   

	</section>
</div><!--end content winner-->