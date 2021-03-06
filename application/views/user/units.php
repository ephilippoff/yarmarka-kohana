<style type="text/css">
label.filebutton{
    overflow: visible;
}
</style>
<script type="text/javascript" src="/js/adaptive/jquery.cookie.js"></script>

<script src="http://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU" type="text/javascript"></script>

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
					$('.reduct-bl .addPhoto').css('margin', '112px 0px 0px 6px');
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
				$.post( "/user/edit_unit_image", { id:parent_id, filename:json.filename_to_save }, function( data ) {}, "json");
			} else if (json.error) {}
		},
		error: function (data, status, e) {
			console.log(data.responseText);
		}
	});
}

var autocomplete_city_id;
var address_input;

$(document).ready(function() {    
	$('#islide_profile').click();
		
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
		if (confirm("Вы уверены, что хотите удалить изображение?")) {
			var parent_el = $(this).closest('article');
			var parent_id = parent_el.attr('data-id');
			$.post( "/user/remove_image", { id:parent_id }, function( data ) {
				if(data.success) {
					parent_el.find('.avatar_img').attr('src', '');
					parent_el.find('.mydel').hide();
				}
				else alert('Произошла ошибка!');
			}, "json");
		}
	});

	address_input = $( "input.address" );
	address_input.prop('disabled', true);
	$( "input.city" ).autocomplete({
      source: "<?=URL::base('http')?>ajax/kladr_city_autocomplete",
      minLength: 1,
      select: function( event, ui ) {
      	autocomplete_city_id = ui.item.id;
      	if(autocomplete_city_id) {
      		address_input.prop('disabled', false);
	      	address_input.autocomplete( "option", "source", "<?=URL::base('http')?>ajax/kladr_address_autocomplete?parent_id="+autocomplete_city_id );
	      	$("#city_kladr_id").val(autocomplete_city_id);
	    }
      }
    })
    .data( "ui-autocomplete" )._renderItem = render_autocomplete;

	address_input.autocomplete({
      source: "<?=URL::base('http')?>ajax/kladr_address_autocomplete?parent_id="+autocomplete_city_id,
      minLength: 2,
      select: function( event, ui ) {
	      	$("#address_kladr_id").val(ui.item.id);
      }
    })
    .data( "ui-autocomplete" )._renderItem = render_autocomplete;
	//Всё для ие8
//	$('.filebutton img').click(function(e){
//		$('#avatar_input').click();
//		e.preventDefault();
//		e.stopPropagation();
//	})
});

function render_autocomplete( ul, item ) {
  return $( "<li>" )
    .append( "<a>" +item.label + "</a>" )
    .appendTo( ul );
};
</script>
<div class="winner cabinet units">
	<section class="main-cont">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">
                <?/*<button id="temp">Temp</button>*/?>
				<header><span class="title">Адреса компании</span></header>
				<div class="p_cont secure-bl myinfo">
					<section class="filials-bl reducting">
