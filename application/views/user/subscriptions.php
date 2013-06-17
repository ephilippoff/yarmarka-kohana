<div class="winner">
	<section class="main-cont">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">
				<header><span class="title">Подписки на обновления «Ярмарка»</span></header>
				<div class="p_cont myadd mysub">
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
					<div class="cont ">   
						<?php foreach ($subscriptions as $subscription) : ?>
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
										<p class="date"></p>
									</div>				                    				
									<div class="col3"><span class="city">Период: </span>
										<div class="period iLight"><span class="iLight-nav"><?=$subscription->get_period()?></span>
											<ul class="iLight-cont">
												<li data-id="<?=$subscription->id?>" data-period="24">1 день</li>
												<li data-id="<?=$subscription->id?>" data-period="12">12 часов</li>
												<li data-id="<?=$subscription->id?>" data-period="6">6 часов</li>
												<li data-id="<?=$subscription->id?>" data-period="3">3 часа</li>
												<li data-id="<?=$subscription->id?>" data-period="2">2 часа</li>
											</ul>
										</div>
									</div>
									<div class="col2">
										<p class="title"><?=$subscription->title?></p>
										
									</div>
								</div>	                    					
							</div>
							<div class="right-bl col5">
								<div class="pmenu">
									<ul>
										<li><a href="" data-id="<?=$subscription->id?>" class="btn-funcmenu unsubscribe">Отписаться</a></li>
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
