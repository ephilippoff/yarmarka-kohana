<div class="winner">
	<section class="main-cont subscriptions cabinet">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">
				<header><span class="title">Настройка соответствий для массовой загрузки объявлений</span></header>
				<div class="p_cont myadd mysub">
					<? if (count($categories)>0): ?>
						<div id="fn-main-cont" class="cont">
							<select id="fn-category">
								<option value>--</option>
								<? foreach($categories as $key=>$value): ?>
									<option value="<?=$key?>"><?=$value?></option>
								<? endforeach; ?>
							</select>
							
						</div>
					<? else: ?>
						Услуга массовой загрузки не подключена.
					<? endif; ?>
				</div>
				<div class="clear"></div>
				<br />
				
			</section>
		</div>	   
		  
	</section>
</div><!--end content winner-->
