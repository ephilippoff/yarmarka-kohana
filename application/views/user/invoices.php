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
									<select class="iselect " name="" id="s3">
										<option value="1">Регион1</option>
										<option value="2">Регион2</option>
										<option value="3">Какой-то большой регион</option>
										<option value="4">Регион1</option>
										<option value="5">Регион2</option>
										<option value="6">Какой-то большой регион</option>
										<option value="7">Регион1</option>
										<option value="8">Регион2</option>
										<option value="9">Какой-то большой регион</option>
										<option value="10">Регион1</option>
										<option value="11">Регион2</option>
										<option value="12">Какой-то большой регион</option>
										<option value="13">Регион1</option>
										<option value="14">Регион2</option>
										<option value="15">Какой-то большой регион</option>
									</select>
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
								<div class="col3"><span class="date"><?=date('d.m.Y', strtotime($invoice->payment_date))?></span></div>
								<div class="col4">
									<p class="istatus "><span><?=$invoice->get_status_text()?></span></p>
								</div>				                    				
								<div class="col2">
									<p class="title"><?=$invoice->description?></p>
								</div>				                    				
							</div>			                    					                   					
						</div>		                    				
						<div class="hide-cont deteil-bl">
							<div class="title ">Детализация счета:</div>
							<ul>
								<li><span class="sum"><?=Num::price($invoice->sum)?> р.</span><span class="text"><?=$invoice->description?></span></li>
							</ul>
							<a href="" class="btn-pmenu">Перезаказать</a><a href="" class="btn-funcmenu">Свернуть</a>
						</div>
					</div>
					<?php endforeach; ?>
					</div>
				</div>
			</section>
		</div>	   
		  
	</section>
</div><!--end content winner-->
