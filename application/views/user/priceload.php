<?=HTML::script('bootstrap/js/bootstrap.min.js')?>
<?//=HTML::style('bootstrap/css/bootstrap.min.css')?>
<?=HTML::style('bootstrap/css/customize.bootstrap.css')?>
<?=HTML::style('bootstrap/css/bootstrap-responsive.min.css')?>
<script type="text/javascript">
$(document).ready(function() {
	$('#islide_services').click();
})	
</script>

<div class="winner">
	<section class="main-cont subscriptions cabinet">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">				
				<header><span class="title">
						Загрузка прайс-листов
				</span>
					<? /* ?><form method="post">
						<input type="hidden" name="hidehelp" value="<?=$hidehelp?>">
						<? if ($hidehelp):?>
							<input type="submit" class="btn btn-info ml10 mt3" value="Скрыть пошаговую инструкцию">
						<? else:?>
							<input type="submit" class="btn btn-info ml10 mt3" value="Показать пошаговую инструкцию">
						<? endif;?>
					</form>
					<? */ ?>
			</header>
				<div class="p_cont massload">
					<? if ($hidehelp):?>
							<h2>Шаг 1. Подговьте файл для загрузки</h2>
							<div class="massload-controlsrow">
								
								<div class="massload-hint">	
									<ul>								
										<li>Пример обычного файла (прайс-листа): ---</li>
										<li>Пример плоского файла (прайс-листа): ---</li>
										<li>После загрузки, файл будет проверен модератором.</li>
										<li>Если файл "плоский" и загрузка одобрена модератором, позиции из прайса будут импортированы в поисковый индекс. Ваши предложения увидят на страницах поиска.</li>
									</ul>
								</div>		
							</div>
					<? endif; ?>
					<h2><? if ($hidehelp):?> Шаг 2. <? endif; ?> Загрузите файл</h2>
						<div class="massload-controlsrow">
								
							<div class="massload-hint">	
								<ul>								
									<li>Укажите название прайс-листа. Например: "Услуги по ремонту компьютеров", "Акция на товары от 20.10.2014", "Запчасти на КАМАЗ"</li>
									<li>Нажмите кнопку "Загрузить" для выбора файла</li>
								</ul>
							</div>		
						</div>
						<script type="text/javascript">
						 	$(document).ready(function() {
							 	var self = this;
							    new AjaxUpload('fn-userfile-upload', {
							            action: '/ajax/massload/save_userpricefile',
							            name: 'file',
							            data : {context :self},
							            autoSubmit: true,
							            onSubmit: function(filename, response){
							            	var self = this._settings.data.context;
									        self.title = $("#fn-title-price").val();
									        this.setData({ context : self, title : self.title});
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
						            		if (data.priceload_id){
						            			$(".staticfile_success").html("Файл загружен");
						            			location.reload();
						            		}
						            		
							            }
							       });
							});

							function show_message(message){
								alert(message);
							}

							function delete_pl(id){
								if (!confirm("Удалить?")) {
									return;
								}
								$(".aloader").show();
								$.post( "/ajax/massload/priceload_delete", {id:id}, function( data ) {
								  	$('.pl_'+id).hide();
								  	$(".aloader").hide();
								});
							}

					   </script>
						<input id="fn-title-price" class="form-control" type="text" value="Название прайс-листа"/>
									
						<div id="fn-userfile-upload">
							<!--<div class="">-->
								<span class="btn btn-primary">Загрузить</span>
							<!--</div>-->
						</div>	

						<div class="massload-controlsrow">
							<div class="aloader" style="display:none;">
								<img src="/images/aloader.gif">
							</div>
							<div class="massload-hint">
								<ul>
									<li>Формат загружаемых файлов: *.xls, *.xlsx;</li>
									<li>Бесплатно вы можете загружать не более <?=$free_limit?> прайс листа</li>
								</ul>
							</div>
							<div class="staticfile_error" style="color:red;"></div>
							<div class="staticfile_success" style="color:green;"></div>

						</div>


						<h2>Ваши прайс листы</h2>
						<table class="table table-hover table-condensed" style="width:100%">
							<tr>
								<th>#</th>
								<th style="width:100px;">Дата</th>
								<th style="width:150px;">Название</th>
								<th>Файл</th>
								<th>Просмотр</th>
								<th>Состояние</th>
								<th></th>
							</tr>
							<?php foreach ($priceloads as $item) : ?>
								<tr id="pl_<?=$item->id?>" class="pl_<?=$item->id?>">			
									<td><?=$item->id?></td>
									<td><?=date( "d.m.Y H:i",strtotime($item->created_on))?></td>
									<td><?=$item->title?></td>	
									<td><a href="/<?=$item->filepath_original?>">Оригинал</a></td>								
									<td>
										<? if ($item->state == 2): ?>
											<a href="/user/pricelist/<?=$item->id?>" target="_blank">Просмотр/редактирование</a>, 
										<? endif; ?>
									</td>
									<td>
											<?if (!$item->comment): ?>
												<?=$states[$item->state]?>
											<?else:?>
												<a style='color:red !important;' href='#' onclick="show_message('<?=$item->comment?>');return false;"><?=$states[$item->state]?> (?)</a>
											<?endif;?>
									</td>		
									<td id="pl_button_<?=$item->id?>">
										<span class="icon-trash" onclick="delete_pl(<?=$item->id?>);return false;" style="cursor:pointer;"></span>
									</td>
								</tr>
							<?php endforeach; ?>
						</table>
				
				</div>
				<div class="clear"></div>
				<br />
				
			</section>
		</div>	   
		  
	</section>
</div><!--end content winner-->