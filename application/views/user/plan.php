<div class="winner">
	<section class="main-cont subscriptions cabinet">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">
				<header><span class="title">Тарифные планы</span></header>
				<div class="p_cont myadd mysub">
					
					<div id="fn-main-cont" class="cont">
						<? if (count($services)>0): ?>
						<div>
							Вы можете купить следющие тарифные планы:<br/>
							<? foreach($services as $service):?>
								<a href="<?=CI::site()."billing/pay_service/".$service->id ?>">Купить <?=$service->title?></a> - <?=$service->description?></br>
							<? endforeach;?>
						</div>
						<?  else: ?>
							Активация тарифного плана не доступна.
						<? endif; ?>
						<?=Request::factory('block/plan_info')->execute()?>
					</div>
				</div>
				<div class="clear"></div>
				<br />
				
			</section>
		</div>	   
		  
	</section>
</div><!--end content winner-->
