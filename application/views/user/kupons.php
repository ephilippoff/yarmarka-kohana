<div class="winner">
	<section class="main-cont kupons-main-cont invoices cabinet">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">
				<header>
					<span class="title ">Купоны</span>
				</header>
				<div class="p_cont myadd myacc">
					<header>
						<div class="col col12"><span>Номер купона</span></div>
						<div class="col2"><span>Заголовок</span></div>	
					</header>
					<div class="cont ">   
					<?php foreach ($kupons as $kupon) : ?>
					<div class="li">
						<div class="left-bl">
							<div class="top-bl">
								<div class="col col12">
									<p class="number">
										<?=$kupon->id?>
									</p>
								</div>
								<div class="col2">
									<p class="kupon-title">
										<a target="_blank" href="<?=Url::site('kupon_test/index/'.$kupon->object_id)?>"><?=$kupon->object_title?></a>
									</p>
								</div>								
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
