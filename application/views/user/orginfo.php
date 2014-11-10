<div class="winner page-addobj">
	<section class="main-cont subscriptions cabinet">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет - Информация о компании</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">				
				<header><span class="title">
						Информация о компании
				</span>
				</header>
				<div class="p_cont">

					<form method="POST" id="orginfo" enctype="multipart/form-data">
						<div class="fl100  pt16 pb15">
							<? foreach ($form as $field): ?>
								<div class="smallcont">
								<div class="labelcont">
									<label><span><?=$field["title"]?></span></label>
								</div>
								<div class="fieldscont">
									<?
										$lenth_unput = "inp-cont-short";
										if ($field["type"] == "long")
											$lenth_unput = "inp-cont-long";
									?>
									<div class="<?=$lenth_unput?>">
										<div class="inp-cont <? if ($errors->{$field["name"]}) echo "error";?>">
											<? if ($field["required"]):?>
												<span class="required-label">*</span>
								    		<? endif; ?>
								    		
								    		<? if ($field["type"] == "photo"): ?>
								    			<?=$field["html"]?>
								    			<? if ($field["path"]):?>
								    				<img src="<?=$field["path"]?>" style="padding:10px;">
								    			<? endif;?>
								    		<? else: ?>
								    			<?=$field["html"]?>
								    		<? endif; ?>
								    		<? if ($errors->{$field["name"]}): ?>
												<span class="inform fn-error">
													<span><?=$errors->{$field["name"]}?></span>
												</span>
											<? endif; ?>

								  		</div>
									</div>
								</div>									
							</div>
							<? endforeach; ?>
						</div>
						<div class="fl100 form-next-cont">
							<div class="smallcont">
								<div class="labelcont"></div>	
								<div class="fieldscont ta-r mb15">						
									<div onclick="$('#orginfo').submit()" class="button blue icon-arrow-r btn-next"><span>Далее</span></div>							
								</div><!--fieldscont-->
							</div><!--smallcont-->	
						</div>
					</form>

				</div>
				<div class="clear"></div>
				<br />
				
			</section>
		</div>	   
		  
	</section>
</div><!--end content winner-->