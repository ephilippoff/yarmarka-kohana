<? if ($already_agree): ?>
<form method="post">
	<input type="hidden" name="hidehelp" value="<?=$hidehelp?>">
	<? if ($hidehelp):?>
		<input type="submit" class="btn btn-info" value="Скрыть пошаговую инструкцию">
	<? else:?>
		<input type="submit" class="btn btn-info" value="Показать пошаговую инструкцию">
	<? endif;?>
</form>
<? endif;?>
</header>
<div class="p_cont massload">
	<? if (count($categories)>0): ?>
		<div id="fn-main-cont" class="cont">
			<? if (!$already_agree): ?>

				<p><h2>Зачем нужна массовая загрузка объявлений?</h2>
					<ul>
						<li>- Главное преимущество – это существенная экономия времени.</li>
						<li>- Управление объявлениями станет существенно удобней. Сравните время на изменение, к примеру, цен у 200 строк в excel-файле относительно ручного редактирования на сайте.</li>
						<li>- У вас есть база данных объектов/товаров/услуг, выгрузив из нее объявления в нужном формате и загрузив на наш сайт, вы потратите на это не более 10 минут.</li>
						<li>- У вас нет базы данных, но есть список объектов/товаров/услуг, который вы ведете в Excel. Наш сайт может стать хранилищем ваших предложений. Объявления можно не только загрузить, но и <a href="/user/objectunload">выгрузить</a></li>
						<li>- <a target="_blank" href="http://c.yarmarka.biz/news/3048-vnimanie-reklamodatelyam-novaya-usluga-massovaya-zagruzka-obyavlenii?em_client_email=noreply@yarmarka.biz&em_campaign_id=4&em_campaign_name=newsone_3048">Подробнее о массовой загрузке</a></li>
					</ul>
				</p>	
				</br>

				<p><h2>Подтвердите, что вы согласны с условиями, прежде чем начать</h2>
					<form method="POST">
					<ul>
						<li>- <span style="color:red;">Сервис работает в тестовом режиме.</span> Об ошибках в работе, а также ваши пожелания пишите в техподдержку</li>
						<li>- Объявления, поданные через массовую загрузку, модерируются по тем же правилам, что и поданные обычным способом</li>
						<li>- Качество объявлений и уникальность превыше всего. Будет хорошо, если ваши товары/услуги хорошо описаны, содержат уникальные фотографии, существуют на самом деле. В некоторых случаях возможно расширение бесплатного лимита строк.</li>
						<li>- Загрузка может быть отклонена модератором</li>
						<li>- Бесплатно вы можете загружать не более <?=$free_limit?> объявлений в одном файле и в одну рубрику</li>
						<li>- Если нет категории, в которую вы хотели бы массово загружать объявления, напишите в техподдержку. Укажите рубрику и примерное содержание объявлений. Сейчас загрузка доступна для следующих рубрик: 
							<? foreach($categories as $key=>$value): ?>
									"<?=$value?>",
							<? endforeach; ?>
						</li>
						<li>- Вам нужно заранее определиться, будете ли вы загружать объявления в рубрику через загрузку или же вручную. Совмещать эти два способа не получится. Объявления которых нет в файле в данной рубрике будут сниматься при каждой загрузке. Управлять же платными услугами для объявлений вы можете обычным способом в личном кабинете.</li>
						<li>- Интерфейс загрузки работает в современных браузерах, работа на устаревшем ПО не гарантируется.</li>
						<li>- Если вы не согласны хоть с одним из пунктов, не нажимайте на кнопку "Я согласен"</li>
						<li><input type="submit" class="btn btn-primary" value="Я согласен"></li>
					</ul>
					<input type="hidden" name="i_agree" value="1">
					</form>
				</p>	
				</br>
			<? endif; ?>
			<? if ($already_agree): ?>
				<? if ($hidehelp):?>
					<p>
						<h2>Шаг 1. Скачайте и заполните шаблон</h2>
						<div class="massload-controlsrow">
							<ul>
								<? foreach($categories as $key=>$value): ?>
									<li key="<?=$key?>"><a href="<?=$categories_templates[$key]?>" target="_blank"><?=$value?>.xls</a></li>
								<? endforeach; ?>
							</ul>
							<div class="massload-hint">	
								<ul>								
									<li>В файле должны остаться только, шапка, с названиями полей и строки с объявлениями</li>
									<li>Порядок и количество столбцов менять нельзя</li>
									<li>Значения полей должны быть единообразны. Т.е. если "Пансионат" то везде "Пансионат", а не "Панс.", "п" и проч.</li>
								</ul>
							</div>		
						</div>
					</p>
					<p><h2>Шаг 2. Настройте соответствия справочников, для рубрик в которые планируется загрузка</h2>
						<div class="massload-controlsrow">
							<ul>
								<? foreach($categories as $key=>$value): ?>
									<li key="<?=$key?>"><a href="/user/massload_conformities/<?=$key?>"><?=$value?></a></li>
								<? endforeach; ?>
							</ul>
							<div class="massload-hint">		
								<ul>							
									<li>В зависимости от того в какую рубрику будете загружать объявления, настройте соответствия.</li>
									<li>Например, если в вашем файле город обозначается как "Тюмень" то поставьте это значение напротив "г. Тюмень"</li>
								</ul>
							</div>		
						</div>
					</p>
				<? endif; ?>
			<p><h2><? if ($hidehelp):?> Шаг 3. <? endif; ?> Загрузите файл</h2>
			<div class="massload-controlsrow">
				<div class="massload-category">
					 <script type="text/javascript">
					 	$(document).ready(function() {
						 	var self = this;
						    new AjaxUpload('fn-userfile-upload', {
						            action: '/ajax/massload/save_userstaticfile',
						            name: 'file',
						            data : {context :self},
						            autoSubmit: true,
						            onSubmit: function(filename, response){
						            	var self = this._settings.data.context;
								        self.category_id = $("#fn-category").val();
								        this.setData({ context : self, category : self.category_id});
								        $(".aloader").show();
						            },
						            onComplete: function(filename, response){
						            	$(".aloader").hide();
						            	$(".staticfile_error").html("");
						            	$(".staticfile_success").html("");
						            	var data = null;
					       				var self = this._settings.data.context; 
					       				if (response) 
					            			data = $.parseJSON(response);
					            		if (data.error)
					            			$(".staticfile_error").html(data.error);
					            		if (data.objectload_id){
					            			$(".staticfile_success").html("Файл загружен");
					            			location.reload();
					            		}
					            		
						            }
						       });
						});

						function show_message(message){
							alert(message);
						}

						function delete_ol(id){
							if (!confirm("Удалить?")) {
								return;
							}
							$(".aloader").show();
							$.post( "/ajax/massload/objectload_delete", {id:id}, function( data ) {
							  	$('.ol_'+id).hide();
							  	$(".aloader").hide();
							});
						}

						function retest_ol(id){
							$(".aloader").show();
							$.post( "/ajax/massload/objectload_retest", {id:id}, function( data ) {
							  	console.log(data);
							  	$(".aloader").hide();
							  	location.reload();
							});
							
						}
					   </script>
					Выберите категорию:
					<select id="fn-category">
						<option value>--</option>
						<? foreach($categories as $key=>$value): ?>
							<option value="<?=$key?>"><?=$value?></option>
						<? endforeach; ?>
					</select>

				</div>
				<div class="button" id="fn-userfile-upload">
					<div class="button blue">
						<span>Загрузить</span>
					</div>
					
				</div>								
			</div>
			<div class="massload-controlsrow">
				<div class="aloader" style="display:none;">
					<img src="/images/aloader.gif">
				</div>
				<div class="massload-hint">
					<ul>
						<li>Формат загружаемых файлов: *.xls, *.xlsx;</li>
						<li>Бесплатно вы можете загружать не более <?=$free_limit?> объявлений в одном файле, в одну рубрику</li>
						<li>! Вам нужно заранее определиться, будете ли вы загружать объявления в рубрику через загрузку или же вручную. Совмещать эти два способа не получится. Объявления которых нет в загруженном файле в данной рубрике будут сниматься при каждой загрузке. Управлять же платными услугами для объявлений вы можете обычным способом в личном кабинете.</li>
					</ul>
				</div>
				<div class="staticfile_error" style="color:red;"></div>
				<div class="staticfile_success" style="color:green;"></div>

			</div>
			</p>
			<? endif; ?>
			<p><h2>Ваши загрузки</h2>
			<table class="table table-bordered table-striped">
				<tr>
					<th>#</th>
					<th style="width:100px;">Дата</th>
					<th style="width:150px;">Категория</th>
					<th>Статистика <br/>(нов/изм/неизм/ошибки(%)=все)</th>
					<th>Просмотр</th>
					<th>Состояние</th>
					<th></th>
				</tr>
				<?php foreach ($objectloads as $item) : ?>
					<tr id="ol_<?=$item->id?>" class="ol_<?=$item->id?>">			
						<td><?=$item->id?></td>
						<td><?=$item->created_on?></td>
						<td></td>	
						<td id="stat_<?=$item->id?>"><?=$item->statistic_str?></td>
						<td></td>
						<td id="ol_state_<?=$item->id?>">
							<?if (!$item->withcomment_state): ?>
								<?=$states[$item->state]?>
							<?else:?>
								<a style='color:red !important;' href='#' onclick="show_message('<?=$item->comment?>');return false;"><?=$states[$item->state]?> (?)</a>
							<?endif;?>
						</td>
						<td id="ol_button_<?=$item->id?>">
							<? if ($item->access_refresh): ?>
								<span class="icon-refresh" onclick="retest_ol(<?=$item->id?>);return false;" style="cursor:pointer;"></span>
							<? endif;?>
							<? if ($item->access_userdelete): ?>
								<span class="icon-trash" onclick="delete_ol(<?=$item->id?>);return false;" style="cursor:pointer;"></span>
							<? endif;?>
						</td>		
					</tr>
					<?php foreach ($item->objfiles as $file) : ?>
						<tr style="" class="ol_<?=$item->id?>">			
							<td></td>
							<td></td>
							<td><a target="_blank" href="/user/massload_conformities/<?=$file->category?>"><?=$config[$file->category]['name']?> (?)</a></td>	
							<td id="stat_<?=$file->id?>_<?=$file->category?>">
								<?=$file->statistic_str?>					
							</td>
							<td>
								<a href="/user/objectload_file_list/<?=$file->id?>" target="_blank">Все строки</a>, 
								<? if ($file->error_exists):?>
									<a href="/user/objectload_file_list/<?=$file->id?>?errors=1" target="_blank">Строки с ошибками</a>
								<? endif;?>													
							</td>
							<td></td>
							<td class="buttons_<?=$item->id?>">
								
							</td>		
						</tr>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</table>

			<? if (!$hidehelp):?>
				<p><h2>Настройка соответствий</h2>
					<div class="massload-controlsrow">
						<ul>
							<? foreach($categories as $key=>$value): ?>
								<li key="<?=$key?>"><a href="/user/massload_conformities/<?=$key?>"><?=$value?></a></li>
							<? endforeach; ?>
						</ul>	
					</div>
				</p>
				<p>
					<h2>Шаблоны</h2>
					<div class="massload-controlsrow">
						<ul>
							<? foreach($categories as $key=>$value): ?>
								<li key="<?=$key?>"><a href="<?=$categories_templates[$key]?>" target="_blank"><?=$value?>.xls</a></li>
							<? endforeach; ?>
						</ul>	
					</div>
				</p>
			<? endif;?>
		</div>


	<? else: ?>
		Услуга массовой загрузки не подключена.
	<? endif; ?>
</div>