<div class="winner">
	<section class="main-cont subscriptions cabinet">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">
				<header><span class="title">Массовая загрузка объявлений</span></header>
				<div class="p_cont massload">
					<? if (count($categories)>0): ?>
						<div id="fn-main-cont" class="cont">
							<div class="massload-controlsrow">
								<div class="massload-category">
									<select id="fn-category">
										<option value>--</option>
										<? foreach($categories as $key=>$value): ?>
											<option value="<?=$key?>"><?=$value?></option>
										<? endforeach; ?>
									</select>

								</div>
								<div class=" massload-button-load" id="fn-userfile-upload">
									<div class="button blue">
										<span>Загрузить</span>
									</div>
									
								</div>								
							</div>
							<div class="massload-controlsrow">
								<div class="massload-hint">
									Формат загружаемых файлов: *.csv, *.zip;</br>
									При включенном флаге "Игнорировать ошибки" сохранятся все объявления, в которых не обнаружены ошибки, остальные будут проигнорирвоаны.</br>
									Подробнее : <a href="#">Помощь по массовой загрузке объявлений</a>
								</div>
							</div>
							<div class="massload-controlsrow massload-checkbox-ingnore-errors">
								<input type="checkbox" id="fn-ignore_errors"/>
								<label for="fn-ignore_errors">Игнорировать ошибки
								</label>
							</div>
							<? /*<input id="fn-userfile-upload" type="button" name="button" value="Загрузить"/> */?>
							<div class="massload-controlsrow massload-textarea">
								<p id="fn-log-area"></p>
							</div>	
							<div class="massload-controlsrow massload-conformities">
								<ul>
									<?=Request::factory('block/massload_categories')->execute()?>
								</ul>
							</div>						
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
