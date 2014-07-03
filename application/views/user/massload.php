<div class="winner">
	<section class="main-cont subscriptions cabinet">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">
				<header><span class="title">Массовая загрузка объявлений</span></header>
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
					<div id="fn-main-cont" class="cont">
						<select id="fn-category">
							<option>--</option>
							<option value="3" selected>Недвижимость</option>
							<option value="15">Легковые автомобили</option>
						</select>
						<br/>
						<input id="fn-userfile-upload" type="button" name="button" value="Загрузить"/>
						
						<br/>
						<textarea id="fn-log-area" style="border:1px solid black;width:500px; height:300px;" readonly="readonly"></textarea>
					</div>
				</div>
				<div class="clear"></div>
				<br />
				
			</section>
		</div>	   
		  
	</section>
</div><!--end content winner-->
