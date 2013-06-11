<div class="m_content">
    <div class="winner">
        <section class="main-cont">
        <div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет &rarr; Мои объявления</h1></div>
        <div class="fl100 shadow-top z1 persomal_room">

			<?=View::factory('user/_left_menu')?>

            <section class="p_room-inner">
				<header>
					<form method="get" id="ads_filter">
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
					<div class="input style3">
						<div class="inp-cont-bl ">
							<div class="inp-cont">
								<i class="imp320 imp">&nbsp; *</i>
								<select class="iselect " name="city_id" id="city_id">
									<option value="">--  выберите регион --</option>
									<?php foreach ($cities as $city) : ?>
									<option value="<?=$city->id?>" <?=Arr::get($_GET, 'city_id') == $city->id ? 'selected' : ''?>><?=$city->title?></option>
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
									<input class="" type="text" name="text" value="<?=Arr::get($_GET, 'text')?>" placeholder="Искать объявления по содержимому">
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
            </div>
            <header>
                <div class="col1"><span>Фото/номер</span></div>
                <?php if (FALSE) : ?><div class="col5"><span>Премиум</span></div><?php endif; ?>
                <div class="col4"><span>Функции</span></div>
                <div class="col3"><span>Статус</span></div>
                <div class="col2"><span>Название</span></div>
            </header>
            <div class="cont ">
                <?php foreach ($objects as $ad) : ?>
                    <div class="li">
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
                                    <div class="img hide-cont">
                                        <?php if (FALSE) : ?>
                                            <img src="" alt="photo" title="<?=$ad->imagetitle ?>">
                                        <?php else : ?>
                                            <img src="<?=URL::site('images/photo/no-photo.jpg')?>" width="80" height="80" alt="photo">
                                        <?php endif; ?>
                                    </div>

                                    <p class="number">#<?=$ad->id?></p>
                                </div>
                                <div class="col4">
                                    <ul>
                                        <li class="show-cont"><a href="" class="btn-funcmenu toggle"><i class="ico clock"></i><span>Действия</span></a></li>


                                        <?php if (!$ad->is_bad) {
                                        if ($ad->in_archive) //в архиве
                                        { ?>

                                            <li class="hide-cont">
                                                <select class="plolong-slc" id="prolong_<?=$ad->id ?>">
                                                    <option value="2w">на 2 недели</option>
                                                    <option selected value="1m">на 1 месяц</option>
                                                    <option value="2m">на 2 месяца</option>
                                                    <option value="3m">на 3 месяца</option>
                                                </select>
                                            </li>

                                            <li class="hide-cont">
                                                <a href="" class="btn-funcmenu" id="prolong-btn<?=$ad->id?>" onclick="prolong(<?=$ad->id?>); return false;">
                                                    <i class="ico clock"></i><span>Продлить</span>
                                                </a>
                                            </li>

                                            <?  }
                                        else //не в архиве
                                        {
                                            ?>


                                            <?php // Получаем дату, когда можно поднять объявление
                                            if ($ad->get_service_up_timestamp() < time()) : ?>
                                                <li class="hide-cont">
                                                    <a href="" class="btn-funcmenu" id="service-up-<?=$ad->id?>" onClick="service_up(<?=$ad->id?>); return false;">
                                                        <i class="ico clock"></i><span>Поднять</span>
                                                    </a>
                                                </li>
                                            <?php else : ?>
                                                <li class="hide-cont">
                                                    <a href="" class="btn-funcmenu noactive" id="service-up-<?=$ad->id?>" onClick="service_up(<?=$ad->id?>); return false;"
                                                       title="Вы можете поднять это объявление не раньше <?=date("d.m Y в H:i", $ad->get_service_up_timestamp())?>"
                                                       onclick="return false;">
                                                        <i class="ico clock"></i><span>Поднять</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>


                                            <li class="hide-cont">
                                                <a href="<?=$ad->get_url()?>" target="_blank" class="btn-funcmenu">
                                                    <i class="ico clock"></i><span>Просмотр</span>
                                                </a>
                                            </li>

                                            <li class="hide-cont">
                                                <a href="" class="btn-funcmenu" onclick="window.location='/user/edit_ad/<?=$ad->id?>'; return false;">
                                                    <i class="ico clock"></i><span>Изменить</span>
                                                </a>
                                            </li>

                                            <?php if ($ad->is_published) : ?>
                                                <li class="hide-cont">
                                                    <a href="" class="btn-funcmenu" id="pub_toggle_link_<?=$ad->id?>" onclick="pub_toggle(<?=$ad->id?>); return false;">
                                                        <i class="ico clock"></i><span>Снять</span>
                                                    </a>
                                                </li>
                                            <?php else : ?>
                                                <li class="hide-cont">
                                                    <a href="" class="btn-funcmenu" id="pub_toggle_link_<?=$ad->id?>" onclick="pub_toggle(<?=$ad->id?>); return false;">
                                                        <i class="ico clock"></i><span>Разместить</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>


                                            <li class="hide-cont">
                                                <a href="" class="btn-funcmenu" onclick="delete_ad(<?php echo $ad->id ?>, this); return false;" class="btn btn-lc active">
                                                    <i class="ico clock"></i><span>Удалить</span>
                                                </a>
                                            </li>


                                            <? }
                                            }
                                            else //есть блок
                                            {  ?>

                                                <?php if ($ad->is_bad == 1 AND $ad->in_archive) : //блок1 и архив ?>

                                                <li class="hide-cont">
                                                    <select class="plolong-slc" id="prolong_<?=$ad->id?>">
                                                        <option value="2w">на 2 недели</option>
                                                        <option selected value="1m">на 1 месяц</option>
                                                        <option value="2m">на 2 месяца</option>
                                                        <option value="3m">на 3 месяца</option>
                                                    </select>
                                                </li>

                                                <li class="hide-cont">
                                                    <a href="" class="btn-funcmenu" onclick="prolong(<?=$ad->id?>); return false;">
                                                        <i class="ico clock"></i><span>Исправить и продлить</span>
                                                    </a>
                                                </li>

                                                <?php elseif ($ad->is_bad == 1 AND ! $ad->in_archive) : ?>

                                                <li class="hide-cont">
                                                    <a href="" class="btn-funcmenu" onclick="fix_ad(<?=$ad->id?>); return false;">
                                                        <i class="ico clock"></i><span>Исправить</span>
                                                    </a>
                                                </li>

                                                <?php endif; ?>

                                                <? } ?>
                                    </ul>
                                    <div id="status<?=$ad->id?>"></div>
                                </div>
                                <div class="col3">
                                    <span class="date">
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
                                    </span>
                                </div>
                                <div class="col2">
                                    <p class="title"><?=htmlspecialchars(mb_substr($ad->title, 0, 50))?> (<a href="<?=$ad->get_url()?>" target="_blank">Перейти</a>)</p>
                                    <div class="hide-cont">
                                        <p class="info"><?=$ad->category_obj->title?></p>
                                        <p class="info"><?=$ad->city_obj->loaded() ? $ad->city_obj->title : $ad->city?></p>
                                        <p class="about"><?=$ad->user_text?></p>
                                        <p class="info">Количество просмотров: <?=(int)$ad->visits?></p>
                                        <p class="date"><?=date('d.m.Y', strtotime($ad->date_created))?></p>
                                    </div>
                                </div>
                            </div>

                            <? // @todo show moderator comments ?>

                        </div>
						<?php if (FALSE) : ?>
                        <div class="right-bl col5">
                            <div class="pmenu show-cont">
                                <ul>
                                    <li><a href="" class="btn-pmenu"><i class="ico info"></i><span>Премиум</span></a></li>
                                </ul>
                            </div>
                            <div class="hide-cont">
                                <div class="pmenu">
                                    <ul>

                                        <?php if ($ad->is_bad == 0 AND ! $ad->in_archive) : ?>
                                            <li>
                                                <a href="" class="btn-pmenu" onclick="view_uslugi(this)" id="uslugi_<?=$ad->id?>">
                                                    <i class="ico info"></i><span>В газету</span>
                                                </a>
                                            </li>
                                        <?php else : ?>

                                            <li>
                                                <a href="" class="btn-pmenu noactive" id="uslugi_<?=$ad->id?>"">
                                                    <i class="ico info"></i><span>В газету</span>
                                                </a>
                                            </li>
                                        <?php endif?>

                                    </ul>
                                </div>
                                <div class="hr"></div>
                                <a href="" class="complex"></a>
                            </div>
                        </div>
						<?php endif; ?>
                    </div>
                <?php endforeach?>
            </div>
            </div>

            <p class="more"><?=$pagination?></p>

            </section>
            </div>

        </section>
    </div><!--end content winner-->
</div>
