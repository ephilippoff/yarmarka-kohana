<script type="text/javascript">
	function favorites(obj_id) 
	{
		$.post('/ajax/set_obj_favorite_status', {obj_id:obj_id}, function(data) {
			if(data.status == "added") {
				$('.fn-favor-text').text('Удалить из избранного');
			}
			if(data.status == "deleted") {
				$('.fn-favor-text').text('Добавить в избранное');
			}
		}, 'json');

	}	
	
	
$(document).ready(function() {	
	
	$('.fn-show-cont-bl').click(function() {
		var obj = this;
		var contact_table = $(this).closest('.fn-contact-bl').find('.fn-contact-bl-info');

		$.post('/ajax/object_contacts/', {id: $(this).data('id')}, function(data){
			$(data).insertAfter(contact_table);
			contact_table.remove();
			$(obj).hide()
		});
	});		
	
})	
</script>	

<div class="m_content inner-page main89">
    <div class="winner">
        <section class="main-cont">
            <div class="m_poll">
                <div class="fixshadow"></div>

                <section class="main-section" itemscope itemtype="http://schema.org/ItemPage">                    
                    <div class="crumbs-bl">
                        <div class="crumbs" itemprop="breadcrumb">
                            <span class="bnt-go-back span-link" href=""
                               onclick="window.history.back(); event.preventDefault();">
                                <i class="ico"></i><span class="text">Вернуться</span>
                            </span>
							<div class="cont">
								<?php if(isset($BreadScrumbs)): //TODO?>
									<?=$BreadScrumbs?>
								<?php endif?>
							</div>
                        </div>
                    </div>
					
					<?php if (!$data->object['is_published'] or $data->object['in_archive'] === 't') : ?>							 
								<div class="iPage-alert-bl error w100p">
									<div class="cont">
										<div class="img"></div>
										<p class="text">
											Обращаем ваше внимание, что данное объявление было снято или находится на сайте больше двух недель, контакты объявления скрыты. Вы всегда можете поискать подобные объявления в поиске или в наших рубриках.
										</p>
									</div>
								</div>	
					<?php endif; ?>	
					
                    <section class="personal-card-bl">

                        <div class="personal-card">

							
					<?php echo View::factory('detail/aside_group_type89')->bind('data', $data) ?>						
							
							
                            <div class="cont">

                                <article class="article" itemscope itemtype="http://schema.org/Product">
                                    <?php if(isset($data->object)):?>
											<?php //if (isset($_GET['reklama'])) : ?>
