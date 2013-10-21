<style type="text/css">
label.filebutton{
    overflow: visible;
}
</style>
<script type="text/javascript" src="/js/adaptive/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" charset="utf-8">
	// wisiwyg
	tinyMCE.init({
			mode : "textareas",
			editor_selector : "tiny",
			theme : "simple",
			language: "ru",
			plugins : "paste",
			width: "335px",
			paste_text_sticky : true,
			setup : function(ed) {
				ed.onInit.add(function(ed) {
				  ed.pasteAsPlainText = true;
				});

				//ed.onKeyUp.add(function(ed, e) {
					//var text = tinyMCE.activeEditor.getContent({format : 'raw'});
				//});
			}
	});
	
function ajaxUpload() {
	$.ajaxFileUpload({
			url:'/user/upload_unit_image', 
			secureuri:false,
			fileElementId:'unit_image_input',
			dataType: 'json',
			success: function (json, status) {
					if (json.code == 200) {
							$('#unit_image').attr('src', json.filename);
							$('#unit_image_input').change(function(){
								ajaxUpload();
							});
							$('#add_image_btn_text').text('Изменить фото');
							$('.addPhoto').css('margin', '112px 0px 0px 6px');
							$('#unit_image_filename').val(json.filename_to_save);
					} else if (json.error) {
							var div = $('#unit_image_input').parents('div.input');
							div.addClass('error');
							div.find('.alert-bl p.text span').html(json.error);
							div.find('.alert-bl').show();
					}
			},
			error: function (data, status, e) {
					console.log(data.responseText);
			}
	});
}

	
function ajaxUploadAndChange(fileEl) {
	var parent_el = $(fileEl).closest('article');
	var parent_id = parent_el.attr('data-id');
	var fileInputId = $(fileEl).attr('id');
	
	$.ajaxFileUpload({
			url:'/user/upload_unit_image', 
			secureuri:false,
			fileElementId: fileInputId,
			dataType: 'json',
			success: function (json, status) {
					if (json.code == 200) {
							parent_el.find('.avatar_img').attr('src', json.filename_big);
							parent_el.find('.mydel').show();
							$('#'+fileInputId).change(function(){
								ajaxUploadAndChange();
							});
							$.post( "/user/edit_unit_image", { id:parent_id, filename:json.filename_to_save }, function( data ) {
								if(data.success) alert('Фото обновленно.');
								else alert('Произошла ошибка!');
							}, "json");
					} else if (json.error) {}
			},
			error: function (data, status, e) {
					console.log(data.responseText);
			}
	});
}
	
