<div class="winner page-addobj">
	<section class="main-cont subscriptions cabinet">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет - <?=$name?></h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">				
				<header><span class="title">
						<?=$name?>
				</span>
				</header>
				<div class="p_cont">
					<div class="p10" style="font-size:14px; padding:20px">
						<p class="p10" style="width:400px">Вы не можете просмотреть этот раздел т.к. Ваша учетная запись является подчиненной 
							для компании '<?=$company->org_name?>' (<?=$company->email?>)</p>
						<p class="p10" style="width:400px">Отменить привязку учетной записи к другой компании вы можете, если перейдете по <a href="/user/reset_parent">ссылке</a>

						</p>
					</div>
				</div>
				<div class="clear"></div>
				
			</section>
		</div>	   
		  
	</section>
</div><!--end content winner-->