<!--												<div class="bmreklama">
													<img src="/images/bmreklama.png">
												</div>-->
											<?php //endif; ?>
                                        <div class="topinfo">
                                            <header>
                                                <h1><?=htmlspecialchars($data->object['title'])?></h1>
												&nbsp;<?php if (!$data->object['is_published'] or $data->object['in_archive'] === 't') : ?><!--noindex--><span class="red status">(Объявления снято или в архиве)</span><!--/noindex--><?php endif; ?>
                                                <meta itemprop="dateCreated" content="<?php echo date("Y-m-d h:m:s", strtotime($data->object['date_created'])) ?>" />
                                            </header>	
											
                                        </div>									
                                        

                                                                                                                  
                                        <?php
											$img_count = isset($data->images['other']) ? count($data->images['other']) : 0;
											//Переворачиваем для соблюдения порядка загрузки
											//$data->images['other'] = array_reverse($data->images['other']);										
                                        ?>
                                        
										<div class="fl w100p">
											
										<?php if (isset($data->images['main'])) : ?>
													<div class="box photo-bl">
														<div class="img-cont">
															<div class="center-img">
																<img src="<?=trim($data->images['main']['original'], '.')?>" />
															</div>
														</div>	
													</div>
										<?php endif;?>
													
										<?php if (!empty($data->object['user_text']) or $priceload or !empty($site) ) : ?>	
												<div class="text mt20 fl w100p" itemprop="description">
													<?=$data->object['user_text']?>
													<?php //if (is_file($_SERVER['DOCUMENT_ROOT'].trim($priceload->filepath_original, '.'))) : ?>
															<p class="mt20"><b>Прайс-лист</b>: <a href="<?//=trim($priceload->filepath_original, '.')?>"><?//=$priceload->title?></a></p>
													<?php //endif;?>
													
													<?php if (!empty($site)) : ?>
														<p class="mt10"><b>Адрес сайта</b>: <span class="span-link" onclick="window.open('http://<?=$site?>', '_blank')"><?=trim($site, '/')?></span></p> 															
													<?php endif;?>
												</div>	
										<?php endif;?>
												
										<div class="fl w100p mt20">
											<?php if (count($data->pricerows)): ?>
	                                          <?php  View::factory('blocks/detail/pricerows')?>
	                                        <?php endif; ?>
                                        </div>												
												
										<?php if ($data->object['geo_loc'] && $data->object['category_obj']['show_map']) : ?>																																															

											<div class="act-center mt20" style="width:100%;">
												<p class="title ">Карта</p>

												<?php if ($data->location) : ?>
													<p class="location">
														<?=$data->location['region']?>, <?=$data->location['city']?>
														<?php if ($data->location['address']) : ?>
															, <?=$data->location['address']?>
														<?php endif ?>
													</p>
												<?php else : ?>
													<p class="location"><?=$data->object['city'];?></p>
												<?php endif ?>			

												<div class="map-bl">													
													<script src="http://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU&coordorder=latlong" type="text/javascript"></script>
													<script type="text/javascript">
														var map;
														var placemark;

														ymaps.ready(function(){
															map = new ymaps.Map("card-map", {
																center: [<?=$data->object['geo_loc']?>],
																zoom: 14
															});

															map.controls.add('zoomControl', { top: 5, left: 5 });
															map.controls.add(new ymaps.control.TypeSelector(['yandex#map', 'yandex#satellite', 'yandex#hybrid', 'yandex#publicMap', 'yandex#publicMapHybrid']));

															placemark = new ymaps.Placemark([<?=$data->object['geo_loc']?>], {
																},
																{
																	draggable: false,
																	iconImageHref: '/images/map_mark_active.png',
																	iconImageSize: [47,47],
																	iconImageOffset: [-15, -45],

																	iconContentOffset: [],
																	hintHideTimeout: 0

																});

															map.geoObjects.add(placemark);
														});

													</script>

													<div id="card-map"  style="width:100%; height:300px">
													</div>
												</div>
											</div>
										<?php endif?>											
											
											
										<?php if ($img_count /*OR !empty($VideosObject)*/) : ?>
                                                                                            												
												<div class="box photo-bl mt20">
                                                    
                                                        <?php 
															foreach($data->images['other'] as $key => $a) : ?>
																<div class="img-cont">
																	<div class="center-img">
																		<img src="<?=trim($a['original'], '.')?>" />
																	</div>
																</div>
                                                        <?php 
															endforeach; ?>
													
														<?php //if ($embed_video) : ?>
<!--																<div class="img-cont" id="video">
																	<div class="center-img">
																		<a name="video"  rel="nofollow">
																			<?//=$embed_video?>
																		</a>
																	</div>
																</div>																															-->
														<?php //endif;?>													
													
                                                </div>
                                            
                                        <?php endif; ?>
									
										</div>
									
									
										<div class="shadow-top">            
											<div class="like">
												<p class="line">Поделиться с друзьями</p>
												<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
												<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,lj,friendfeed,moikrug,gplus"></div>           
											</div>
										</div>

                                    <?php else : ?>
											<p>Такого объявления не существует. Попробуйте воспользоваться поиском.</p>
                                    <?php endif; ?>

                                </article>								
                            </div>                            
                        </div>
                    </section>



                </section><!-- main-section -->
            </div>
        </section><!-- main-cont -->
    </div>
</div>
