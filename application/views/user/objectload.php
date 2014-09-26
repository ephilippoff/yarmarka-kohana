<?=HTML::script('bootstrap/js/bootstrap.min.js')?>
<?=HTML::style('bootstrap/css/bootstrap.min.css')?>
<?=HTML::style('bootstrap/css/bootstrap-responsive.min.css')?>
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
									Формат загружаемых файлов: *.xls, *.xlsx;</br>
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
									<th>Статистика <br/>(нов/изм/неизм/ош(%)=все)</th>
									<th>Просмотр</th>
									<th>Состояние</th>
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
												<a style='color:red !important;' href='#' onclick="show_message('<?=$item->comment?>');return false;"><?=$states[$item->state]?></a>
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
										<tr style="border:0px;" class="ol_<?=$item->id?>">			
											<td></td>
											<td></td>
											<td><a target="_blank" href="/user/massload_conformities/<?=$file->category?>"><?=$config[$file->category]['name']?></a></td>	
											<td id="stat_<?=$file->id?>_<?=$file->category?>">
												<?=$file->statistic_str?>					
											</td>
											<td>
												<a href="/user/objectload_file_list/<?=$file->id?>" target="_blank">все</a>, 
												<? if ($file->error_exists):?>
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