$(document).ready(function() {
    $(".btn-save").click(function(e){
        e.preventDefault();
        $("#create-init").submit();
    });
    $('#unit_image_input').change(function(){
		ajaxUpload();
    });
    $('.fileInput_edit').change(function(){
		ajaxUploadAndChange(this);
    });
	
	$('.remove').click(function() {
		if (confirm("Вы уверенны, что хотите удалить запись?")) {
			var parent_el = $(this).closest('article');
			var parent_id = parent_el.attr('data-id');
			$.post( "/user/remove_unit", { id:parent_id }, function( data ) {
				if(data.success) parent_el.remove();
				else alert('Произошла ошибка!');
			}, "json");
		}
	});
	
	$('.mydel').click(function() {
		if (confirm("Вы уверенны, что хотите удалить изображение?")) {
			var parent_el = $(this).closest('article');
			var parent_id = parent_el.attr('data-id');
			$.post( "/user/remove_image", { id:parent_id }, function( data ) {
				if(data.success) {
					parent_el.find('.avatar_img').attr('src', '<?=URL::site('images/mylogo.png')?>');
					parent_el.find('.mydel').hide();
				}
				else alert('Произошла ошибка!');
			}, "json");
		}
	});
	//Всё для ие8
//	$('.filebutton img').click(function(e){
//		$('#avatar_input').click();
//		e.preventDefault();
//		e.stopPropagation();
//	})
})		
</script>
<div class="winner cabinet profile">
	<section class="main-cont">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">
                <?/*<button id="temp">Temp</button>*/?>
				<header><span class="title">Подразделения</span></header>
				<div class="p_cont secure-bl myinfo">
					<section class="filials-bl reducting">
						<article class="informator">
							<p class="title"><span style="display: block;">Что это такое?</span><a href="" class="toggle"><span class="show">свернуть</span><span>развернуть</span></a></p>
							<div class="cont" style="display: block;">
								<p style="text-align: justify">рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув </p>
							</div>
						</article>
						
						<a href="" class="btn-blue2 btn-reduct"><span>Добавить</span></a>
					<script> 
						$('.btn-reduct').click(function(e){
							e.preventDefault();
							$('.reduct-bl').toggle();
							return false;
						})
						//select choosen
						$(document).ready(function(){
							$(".iselect").chosen();
						});
					</script>
	                    <article class="article">
							<div class="reduct-bl" style="display: none;">
                                                                <form method="post" accept-charset="utf-8" enctype="multipart/form-data">
                                                                    <label class="filebutton">
                                                                    <div class="img">
                                                                        <div class="img-container"><img src="" id="unit_image" /></div>
                                                                        <span class="addPhoto">
                                                                            <span><span id="add_image_btn_text">Добавить фото</span><input type="file" name="unit_image_input" class="avatar" id="unit_image_input" />
                                                                            </span>
                                                                        </span>
                                                                        <?/*<div class="number">#325556474844878</div>*/?>
                                                                    </div>
                                                                    </label>
								</form>
                                                            <form id="create-init" action="<?=URL::base('http')?>user/addunit" method="POST">
                                
								<input type="hidden" name="unit_image_filename" id="unit_image_filename" />
								<ul class="main-ul">
									<li>
										<div class="input style2">
											<label><span><i class="name">Название</i></span></label>
											<div class="inp-cont-bl ">
												<div class="inp-cont">
													<div class="inp"><input type="text" class="" name="title"></div>
												</div>
											</div>
										</div>
									</li>
									<li>
										<div class="input style2">
											<label><span><i class="name">Тип</i></span></label>
											<div class="inp-cont-bl ">
												<div class="inp-cont">
													<select class="iselect " name="unit_id" id="unit_id">
														<option value="">-- выберите тип --</option>
                                                                                                                    <? foreach ($units as $unit):?>
                                                                                                                        <option value="<?=$unit->id?>"><?=$unit->title?></option>
                                                                                                                    <? endforeach;?>
													</select>
												</div>
											</div>
										</div>
									</li>
									<li>
										<div class="input style2">
											<label><span><i class="name">Регион</i></span></label>
											<div class="inp-cont-bl ">
												<div class="inp-cont">
													<select class="iselect " name="region_id" id="region_id">
														<option value="">-- выберите регион --</option>
														<?php foreach ($regions as $region) : ?>
														<option value="<?=$region->id?>" <?=Arr::get($_GET, 'region_id') == $region->id ? 'selected' : ''?>><?=$region->title?></option>
														<?php endforeach; ?>
													</select>													
												</div>
											</div>
										</div>
									</li>
									<li>
										<div class="input style2">
											<label><span><i class="name">Город</i></span></label>
											<div class="inp-cont-bl ">
												<div class="inp-cont">																		
													<select class="iselect " name="city_id" id="city_id">
														<option value="">-- выберите город --</option>
														<?php foreach ($cities as $city) : ?>
														<option value="<?=$city->id?>" <?=Arr::get($_GET, 'city_id') == $city->id ? 'selected' : ''?>><?=$city->title?></option>
														<?php endforeach; ?>
													</select>
												</div>
											</div>
										</div>
									</li>								
									<li>
										<div class="input style2">
											<label><span><i class="name">Адресс</i></span></label>
											<div class="inp-cont-bl ">
												<div class="inp-cont">
													<div class="inp"><input type="text" class="" name=""></div>				
												</div>
											</div>
										</div>
									</li>
													<li>
	                    								<div class="input style2">
							                    			<label><span><i class="name">Сайт</i></span></label>
							                    			<div class="inp-cont-bl ">
							                    				<div class="inp-cont">
							                    					<div class="inp"><input type="text" class="" name="web" /></div>
							                    					<span class="inform">
							                    						<span>На ваш E-mail придет письмо с подтверждением регистрации</span>
							                    					</span>
							                    					<div class="alert-bl">
							                    						<div class="cont">
							                    							<div class="img"></div>
							                    							<div class="arr"></div>
							                    							
							                    							<p class="text"><span>Важно заполнить поле e-mail правильно, иначе вы не сможете активировать свой аккаунт и пользоваться многими преимуществами зарегистрированных... &nbsp;  <a href="">>>></a></span></p>
							                    						</div>
							                    					</div>
							                    				</div>
							                    			</div>
							                    		</div>
						                    		</li>
									
													<li>
	                    								<div class="input style2">
							                    			<label><span><i class="name">Контакты</i></span></label>
							                    			<div class="inp-cont-bl ">
							                    				<div class="inp-cont">
							                    					<div class="inp"><input type="text" class="" name="contacts" /></div>
							                    				</div>
							                    			</div>
							                    		</div>
						                    		</li>	
													<li>
	                    								<div class="input style2">
							                    			<label><span><i class="name">Описание</i></span></label>
							                    			<div class="inp-cont-bl ">
																<textarea name="description" class="textarea" style="width: 100%;"></textarea>
							                    			</div>
							                    		</div>
						                    		</li>
								</ul>
								<div class="info-bl" style="height: 97%;">
									<p>Добавьте свои места продаж и сотрудников, подавайте объявления от их имени</p><br><br><br>
									<img src="images/01.png" alt=""><br><br><br><br>
									<p>Больше мест — больше объявлений</p>
									<a href="" class="btn-blue2 btn-save"><i class="ico ico-save"></i><span>Сохранить</span></a>
								</div>
								<img class="bottom-shadow" src="images/shadow5.png" alt="">
                                                                </form>
							</div>
						</article>
						
						<?php if ($user_units) foreach($user_units as $unit): ?>
						<article class="article" data-id="<?=$unit->id?>">
							<div class="visible-bl" style="display: block;">
			<div class="mylogo-bl">
				<form method="post" accept-charset="utf-8" enctype="multipart/form-data">
					<label class="filebutton">
						<div class="img-container">
						<?php if ($unit->filename) : ?>
							<img src="<?=Uploads::get_file_path($unit->filename, '208x208')?>" class="avatar_img" />
						<?php else : ?>
							<img src="<?=URL::site('images/mylogo.png')?>" class="avatar_img" />
						<?php endif; ?>
						</div>
						<input type="file" name="unit_image_input" class="fileInput_edit" id="fileInput_<?=$unit->id?>" />
					</label>
				</form>

				<span class="mydel" <?php if ( ! $unit->filename) echo "style='display:none;'" ?>></span>
			</div>
			
			<!--					<div class="img"><img src="img/if_5.jpg" alt=""><a href="" class="addPhoto"><span>Добавить фото</span></a><div class="number">#325556474844878</div></div>-->
								<div class="content">
									<div class="right-b">
										<div class="publish"><span class="cont">Опубликовано</span><span class="remove"></span></div>
									</div>
									<p class="title"><?php echo $unit->title ?><span class="inf">(<?php echo $unit->unit->title; ?>)</span></p>
									<?php
									if(count($unit->get_address()) > 2) { ?><p class="addr"><?php echo $unit->get_address(); ?> <span class="show-map toggle"><span class="show">на карте</span><span>свернуть карту</span></span></p>
									<div class="map-bl">
										<div class="map"><div id="ymaps-map-id_1352895717894414938722" style="width: 372px; height: 236px;"><ymaps class="ymaps-map ymaps-i-ua_js_yes" style="z-index: 0; width: 0px; height: 0px;"><ymaps class="ymaps-glass-pane ymaps-events-pane" style="z-index: 500; position: absolute; width: 0px; height: 0px; left: 0px; top: 0px; -webkit-user-select: none; -webkit-transform: translate3d(0px, 0px, 0px) scale(1, 1); cursor: url(http://api-maps.yandex.ru/2.0.30/images/ef50ac9e93aaebe3299791c79f277f8e) 16 16, url(http://api-maps.yandex.ru/2.0.30/images/ef50ac9e93aaebe3299791c79f277f8e), move;" unselectable="on"></ymaps><ymaps class="ymaps-layers-pane" style="z-index: 100; position: absolute; left: 0px; top: 0px;"><ymaps style="z-index: 150; position: absolute;"><canvas height="256" width="256" style="position: absolute; width: 256px; height: 256px; left: -128px; top: -128px;"></canvas></ymaps></ymaps><ymaps class="ymaps-copyrights-pane" style="z-index: 1000; position: absolute;"><ymaps><ymaps class="ymaps-copyrights-logo"><ymaps class="ymaps-logotype-div"><a target="_blank" class="ymaps-logo-link ymaps-logo-link-ru" href="http://maps.yandex.ru/?origin=jsapi&amp;ll=158.622473,53.061561&amp;z=10&amp;l=map"><ymaps class="ymaps-logo-link-wrap"></ymaps></a></ymaps></ymaps><ymaps class="ymaps-copyrights-legend"><ymaps class="ymaps-copyright-legend-container"><ymaps class="ymaps-copyright-legend"><ymaps class="ymaps-copyright-legend-element ymaps-copyright-legend-element-black"><ymaps style="display: inline;">©&nbsp;Роскартография</ymaps>, <ymaps style="display: inline;">©&nbsp;ЗАО&nbsp;«Геоцентр-Консалтинг»,&nbsp;2009</ymaps>, <ymaps style="display: inline;">©&nbsp;ЗАО «Резидент»</ymaps></ymaps></ymaps><ymaps class="ymaps-copyright-agreement ymaps-copyright-agreement-black"><ymaps class="ymaps-coyprights-ua-extended"><ymaps><a href="http://maps.yandex.ru/?ll=158.622473,53.061561&amp;z=10&amp;origin=jsapi&amp;fb" target="_blank">Сообщить об ошибке</a> · </ymaps></ymaps><a href="http://legal.yandex.ru/maps_termsofuse/" target="_blank">Пользовательское соглашение</a></ymaps></ymaps></ymaps></ymaps></ymaps><ymaps class="ymaps-controls-pane" style="z-index: 800; position: static;"><ymaps class="ymaps-controls-lefttop" style="z-index: 800;"><ymaps class="ymaps-b-zoom_hints-pos_right" style="top: 75px; left: 5px; position: absolute;"><ymaps><ymaps class="ymaps-b-zoom"><ymaps class="ymaps-b-zoom__button ymaps-b-zoom__button_type_minus" unselectable="on" style="-webkit-user-select: none;"><ymaps class="ymaps-b-form-button ymaps-b-form-button_size_sm ymaps-b-form-button_theme_grey-sm ymaps-b-form-button_height_26 ymaps-i-bem" role="button"><ymaps class="ymaps-b-form-button__left"></ymaps><ymaps class="ymaps-b-form-button__content"><ymaps class="ymaps-b-form-button__text"><ymaps class="ymaps-b-zoom__sprite"></ymaps></ymaps></ymaps></ymaps></ymaps><ymaps class="ymaps-b-zoom__scale" unselectable="on" style="height: 139.66666666666666px; -webkit-user-select: none;"><ymaps class="ymaps-b-zoom__scale-bg"></ymaps><ymaps class="ymaps-b-zoom__mark" style="top: 73px;"><ymaps class="ymaps-b-zoom__mark-inner"><ymaps class="ymaps-b-zoom__sprite"></ymaps></ymaps></ymaps><ymaps class="ymaps-b-hint-placeholder"><ymaps><ymaps><ymaps class="ymaps-b-zoom__hint" style="top: 17px;"><ymaps class="ymaps-b-zoom__hint-left"><ymaps class="ymaps-b-zoom__sprite"></ymaps></ymaps><ymaps class="ymaps-b-zoom__hint-content"><ymaps class="ymaps-b-zoom__hint-text">мир</ymaps></ymaps><ymaps class="ymaps-b-zoom__hint-right"><ymaps class="ymaps-b-zoom__sprite"></ymaps></ymaps></ymaps></ymaps></ymaps><ymaps><ymaps><ymaps class="ymaps-b-zoom__hint" style="top: 38px;"><ymaps class="ymaps-b-zoom__hint-left"><ymaps class="ymaps-b-zoom__sprite"></ymaps></ymaps><ymaps class="ymaps-b-zoom__hint-content"><ymaps class="ymaps-b-zoom__hint-text">страна</ymaps></ymaps><ymaps class="ymaps-b-zoom__hint-right"><ymaps class="ymaps-b-zoom__sprite"></ymaps></ymaps></ymaps></ymaps></ymaps><ymaps><ymaps><ymaps class="ymaps-b-zoom__hint" style="top: 66px;"><ymaps class="ymaps-b-zoom__hint-left"><ymaps class="ymaps-b-zoom__sprite"></ymaps></ymaps><ymaps class="ymaps-b-zoom__hint-content"><ymaps class="ymaps-b-zoom__hint-text">город</ymaps></ymaps><ymaps class="ymaps-b-zoom__hint-right"><ymaps class="ymaps-b-zoom__sprite"></ymaps></ymaps></ymaps></ymaps></ymaps><ymaps><ymaps><ymaps class="ymaps-b-zoom__hint" style="top: 94px;"><ymaps class="ymaps-b-zoom__hint-left"><ymaps class="ymaps-b-zoom__sprite"></ymaps></ymaps><ymaps class="ymaps-b-zoom__hint-content"><ymaps class="ymaps-b-zoom__hint-text">улица</ymaps></ymaps><ymaps class="ymaps-b-zoom__hint-right"><ymaps class="ymaps-b-zoom__sprite"></ymaps></ymaps></ymaps></ymaps></ymaps><ymaps><ymaps><ymaps class="ymaps-b-zoom__hint" style="top: 115px;"><ymaps class="ymaps-b-zoom__hint-left"><ymaps class="ymaps-b-zoom__sprite"></ymaps></ymaps><ymaps class="ymaps-b-zoom__hint-content"><ymaps class="ymaps-b-zoom__hint-text">дом</ymaps></ymaps><ymaps class="ymaps-b-zoom__hint-right"><ymaps class="ymaps-b-zoom__sprite"></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps><ymaps class="ymaps-b-zoom__button ymaps-b-zoom__button_type_plus" unselectable="on" style="-webkit-user-select: none;"><ymaps class="ymaps-b-form-button ymaps-b-form-button_size_sm ymaps-b-form-button_theme_grey-sm ymaps-b-form-button_height_26 ymaps-i-bem" role="button"><ymaps class="ymaps-b-form-button__left"></ymaps><ymaps class="ymaps-b-form-button__content"><ymaps class="ymaps-b-form-button__text"><ymaps class="ymaps-b-zoom__sprite"></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps><ymaps style="top: 5px; left: 5px; position: absolute;"><ymaps class="ymaps-group"><ymaps><ymaps class="ymaps-group"><ymaps unselectable="on" style="-webkit-user-select: none;"><ymaps><ymaps class="ymaps-b-form-button ymaps-b-form-button_type_tool ymaps-b-form-button_valign_middle ymaps-b-form-button_theme_grey-no-transparent-26 ymaps-b-form-button_height_26 ymaps-i-bem ymaps-b-form-button_selected_yes" title="Переместить карту"><ymaps class="ymaps-b-form-button__left"></ymaps><ymaps class="ymaps-b-form-button__content"><ymaps class="ymaps-b-form-button__text"><ymaps id="id_138054764874642174_1"><ymaps><ymaps class="ymaps-b-form-button__text"><ymaps class="ymaps-b-ico ymaps-b-ico_type_move"></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps><ymaps unselectable="on" style="-webkit-user-select: none;"><ymaps><ymaps class="ymaps-b-form-button ymaps-b-form-button_type_tool ymaps-b-form-button_valign_middle ymaps-b-form-button_theme_grey-no-transparent-26 ymaps-b-form-button_height_26 ymaps-i-bem" title="Увеличить"><ymaps class="ymaps-b-form-button__left"></ymaps><ymaps class="ymaps-b-form-button__content"><ymaps class="ymaps-b-form-button__text"><ymaps id="id_138054764874642174_2"><ymaps><ymaps class="ymaps-b-form-button__text"><ymaps class="ymaps-b-ico ymaps-b-ico_type_magnifier"></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps><ymaps unselectable="on" style="-webkit-user-select: none;"><ymaps><ymaps class="ymaps-b-form-button ymaps-b-form-button_type_tool ymaps-b-form-button_valign_middle ymaps-b-form-button_theme_grey-no-transparent-26 ymaps-b-form-button_height_26 ymaps-i-bem" title="Измерение расстояний на карте"><ymaps class="ymaps-b-form-button__left"></ymaps><ymaps class="ymaps-b-form-button__content"><ymaps class="ymaps-b-form-button__text"><ymaps id="id_138054764874642174_3"><ymaps><ymaps class="ymaps-b-form-button__text"><ymaps class="ymaps-b-ico ymaps-b-ico_type_ruler"></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps><ymaps class="ymaps-controls-righttop" style="z-index: 800;"><ymaps style="top: 5px; right: 5px; position: absolute;"><ymaps><ymaps class="ymaps-b-select ymaps-b-select_control_listbox" style=""><ymaps class="ymaps-b-form-button ymaps-b-form-button_theme_grey-no-transparent-26 ymaps-b-form-button_height_26 ymaps-i-bem" role="button" unselectable="on" style="-webkit-user-select: none;"><ymaps class="ymaps-b-form-button__left"></ymaps><ymaps class="ymaps-b-form-button__content"><ymaps class="ymaps-b-form-button__text"><ymaps id="id_138054764874642174_0" unselectable="on" style="-webkit-user-select: none;"><ymaps><ymaps class="ymaps-b-select__title" style="display: block; width: 15px;">Схема</ymaps><ymaps class="ymaps-b-select__arrow" title="Развернуть"></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps><ymaps class="ymaps-b-popupa ymaps-b-popupa_layout_yes ymaps-b-popupa_theme_white ymaps-i-bem"><ymaps class="ymaps-b-popupa__shadow"></ymaps><ymaps class="ymaps-b-popupa__body ymaps-b-popupa__body_theme_white"><ymaps class="ymaps-b-popupa__ie-gap">&nbsp;</ymaps><ymaps class="ymaps-b-listbox-panel" style=""><ymaps><ymaps class="ymaps-group"><ymaps><ymaps></ymaps><ymaps><ymaps class="ymaps-b-listbox-panel__item ymaps-b-listbox-panel__item_state_current"><ymaps class="ymaps-b-listbox-panel__item-link" unselectable="on" style="-webkit-user-select: none;">Схема</ymaps><ymaps class="ymaps-b-listbox-panel__item-flag"></ymaps></ymaps></ymaps></ymaps><ymaps><ymaps></ymaps><ymaps><ymaps class="ymaps-b-listbox-panel__item "><ymaps class="ymaps-b-listbox-panel__item-link" unselectable="on" style="-webkit-user-select: none;">Спутник</ymaps><ymaps class="ymaps-b-listbox-panel__item-flag"></ymaps></ymaps></ymaps></ymaps><ymaps><ymaps></ymaps><ymaps><ymaps class="ymaps-b-listbox-panel__item "><ymaps class="ymaps-b-listbox-panel__item-link" unselectable="on" style="-webkit-user-select: none;">Гибрид</ymaps><ymaps class="ymaps-b-listbox-panel__item-flag"></ymaps></ymaps></ymaps></ymaps><ymaps><ymaps></ymaps><ymaps><ymaps class="ymaps-b-listbox-panel__item "><ymaps class="ymaps-b-listbox-panel__item-link" unselectable="on" style="-webkit-user-select: none;">Народная карта</ymaps><ymaps class="ymaps-b-listbox-panel__item-flag"></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></ymaps></div>
										<script type="text/javascript">
											function fid_1352895717894414938722(ymaps) {var map = new ymaps.Map("ymaps-map-id_1352895717894414938722", {center: [158.62247349999987, 53.06156138183279], zoom: 10, type: "yandex#map"});map.controls.add("zoomControl").add("mapTools").add(new ymaps.control.TypeSelector(["yandex#map", "yandex#satellite", "yandex#hybrid", "yandex#publicMap"]));};
										</script>  
										<script type="text/javascript" src="http://api-maps.yandex.ru/2.0-stable/?lang=ru-RU&amp;coordorder=longlat&amp;load=package.full&amp;wizard=constructor&amp;onload=fid_1352895717894414938722"></script>
										</div>
									</div><?php } ?>	
									<?php if(!empty($unit->description)) { ?><div>
										<?=nl2br($unit->description);?>
									</div><?php } ?>
									<div class="contacts ">
										<ul>
											<li class="title">
												<label><span><i class="name">Контакты:</i></span></label>
											</li>
											<li class="add-contact-li">											
												<?php echo $unit->contacts ?>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</article>						
						<?php endforeach; ?>
					</section>

	
				</div>
			</section>
		</div>	   
		  
	</section>
</div><!--end content winner-->
