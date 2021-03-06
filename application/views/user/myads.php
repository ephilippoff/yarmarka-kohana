    <div class="winner">
        <section class="main-cont myads cabinet">
        <div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет &rarr; Мои объявления</h1></div>
        <div class="fl100 shadow-top z1 persomal_room">

			<?=View::factory('user/_left_menu')?>

            <section class="p_room-inner">
				<header>
					<form method="get" id="ads_filter" action="/user/myads">
					<? /* ?>
					<div class="input style3">
						<div class="inp-cont-bl ">
							<div class="inp-cont">
								<i class="imp320 imp">&nbsp; *</i>
								<select class="iselect " name="region_id" id="region_id">
									<option value="">-- регион --</option>
									<?php foreach ($regions as $region) : ?>
									<option value="<?=$region->id?>" <?=Arr::get($_GET, 'region_id') == $region->id ? 'selected' : ''?>><?=$region->title?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
					<? */ ?>
					<div class="input style3">
						<div class="inp-cont-bl ">
							<div class="inp-cont">
								<i class="imp320 imp">&nbsp; *</i>
								<select class="iselect " name="city_id" id="city_id">
									<option value="">--  город --</option>
									<?php foreach ($cities as $city) : ?>
									<option value="<?=$city->id?>" <?=Arr::get($_GET, 'city_id') == $city->id ? 'selected' : ''?>><?=$city->title?>  (<?=$city->count?>)</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
					
					<div class="input style3">
						<div class="inp-cont-bl ">
							<div class="inp-cont">
								<i class="imp320 imp">&nbsp; *</i>
								<select class="iselect " name="category_id" id="category_id">
									<option value="">--  категория --</option>
									<?php foreach ($categories as $category) : ?>
									<option value="<?=$category->id?>" <?=Arr::get($_GET, 'category_id') == $category->id ? 'selected' : ''?>><?=$category->title?> (<?=$category->count?>)</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
					<div class="input pseach">
						<div class="inp-cont-bl ">
							<div class="inp-cont">
								<div class="inp">
									<input style="box-sizing: initial" type="text" name="text" value="<?=Arr::get($_GET, 'text')?>" placeholder="Искать объявления по содержимому">
								</div>
							</div>
						</div>
					</div>
					<input type="submit" style="visibility:hidden" />
					<div class="btn-red btn-find" onClick="$('#ads_filter').submit();"></div>
					</form>
				</header>
            <div class="p_cont myadd">
            <div class="nav">
                <div class="input style2">
                    <div class="inp-cont-bl ">
                        <div class="inp-cont">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="checkbox" value="checkbox" id="select_all"><span>Выделить все</span>
                                </label>

                                <label class="no-box">
                                    <input type="checkbox" name="checkbox" value="checkbox" id="delete_selected"><span>Удалить выделенное</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>		
				<div class="input style2 notice" style="font-size: 12px;">Внимание! В разделе "Вакансии" поднятие объявления доступно 1 раз в сутки. В остальных разделах поднятие объявления доступно 1 раз в 3 суток. УСЛУГА БЕСПЛАТНАЯ!</div>
				<? if ($premium_balance>0):?>
					<div class="input style2 notice" style="font-size: 12px; color: green;">Ваш лимит Премиум объявлений - <span id="fn-premium-balance"><?=$premium_balance?><span></div>
				<? endif;?>
            </div>		
			<header>
				<div class="col1"><span>Фото/номер</span></div>
				<div class="col5"><span>Услуги</span></div>
				<div class="col4"><span>Функции</span></div>
				<div class="col3"><span>Цена</span></div>
				<div class="col2"><span>Название</span></div>	
	        </header>				
				
				
				
            <div class="cont contmyadd">
                <?php foreach ($objects as $ad) : ?>
				
						<?php 
						$user_messages = $ad->user_messages->order_by('createdOn', 'DESC')->cached(DATE::HOUR)->find_all(); 
						$obj_is_active = ($ad->is_bad == 0 AND ! $ad->in_archive AND $ad->is_published); //активность объявления  
						//Цена или зарплата
						if (!$price = ORM::factory('Object')->set_time_link_cache(15)->get_intattr_value_by_id($ad->id, 44))
							$price = ORM::factory('Object')->set_time_link_cache(15)->get_intattr_value_by_id($ad->id, 71);
						?>
				
					    <?
					    	$serviceup = 'default';
					    	$true_categories 		= Kohana::$config->load('billing.premium_ads_categories');
					    	$premium_ads_price 		= Kohana::$config->load('billing.premium_ads_price');
					    	$premium_enabled 		= Kohana::$config->load('billing.premium_enabled');

					    	if ($premium_enabled) {
						    	if (count($true_categories) > 0 AND in_array($ad->category, $true_categories)){
						    		$serviceup = 'premium';

						    	} else if(in_array($ad->category, $true_categories)){
						    		$serviceup = 'premium';
						    	}
						    }
		
						?>

						<? if (array_key_exists($ad->id, $already_buyed)) 
							$is_premium = TRUE; 
						else 
							$is_premium = FALSE; ?>
				
						<div class="li <? if (!$obj_is_active) : ?> blocked <? elseif ($is_premium):?> premium <? else: ?> active <? endif;?>">
							<div class="left-bl">
								<div class="top-bl">
									<div class="col1">
										<div class="input">			                    			
											<div class="inp-cont-bl ">
												<div class="inp-cont">
													<div class="checkbox">
														<label>
															<form action="#" method="post"><input name="to_del[]" class="to_del" type="checkbox" value="<?php echo $ad->id ?>"></form>
														</label>
													</div>
												</div>
											</div>
										</div>
										<div class="img">
												<?php if ($ad->main_image_filename) : ?>
													<?php list($width, $height) = Uploads::get_optimized_file_sizes($ad->main_image_filename, '120x90', '70x50') ?>
													<img src="<?=Uploads::get_file_path($ad->main_image_filename, '120x90')?>" 
													
													title="<?=$ad->main_image_title ?>">

												<?php endif; ?>											
										</div>
										<p class="number">
											<?php if ($ad->is_bad == 0 AND ! $ad->in_archive) : //не блок и не архив?>

                                            <?php  if ( ! $ad->is_published) : //не опубликовано?>

                                                <span>Снято</span>

                                                <?php else : //опубликовано?>

                                                <span>Опубликовано до <?=date('d.m.Y', strtotime($ad->date_expiration))?></span>

                                                <?php endif; ?>

                                            <?php	else : //либо блок, либо архив ?>

                                            <?php	if ($ad->is_bad == 2 AND $ad->in_archive) : //блок2 и архив ?>

                                                <span title="Это объявление заблокировано и перемещено в архив" class="object_blocked">Заблокировано окончательно</span>

                                                <?php	elseif ($ad->is_bad == 2 AND ! $ad->in_archive) : //блок2 и не архив?>

                                                <span title="Объявление заблокировано Модератором окончательно" class="object_blocked">Заблокировано окончательно</span>

                                                <?php	elseif ($ad->is_bad == 1 AND $ad->in_archive) : //блок1 и архив?>

                                                <span title="Это объявление перемещено в архив, вы можете его продлить, но перед этим вам необходимо его исправить, т.к. объявление было отклонено модератором">В архиве</span>

                                                <?php	elseif ($ad->is_bad == 1 AND ! $ad->in_archive) : //блок1 и не архив?>

                                                <span title="Объявление заблокировано Модератором до исправления" >Заблокировано до исправления</span>

                                                <?php	elseif ($ad->in_archive) : //иначе просто архив ?>

                                                <span>В архиве</span>

                                                <?php	endif; ?>

                                        <?php endif; ?>
										</p>
									</div>			                    				
									<div class="col4">
										<ul>
							
										<? if (!$ad->is_bad): ?>

	                                        <? if ($ad->in_archive): //в архиве ?>

	                                            <li class="">
	                                                <select class="plolong-slc" id="prolong_<?=$ad->id ?>">
	                                                    <option value="2w">на 2 недели</option>
	                                                    <option selected value="1m">на 1 месяц</option>
	                                                    <option value="2m">на 2 месяца</option>
	                                                    <option value="3m">на 3 месяца</option>
	                                                </select>
	                                            </li>

	                                            <li class="">
	                                                <a title="Продлить объявление" href="" class="btn-funcmenu full-btn" id="prolong-btn<?=$ad->id?>" onclick="prolong(<?=$ad->id?>); return false;">
	                                                    <i class="ico clock"></i><span>Продлить</span>
	                                                </a>
	                                            </li>

	                                            <li class="">
	                                                <a title="Редактировать объявление" href="<?=URL::site('user/edit_ad/'.$ad->id)?>" class="btn-funcmenu  ">
	                                                    <i class="ico change"></i><span>Изменить</span>
	                                                </a>
	                                            </li>

	                                        <? else : //не в архиве ?>


	                                            <?php if (empty($linked_user)) : ?>
			                                            <?php if ($ad->get_service_up_timestamp() < time()) : // Получаем дату, когда можно поднять объявление?>
			                                                <li class="">
			                                                    <a title="Поднять объявление в общем списке" href="" class="btn-funcmenu  " id="service-up-<?=$ad->id?>" onClick="service_up(<?=$ad->id?>, this); return false;">
			                                                        <i class="ico up"></i><span>Поднять</span>
			                                                    </a>
			                                                </li>
			                                            <?php else : ?>
			                                                <li class="">
			                                                    <a href="" class="btn-funcmenu disable noactive  " id="service-up-<?=$ad->id?>" 
			                                                       title="Вы можете поднять это объявление не раньше <?=date("d.m Y в H:i", $ad->get_service_up_timestamp())?>"
			                                                       onclick="return false;">
			                                                        <i class="ico up"></i><span>Поднять</span>
			                                                    </a>
			                                                </li>
			                                            <?php endif ?>
	                                            <?php endif?>

	                                            <li class="">
	                                                <a title="Редактировать объявление" href="<?=URL::site('user/edit_ad/'.$ad->id)?>" class="btn-funcmenu  ">
	                                                    <i class="ico change"></i><span>Изменить</span>
	                                                </a>
	                                            </li>

	                                            <?php if ($ad->is_published) : ?>
	                                                <li class="">
	                                                    <a title="Снять объявление с публикации" href="" class="btn-funcmenu  " id="pub_toggle_link_<?=$ad->id?>" onclick="pub_toggle(<?=$ad->id?>, this); return false;">
	                                                        <i class="ico hide"></i><span>Снять</span>
	                                                    </a>
	                                                </li>

	                                            <?php else : ?>
	                                                <li class="">
	                                                    <a title="Разместить объявление в публикацию" href="" class="btn-funcmenu  " id="pub_toggle_link_<?=$ad->id?>" onclick="pub_toggle(<?=$ad->id?>, this); return false;">
	                                                        <i class="ico show"></i><span>Разместить</span>
	                                                    </a>
	                                                </li>
	                                            <?php endif; ?>


	                                            <li class="">
	                                                <a title="Удалить объявление" href="" class="btn-funcmenu  " onclick="delete_ad(<?php echo $ad->id ?>, this); return false;" class="btn btn-lc active">
	                                                    <i class="ico del"></i><span>Удалить</span>
	                                                </a>
	                                            </li>


                                       		<? endif; ?>
                                        <? else:  ?>

                                                <? if ($ad->is_bad == 1 AND $ad->in_archive) : //блок1 и архив ?>

                                                <li class="">
                                                    <select class="plolong-slc" id="prolong_<?=$ad->id?>">
                                                        <option value="2w">на 2 недели</option>
                                                        <option selected value="1m">на 1 месяц</option>
                                                        <option value="2m">на 2 месяца</option>
                                                        <option value="3m">на 3 месяца</option>
                                                    </select>
                                                </li>

                                                <li class="">
	                                                <a title="Продлить объявление" href="" class="btn-funcmenu full-btn" id="prolong-btn<?=$ad->id?>" onclick="prolong(<?=$ad->id?>); return false;">
	                                                    <i class="ico clock"></i><span>Продлить</span>
	                                                </a>
	                                            </li>

	                                            <li class="">
                                                    <a title="Исправить объявление" href="<?=CI::site('user/edit_ad/'.$ad->id)?>" class="btn-funcmenu ">
                                                        <i class="ico show"></i><span>Исправить</span>
                                                    </a>
                                                </li>

                                                <? elseif ($ad->is_bad == 1 AND ! $ad->in_archive) : ?>

                                                <li class="">
                                                    <a title="Исправить объявление" href="<?=CI::site('user/edit_ad/'.$ad->id)?>" class="btn-funcmenu ">
                                                        <i class="ico show"></i><span>Исправить</span>
                                                    </a>
                                                </li>

                                                <?php endif; ?>

                                        <? endif; ?>										
										
										
										</ul>
									</div>
									
									
									
									<div class="col3">
										<div class="price-bl"><span class="price"><?php if (!$price) : ?> &mdash; <?php else : ?><?=$price?> р.<?php endif; ?></span></div>
										<div title="Количество комментариев" class="mess-bl"><span class="mess"><?=count($user_messages)?></span></div>
									    <div title="Количество просмотров" class="view-bl fn-stat" data-id="<?=$ad->id?>"><div class="iview"><?=$ad->visits?></div></div>
									</div>
									<div class="col2">
										<a href="<?=$ad->get_url()?>" target="_blank"><?=htmlspecialchars($ad->title)?></a>
										<div class="ml5">
											<p class="info">Публикуется в рубрике: <a href="<?=$ad->category_obj->get_url($ad->city_obj->region_id, $ad->city_id)?>"><?=$ad->category_obj->title?></a>, <?=$ad->city_obj->loaded() ? $ad->city_obj->title : $ad->city?></p>
											<p class="info">Расположение:
												<?php if ($ad->location_obj->loaded() AND $ad->location_obj->address) : ?>
													<?=$ad->location_obj->city?>,<?=$ad->location_obj->address?>
												<?php endif ?>
											</p>
											<p class="about"><?=strip_tags($ad->user_text)?></p>
											<?php if (count($user_messages) > 0) : ?><p class="panel-toggle"><span>Показать комментарии</span></p><?php endif; ?>
										</div>
									</div>
								</div>
								
								<?php if (count($user_messages)) : ?>
											<div class="bottom-bl hide-cont">
												<div class="msg-bl">										
													<?php foreach ($user_messages as $message) : ?>
													<?php		if ($message->user->role == 1 or $message->user->role == 3) : ?>		
																	<article class="msg moderator">
																		<a href=""><span class="date"><?=date('d.m.Y', strtotime($message->createdOn))?></span></a>
																		<p class="autor">Модератор</p>
																		<p><?=$message->text?></p>
																	</article>
													<?php		endif; ?>
													<?php endforeach; ?>

													<?php foreach ($user_messages as $message) : ?>
													<?php		if ($message->user->role != 1 and $message->user->role != 3) : ?>											
																	<article class="msg">
																		<a href=""><span class="date"><?=date('d.m.Y', strtotime($message->createdOn))?></span></a>
																		<p class="autor"><?=$message->user_name?></p>
																		<p><?=$message->text?>
																			<a target="_blank" class="answer" href="<?=$ad->get_url()?>#N<?=$message->id?>">Ответить</a></p>
																	</article>										
													<?php		endif; ?>
													<?php endforeach; ?>										



			<!--										<article class="msg moderator msg-hide">
														<a href=""><span class="date">13,02,2013</span></a>
														<p class="autor">Модератор</p>
														<p>Ваше объявление заблокировано, вы можете его исправить, но осторожнее у вас осталась последняя попытка</p>
													</article>
													<article class="msg moderator msg-hide last">
														<a href=""><span class="date">11,02,2013</span></a>
														<p class="autor">Модератор</p>
														<p>Ваше объявление заблокировано, вы можете его исправить, но осторожнее у вас осталась последняя попытка</p>
													</article>-->

													<!--<p><a href="" class="more">посмотреть все</a></p>-->
												</div>
											</div>	   
								<?php endif; ?>
							</div>
			
							<div class="right-bl col5">
								<div class="pmenu ">
									<ul>
										<?php if (!$ad->is_bad and !$ad->in_archive and $ad->is_published) : ?>
													<? if ($premium_balance > 0): ?>
														<li>
															<a title="Премиум" href="" class="btn-pmenu " id="premium-btn<?=$ad->id?>" 
																		onclick="premium(<?=$ad->id?>, this); return false;"
																			data-url="<?=CI::site('billing/pay_service/'.$service_premium->id.'/'.$ad->id)?>">
																			<i class="ico premium"></i><span>Премиум (free)</span>
															</a>
														</li>
													<? else: ?>
														<li><a title="Премиум" href="<?=CI::site('billing/pay_service/'.$service_premium->id.'/'.$ad->id)?>" class="btn-pmenu ">
															<i class="ico premium"></i><span>Премиум</span>
														</a></li>
													<? endif; ?>
													<li><a title="Бегущая строка на сайте" href="<?=CI::site('billing/pay_service/'.$running_line_site_s->id.'/'.$ad->id)?>" class="btn-pmenu "><i class="ico rs"></i><span>Бегущая строка</span></a></li>																																		
													<li><a title="Текстовая ссылка" href="<?=CI::site('billing/pay_service/'.$service_promo_link->id.'/'.$ad->id)?>" class="btn-pmenu "><i class="ico pl"></i><span>Текст. ссылка</span></a></li>
													<li><a title="Графическая ссылка" href="<?=CI::site('billing/pay_service/'.$service_promo_link_bg->id.'/'.$ad->id)?>" class="btn-pmenu "><i class="ico plg"></i><span>Графич. ссылка</span></a></li>													
													<li><a title="Воспользоваться услугами" href="<?=CI::site('billing/services_for_ads/'.$ad->id)?>" class="btn-pmenu "><i class="ico services"></i><span>Пакет услуг</span></a></li>													
										<?php endif; ?>
									</ul>
								</div>
<!--								<div class="">
									<div class="pmenu">
										<ul>
											<?php if (!$ad->is_bad and !$ad->in_archive and $ad->is_published) : ?>
													<li><a title="Выделить объявление" href="<?=CI::site('billing/services_for_ads/'.$ad->id)?>" class="btn-pmenu "><i class="ico show"></i><span>Выделить</span></a></li>
											<?php endif; ?>
										</ul>
									</div>																		
								</div>-->
							</div>
						</div>				
								
				
				
				
				
				
				
				
				
				
				
				
				
				
                 
                <?php endforeach?>
            </div>
            </div>

            <?=$pagination?>
			<div class="clear"></div>
			<br />

            </section>
            </div>

			
		
        </section>
    </div><!--end content winner-->


