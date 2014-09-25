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
										            },
										            onComplete: function(filename, response){
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
									   </script>
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
								<div class="massload-hint">
									Формат загружаемых файлов: *.csv, *.xls, *.xlsx;</br>
									При включенном флаге "Игнорировать ошибки" сохранятся все объявления, в которых не обнаружены ошибки, остальные будут проигнорирвоаны.</br>
									Подробнее : <a href="#">Помощь по массовой загрузке объявлений</a>
								</div>
								<div class="staticfile_error" style="color:red;"></div>
								<div class="staticfile_success" style="color:green;"></div>
							</div>
							<p><h2>Загрузки</h2>
							<table class="table table-hover table-condensed" style="width:100%">
								<tr>
									<th>#</th>
									<th>Дата</th>
									<th>Категория</th>
									<th>Статистика <br/>(нов./ред./все/ош.(%))</th>
									<th>Просмотр</th>
									<th>Состояние</th>
								</tr>
								<?php foreach ($objectloads as $item) : ?>
									<?
										$statstr = '';
										$errorstr ='';
										if ($item->statistic) {
											$statistic = new Obj(unserialize($item->statistic));
											$new = $statistic->loaded - $statistic->edited;
											if($statistic->error>0)
											{
												$percent = 0;
												if ($statistic->all<>0)
													$percent = round(($statistic->error/$statistic->all)*100);

												$allow_percent = Kohana::$config->load('massload.allow_error_percent');
												
												$color = "red";
												if ($percent < $allow_percent)
													$color = "green";

												$errorstr = "/ <span style='color:$color;'>".$statistic->error." (".$percent."%)</span>";
											}
											$statstr = $new." / ".$statistic->edited." / ".$statistic->all." ".$errorstr;
										}
									?>
									<tr>			
										<td><?=$item->id?></td>
										<td><?=$item->created_on?></td>
										<td></td>	
										<td id="stat_<?=$item->id?>"><?=$statstr?></td>
										<td></td>
										<td>
											<?if ($item->state <> 99 AND $item->state <> 3): ?>
												<?=$states[$item->state]?>
											<?else:?>
												<a href='#' onclick="show_message('<?=$item->comment?>');return false;"><?=$states[$item->state]?></a>
											<?endif;?>
										</td>		
									</tr>
									<?php foreach ($item->objfiles as $file) : ?>
										<?
											$statstr = '';
											$notloaded_button = FALSE;
											$witherror_button = FALSE;
											if ($file->statistic) {
												$statistic = new Obj(unserialize($file->statistic));
												$new = $statistic->loaded - $statistic->edited;
												$flagend ='';
												if ($statistic->loaded + $statistic->error <> $statistic->all)
												{
													$flagend = '<span style="color:red;">(!)</span>';
													$notloaded_button = TRUE;
												}
												$errorstr ='';
												if($statistic->error>0)
												{
													$witherror_button = TRUE;
													$errorstr = "Ошибок:".$statistic->error;
												}
												$statstr = $new." / ".$statistic->edited." / ".$statistic->all." ".$errorstr." ".$flagend;
											}
										?>
										<tr style="border:0px;">			
											<td></td>
											<td></td>
											<td><a target="_blank" href="/user/massload_conformities/<?=$file->category?>"><?=$config[$file->category]['name']?></a></td>	
											<td id="stat_<?=$file->id?>_<?=$file->category?>">
												<?=$statstr?>					
											</td>
											<td>
												<a href="/user/objectload_file_list/<?=$file->id?>" target="_blank">все</a>, 
												<? if ($witherror_button):?>
													<a href="/user/objectload_file_list/<?=$file->id?>?errors=1" target="_blank">только ошибки</a>
												<? endif;?>
													
											</td>
											<td class="buttons_<?=$item->id?>">
												
											</td>		
										</tr>
									<?php endforeach; ?>
								<?php endforeach; ?>
							</table>
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