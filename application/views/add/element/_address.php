<div class="smallcont">
			<div class="labelcont">
				<label><span><?=$title?></span></label>
			</div>
			<div class="fieldscont">
				<div class="inp-cont" data-condition="">
					<div class="inp-cont <? if ($errors) echo "error";?>">
						<span class="required-label">*</span>
						
						<input id="<?=$id?>" type="text" name="<?=$name?>" value="<?=$value?>"/>

							<span class="inform">
								<span>Пример заполнения адреса</span>
							</span>
					</div>
			</div>
		</div>									
</div>

<div class="smallcont">
	<div class="labelcont">Укажите местоположение объекта на карте</div>
	<div class="fieldscont">
		<div id="map_block_div" class="map_block_div add_form_info inp-cont-long">
			<div class="map" id="map_block" style="height:250px;width:100%;">		
			карта
				<input type="hidden" id="object_coordinates" name="object_coordinates" value=""/>
			</div>				
		</div><!--#map_block_div-->
	</div><!--fieldscont-->
</div><!--smallcont-->
