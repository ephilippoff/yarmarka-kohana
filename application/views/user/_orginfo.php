<script type="text/javascript">
	function reset_orgtype(){
		if (confirm("Вы уверены что хотите сменить тип учетной записи на 'Частное лицо?'")) {
		  window.location ="/user/reset_orgtype";
		}
	}
</script>
<div class="form-cont">
	<form method="POST" id="orginfo" enctype="multipart/form-data">
		<? 
			$date = new DateTime(); 
			if (!$inn["inn"]): 
		?>

			<? if ($from OR strtotime($date->format('Y-m-d H:i:s')) >= strtotime($expired)): ?>
			
				<div class="row mb10">
					<div class="col-md-3 col-xs-4 labelcont">
						<label></label>
					</div>
					<div class="col-md-9 col-xs-8">
								<div class="inp-cont" style=" font-size:16px;">
									<span style="color:red;">Для продолжения работы с сайтом, пожалуйста, поделитесь с нами информацией о Вашей компании</span>.
								</div>				
					</div>
				</div>		
			
			<? 
				elseif (strtotime($date->format('Y-m-d H:i:s')) <= strtotime($expired)):							
			?>
			
				<div class="row mb10">
					<div class="col-md-3 col-xs-4 labelcont">
						<label></label>
					</div>
					<div class="col-md-9 col-xs-8">
						<div class="inp-cont" style=" font-size:16px;">
							<span style="color:red;">Для продолжения работы с сайтом, пожалуйста, поделитесь с нами информацией о Вашей компании</span>.
							Если сейчас у Вас нет возможности заполнить форму, сделайте это в любое удобное время, но не позднее чем <?=$expired?>
						</div>
					</div>
				</div>			
			
			<? endif; ?>
	
	
		<div class="row mb10">
			<div class="col-md-3 col-xs-4 labelcont">
				<label>1</label>
			</div>
			<div class="col-md-9 col-xs-8">
						<div class="inp-cont">
							<div class="">
								Вы выбрали тип учетной записи "Компания". Для завершения регистрации с этим типом, необходимо предоставить ИНН и загрузить его скан, а также заполнить другие обязательные поля отмеченные зведочкой <span style="color:red;">*</span>.
							</div>
				  		</div>
			</div>
		</div>			
			
		<div class="row mb10">
			<div class="col-md-3 col-xs-4 labelcont">
				<label>2</label>
			</div>
			<div class="col-md-9 col-xs-8">
						<div class="inp-cont">
							<div class="">
								Если вы представитель компании, уже имеющей официальную учетную запись на нашем сайте, и хотите подавать объявления от ее лица, без ограничений, совершите привязку учетной записи к этой компании. (Т.е. компания, в своей учетной записи должна добавить Вашу учетную запись в разделе 'Сотрудники'). Свою учетную запись нужно сменить на тип "Частное лицо"
				  			</div>
				  		</div>
			</div>
		</div>	
			
		<div class="row mb10">
			<div class="col-md-3 col-xs-4 labelcont">
				<label>3</label>
			</div>
			<div class="col-md-9 col-xs-8">
						<div class="inp-cont">
							<div class="">
								Вы можете не заполнять форму и сбросить тип учетной записи на "Частное лицо" если перейдете по <span class="link" style="cursor:pointer;" href="/user/reset_orgtype" onclick="reset_orgtype()">ссылке</span>. При этом будут применены ограничения на количество объявлений в некоторые рубрики (Продажа квартир и комнат, Аренда квартир и комнат, Вакансии)
							</div>
				  		</div>
			</div>
		</div>			
			
		<div class="row mb10">
			<div class="col-md-3 col-xs-4 labelcont">
				<label>4</label>
			</div>
			<div class="col-md-9 col-xs-8">
						<div class="inp-cont">
							<div class="">
								Не забудьте нажать на кнопку "Сохранить", после заполнения формы!
							</div>
				  		</div>
			</div>
		</div>


		<hr/>
		<? endif; ?>
		<? if ($success OR count((array) $errors)): ?>

		<div class="row mb10">
			<div class="col-md-3 col-xs-4 labelcont">
				<label></label>
			</div>
			<div class="col-md-9 col-xs-8">
							<div class="inp-cont">
								<div class="">
									<? if (count((array) $errors)): ?>
										<span style="color:red;">
											<? foreach((array) $errors as $error):?>
												<?=$error?></br>
											<?	endforeach; ?>
										</span>
									<? else: ?>
										<span style="color:green;">Успешно сохранено!</span>
									<? endif; ?>
								</div>	
					  		</div>
			</div>
		</div>		
	
		<? endif; ?>
		<? if (!is_null($inn_moderate["inn_moderate"])): ?>
			<? 
				$style = ""; 
				if ($inn_moderate["inn_moderate"] == 0)
					$style = "background:#FFFACC"; 
				elseif ($inn_moderate["inn_moderate"] == 1)
					$style = "background:#8DE8AB"; 
				elseif ($inn_moderate["inn_moderate"] == 2)
					$style = "background:#FFA6A6";
	
			?>
		
		
		
		
		<div style="<?=$style?>">
			<div class="pb10 pt20">

				<div class="row mb10">
					<div class="col-md-3 col-xs-4 labelcont">
						<label>Состояние модерации</label>
					</div>
					<div class="col-md-9 col-xs-8">
							<div class="inp-cont">
								<div class="">
									<?=$org_moderate_states[$inn_moderate["inn_moderate"]]?>
									<? if ($inn_moderate["inn_moderate"] <> 1): ?> 
										(<a href="/user/orginfoinn_decline_user">сбросить/отменить</a>)
									<? endif; ?>
								</div>
							</div>
					</div>
				</div>		
				
				<? if ($inn_moderate["inn_moderate_reason"]): ?>				
					<div class="row mb10">
						<div class="col-md-3 col-xs-4 labelcont">
							<label>Причина отклонения</label>
						</div>
						<div class="col-md-9 col-xs-8">
								<div class="inp-cont">
									<div class="">
										<?=$inn_moderate["inn_moderate_reason"]?>
									</div>
						  		</div>
						</div>
					</div>											
				<? endif; ?>
			</div>
		</div>
			<hr/>
		<? endif; ?>
		<? if ($inn["inn"]): ?>

		<div class="row mb10">
			<div class="col-md-3 col-xs-4 labelcont">
				<label>ИНН</label>
			</div>
			<div class="col-md-9 col-xs-8">
				<?=$inn["inn"]?>
			</div>
		</div>			
			
		<div class="row mb10">
			<div class="col-md-3 col-xs-4 labelcont">
				<label>Юридическое название</label>
			</div>
			<div class="col-md-9 col-xs-8">
							<div class="inp-cont">
								<div class="">
									<?=$inn["org_full_name"]?>
								</div>
					  		</div>
			</div>
		</div>			
			
		<div class="row mb10">
			<div class="col-md-3 col-xs-4 labelcont">
				<label>Скан ИНН</label>
			</div>
			<div class="col-md-9 col-xs-8">
							<div class="inp-cont">
								<img src="<?=$inn["inn_skan"]?>" style="padding:10px;max-width:300px;">
					  		</div>
			</div>
		</div>
			
			<hr>
		<? endif; ?>
	
		
			<script>
				function setImage(fieldname, success, data, message)
			 	{
			 		if (success){
			 			$('#'+fieldname+"_status").text(message).addClass("success");
						$('#'+fieldname+"_image").html("<img src='"+data.filepaths["120x90"]+"'/>");
						$('#'+fieldname+"_input").html("<input name='"+fieldname+"' type='hidden' value='"+data.filename+"'/>");
						$('#'+fieldname+"_container").show();
			 		} else {
			 			$('#'+fieldname+"_status").text(message).addClass("error");
			 			$('#'+fieldname+"_image").html("");
						$('#'+fieldname+"_input > input").val("");
						$('#'+fieldname+"_container").hide();
			 		}
			 	}
			</script>
			<? foreach ($form as $field): ?>
				<div class="row mb10">
					<div class="col-md-3 col-xs-4 labelcont">
						<label><?= $field["title"] ?></label>
					</div>
					<div class="col-md-9 col-xs-8">
						<?
						$lenth_unput = "col-md-6";
						if ($field["type"] == "long" OR $field["type"] == "text")
							$lenth_unput = "col-md-12";
						?>
						<div class="row">
							<div class="<?= $lenth_unput ?>">
								<div class="inp-cont <? if ($errors->{$field["name"]}) echo "error"; ?>">
								<? if ($field["required"]): ?>
									<span class="required-star">*</span>
								<? endif; ?>

								<? if ($field["type"] == "photo"): ?>
									<script>
										$(document).ready(function() {
											var self = this,
												fieldname = '<?= $field["name"] ?>';
			
											new AjaxUpload(fieldname, {
													action: '/ajax_form/upload_photofield',
													name: fieldname,
													data : {fieldname :fieldname},
													autoSubmit: true,
													onSubmit: function(filename, response){
			
													},
													onComplete: function(filename, response){
														if (response) 
															data = $.parseJSON(response);
														else
															return;
			
														setImage(fieldname, (!data.error), data, (data.error)?data.error:"Файл загружен");
													}
											   });
										});
									</script>
									<?= $field["html"] ?></br>
									<div class="p10">								    			
										<span id="<?= $field["name"] ?>_status"></span>
									</div>
									<div class="p10" style="<? if (!isset($field['value'])) {
								echo 'display:none;';
							} ?>" id="<?= $field['name'] ?>_container">								    					
										<span id="<?= $field["name"] ?>_image">
											<? if (isset($field["value"])): ?>
												<?
												$filepaths = Imageci::getSitePaths($field["value"]);
												$path = $filepaths["120x90"];
												?>
												<img src='<?= $path ?>'/>
											<? endif; ?>
										</span></br>
										<span id="<?= $field["name"] ?>_input">
											<? if (isset($field["value"])): ?>
												<input name='<?= $field["name"] ?>' type='hidden' value='<?= $field["value"] ?>'/>
								<? endif; ?>
										</span></br>
										<span class="span-link" style="cursor:pointer;" onclick="setImage('<?= $field["name"] ?>',false, null, ''); return false;">Удалить</span>
									</div>
								<? else: ?>
									<?= $field["html"] ?>
								<? endif; ?> 		

									<? if ($errors->{$field["name"]}): ?>
									<span class="inform fn-error">
									<?= $errors->{$field["name"]} ?>
									</span>
									<? endif; ?>

									<? if ($field["description"]): ?>
									<span class="inform">
									<?= $field["description"] ?>
									</span>
							<? endif; ?>

							</div>
						</div>	
						</div>
					</div>
				</div>			

			<? endforeach; ?>

			
			<div class="row mb20">
				<div class="col-md-3 labelcont">
					<label></label>
				</div>
				<div class="col-md-9 ta-r">	
					<span onclick="$('#orginfo').submit()" class="button button-style1 bg-color-blue btn-next">Сохранить</span>
				</div>
			</div>			
			
	</form>
</div>
