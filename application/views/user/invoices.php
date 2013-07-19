<div class="winner">
	<section class="main-cont">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">
				<header>
					<span class="title ">Счета</span>
					<div class="filter">
						<span class="tit">Фильтровать:</span>
						<div class="input style3">
							<div class="inp-cont-bl ">
								<div class="inp-cont">
									<i class="imp320 imp">&nbsp; *</i>
									<form method="get" id="filter_form">
									<select class="iselect " name="status" id="status">
										<option value="">Все</option>
										<option value="success" <?=Arr::get($_GET, 'status') == 'success' ? 'selected' : ''?>>Оплачен</option>
										<option value="created" <?=Arr::get($_GET, 'status') == 'created' ? 'selected' : ''?>>В ожидании оплаты</option>
										<option value="refused" <?=Arr::get($_GET, 'status') == 'refused' ? 'selected' : ''?>>Отменен</option>
									</select>
									</form>
								</div>
							</div>
						</div>
					</div>
				</header>
				<div class="p_cont myadd myacc">
					<header>
						<div class="col1"><span>Дата</span></div>
						<div class="col col12"><span>Номер счета</span></div>
						<div class="col5"><span>Сумма</span></div>
						
						<div class="col3"><span>Дата оплаты</span></div>
						<div class="col4"><span>Статус</span></div>
						<div class="col2"><span>Описание</span></div>	
					</header>
					<div class="cont ">   
					<?php foreach ($invoices as $invoice) : ?>
					<div class="li">
						<div class="left-bl">
							<div class="top-bl">
								<div class="col1">
									<p class="date"><?=date('d.m.Y', strtotime($invoice->created_on))?></p>
								</div>
								<div class="col col12">
									<p class="number"><?=$invoice->id?></p>
								</div>	
								<div class="right-bl col5">
									<div class="pmenu">
										<ul>
											<li><span><?=Num::price($invoice->sum)?> р.</span></li>
										</ul>
									</div>		                    					
								</div> 				                    				
								<div class="col3"><span class="date">
								<?php if ($invoice->payment_date) : ?>
									<?=date('d.m.Y', strtotime($invoice->payment_date))?>
								<?php endif; ?>
								</span></div>
								<div class="col4">
									<?php if ($invoice->is_refused()) : ?>
									<p class="istatus idel">
									<?php elseif ($invoice->is_success()) : ?>
									<p class="istatus act">
									<?php else : ?>
									<p class="istatus archive">
									<?php endif; ?>
										<span><?=$invoice->get_status_text()?></span>
									</p>
								</div>				                    				
								<div class="col2">
									<p class="title"><?=$invoice->description?></p>
								</div>				                    				
							</div>			                    					                   					
						</div>		                    				
						<div class="hide-cont deteil-bl">
							<div class="title ">Детализация счета:</div>
							<ul>
								<?php foreach ($invoice->services->find_all() as $service) : ?>
								<li>
									<span class="sum"><?=Num::price($service->sum)?> р.</span>
									(<?=$service->count?>)
									<span class="read">
									<?php if ($service->graph) : ?>
											<a href="<?=CI::site(Search::get_url_to_main_category())?>?source_id=<?=intval($service->graph)?>" class="btn-pmenu">Читать</a>
									<?php elseif ($service->object_id) : ?>
											<a href="<?=$service->object->get_url()?>" class="btn-pmenu">Перейти</a>
									<?php endif; ?>
									</span>
									<span class="text"><?=$service->service_name?></span>
								</li>
								<?php endforeach; ?>
							</ul>
							<?php if (FALSE) : ?>
							<a href="" class="btn-pmenu">Перезаказать</a><a href="" class="btn-funcmenu">Свернуть</a>
							<?php endif; ?>
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
