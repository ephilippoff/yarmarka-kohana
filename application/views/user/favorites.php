<div class="winner">
	<section class="main-cont">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
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
				<div class="p_cont myadd myaddfav">
					<div class="nav">
						<div class="input style2">			                    			
							<div class="inp-cont-bl ">
								<div class="inp-cont">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="checkbox" value="checkbox"><span>Выделить все</span>
										</label>	                    						
									
										<label class="no-box">
											<input type="checkbox" name="checkbox" value="checkbox"><span>Удалить выделенное</span>
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>
					<header>
						<div class="col1"><span>Номер</span></div>
						<div class="col5"><span>Удалить</span></div>
						<div class="col4"><span>Статус</span></div>
						<div class="col31"><span>Функции</span></div>
						<div class="col3"><span class="arr_down pointer">Дата</span></div>
						<div class="col31"><span>Город</span></div>
						<div class="col2"><span>Наименование</span></div>	
					</header>
					<div class="cont ">   

					<?php foreach ($objects as $object) : ?>
					<div class="li">
						<div class="left-bl">
							<div class="top-bl">
								<div class="col1">
									<div class="input">			                    			
										<div class="inp-cont-bl ">
											<div class="inp-cont">
												<div class="checkbox">
													<label>
														<input type="checkbox" name="checkbox" value="checkbox">
													</label>
												</div>
											</div>
										</div>
									</div>

                                    <div class="img hide-cont">
                                        <?php if ($object->main_image_filename) : ?>
											<?php list($width, $height) = Uploads::get_optimized_file_sizes($object->main_image_filename, '120x90', '106x106') ?>
                                            <img src="<?=Uploads::get_file_path($object->main_image_filename, '120x90')?>" 
											width="<?=$width?>" height="<?=$height?>"
											title="<?=$object->main_image_title ?>">
                                        <?php else : ?>
                                            <img src="<?=URL::site('images/photo/no-photo.jpg')?>" width="80" height="80" alt="photo">
                                        <?php endif; ?>
                                    </div>
									<p class="number">#<?=$object->id?></p>
								</div>			                    				
								<div class="col4">
									<?php if ($object->is_active()) : ?>
									<p class="istatus act"><span>Активно</span></p>
									<?php elseif($object->in_archive()) : ?>
									<p class="istatus archive"><span>В архиве</span></p>
									<?php elseif( ! $object->is_published()) : ?>
									<p class="istatus idel"><span>Удалено</span></p>
									<?php elseif( ! $object->is_banned()) : ?>
									<p class="istatus idel"><span>Заблокировано</span></p>
									<?php endif; ?>
								</div>
                                <div class="col31">
									<a href="<?=$object->get_url()?>" target="_blank" class="btn-funcmenu">
										<i class="ico info"></i><span>Просмотр</span>
									</a>
								</div>
								<div class="col3"><span class="date"><?=$object->get_real_date_created()?></span></div>
								<div class="col31"><span class="city"><?=$object->city_obj->loaded() ? $object->city_obj->title : $object->city?></span></div>
								<div class="col2">
									<p class="title"><?=$object->title?></p>
									
									<div class="hide-cont">
										<p class="info"><?=$object->category_obj->title?></p>
										<p class="info"><?=$object->city_obj->title?></p>
										<p class="about"><?=$object->full_text?></p>
									</div>
								</div>
							</div>	                    					
						</div>
						<div class="right-bl col5">
							<div class="pmenu">
								<ul>
									<li><a href="" data-id="<?=$object->id?>" class="btn-favorite"></a></li>
								</ul>
							</div>
							
						</div>
					</div>	
					<?php endforeach; ?>

					</div>
				</div>

				<?=$pagination?>
				<div class="clear"></div>
				<br />
				
			</section>
		</div>	   
		  
	</section>
</div><!--end content winner-->