<!--						<article class="informator">
							<p class="title"><span style="display: block;">Что это такое?</span><a href="" class="toggle"><span class="show">свернуть</span><span>развернуть</span></a></p>
							<div class="cont" style="display: block;">
								<p style="text-align: justify">рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув рыба рыбааьбыыр рыбабытр как рыбы бырабыр вув </p>
							</div>
						</article>-->
						
							<a href="" class="btn-blue2 btn-reduct ml10 mt10 mb10">
								<span>
									<?php if (Arr::get($_GET, 'add', 'none') == 'none') : ?>
										Добавить
									<?php else :?>
										Отменить
									<?php endif;?>
								</span>
							</a>
					<script> 
						$('.btn-reduct').click(function(e){
							e.preventDefault();
							$('.reduct-bl').toggle();
							if ($('.reduct-bl').css('display') == 'none') $(this).children('span').text('Добавить')
							else $(this).children('span').text('Отменить');
							return false;
						})
					</script>
	                    <article class="article">
							<div class="reduct-bl" <?php if (Arr::get($_GET, 'add', 'none') == 'none') : ?> style="display: none;" <?php endif; ?> >
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
									<input type="hidden" name="address_kladr_id" id="address_kladr_id" />
									<input type="hidden" name="city_kladr_id" id="city_kladr_id" />
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
														<select class="iselect-ns" name="unit_id" id="unit_id">
															<option value="">-- выберите тип --</option>

	                                                        <?php foreach ($units as $unit):?>
	                                                            <option value="<?=$unit->id?>"><?=$unit->title?></option>
	                                                        <?php endforeach; ?>
														</select>
													</div>
												</div>
											</div>
										</li>
										<li>
											<div class="input style2">
												<label><span><i class="name">Сфера деятельности</i></span></label>
												<div class="inp-cont-bl ">
													<div class="inp-cont">
														<select class="iselect-ns" name="business_type_id" id="business_type_id">
															<option value="">-- выберите категорию --</option>

	                                                        <?php foreach ($business_types as $business_type):?>
	                                                            <option value="<?=$business_type->id?>"><?=$business_type->title?></option>
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
														<div class="inp"><input type="text" class="city" name="city" /></div>				
													</div>
												</div>
											</div>
										</li>								
										<li>
											<div class="input style2">
												<label><span><i class="name">Адрес</i></span></label>
												<div class="inp-cont-bl ">
													<div class="inp-cont">
														<div class="inp"><input type="text" class="address" name="address" /></div>				
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
										<img src="/images/01.png" alt=""><br><br><br><br>
										<p>Больше мест — больше объявлений</p>
										<a href="" class="btn-blue2 btn-save"><i class="ico ico-save"></i><span>Сохранить</span></a>
									</div>
									<img class="bottom-shadow" src="/images/shadow5.png" alt="">
                                </form>
							</div>
						</article>
						
						<?php if ($user_units) foreach($user_units as $unit): ?>
						<article class="article filial" data-id="<?=$unit->id?>">
							<div class="visible-bl" style="display: block;">
								<div class="mylogo-bl ml6">
									<form method="post" accept-charset="utf-8" enctype="multipart/form-data">
										<label class="filebutton">
											<div class="img-container">
											<?php if ($unit->filename) : ?>
												<img src="<?=Uploads::get_file_path($unit->filename, '136x136')?>" class="avatar_img" />
											<?php else : ?>
												<img src="" class="avatar_img" />
											<?php endif; ?>
											</div>
											<input type="file" name="unit_image_input" class="fileInput_edit" id="fileInput_<?=$unit->id?>" />
											<span class="addPhoto">
													<span><span id="add_image_btn_text">Кликните в область, чтобы сменить фото</span></span>
											</span>												
										</label>
									</form>

									<span class="mydel" <?php if ( ! $unit->filename) echo "style='display:none;'" ?>></span>
								
								</div>
								
								<div class="content">
									<div class="right-b">
										<div class="publish"><span class="cont">Опубликовано</span><span class="remove"></span></div>
									</div>
									<p class="title"><?php echo $unit->title ?><span class="inf">(<?php echo $unit->unit->title; ?>)</span></p>
									<?php
									if($unit->location)
									{ 
									?>
									<p class="addr">
											<?php echo $unit->location->city ?>
											<?php if (trim($unit->location->address) != '') : ?>
														, <?php echo $unit->location->address; ?> <span class="show-map toggle"><span class="show">на карте</span><span>свернуть карту</span></span>
											<?php endif ?>
									</p>
									<div class="map-bl">
										<div class="map">
											<div id="ymap_<?=$unit->id?>" style="width: 372px; height: 236px;"></div>

											<script type="text/javascript">
										        ymaps.ready(init_<?=$unit->id?>);
										 
										        function init_<?=$unit->id?> () {
										            var myGeocoder = [<?=$unit->location->lat.", ".$unit->location->lon?>];
										            var myMap = new ymaps.Map('ymap_<?=$unit->id?>', {
										                    center: myGeocoder, 
										                    zoom: 13
										                });
													var myPlacemark = new ymaps.Placemark(
														myGeocoder        
													);
													myMap.geoObjects.add(myPlacemark);
										        }
										    </script>

										</div>
									</div>
									<?php
									}
									?>	
									<?php
									if( ! empty($unit->description))
									{
									?><p class="pt10">
										<?=nl2br($unit->description);?>
									</p>
									<?php
									}
									?>
									<div class="contacts oh">
										<ul>
											<li class="title">
												<label><span><i class="name">Контакты:</i></span></label>
											</li>
											<li class="add-contact-li">											
												<?php echo $unit->contacts ?>
											</li>
										</ul>
									</div>
									<?php if (!empty($unit->web)) : ?>
										<p class="site-link pt10">
											<a target="_blank" rel="nofollow" href="<?=URL::prep_url($unit->web)?>"><?=URL::prep_url($unit->web)?></a>	
										</p>
									<?php endif;?>
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
