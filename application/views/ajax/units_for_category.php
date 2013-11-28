<?php 
if(!empty($units )) {
	?>
	<section class="filials-bl mt15">
		<h3>Филиалы компании</h3>
		<?php foreach($units as $unit) { ?>
		<article class="article">
			
			<div class="visible-bl">
				<div class="img">
					<div class="img-container">
						<?php if (!empty($unit->filename)) : ?>
							<img src="<?=Uploads::get_file_path($unit->filename, '136x136')?>" alt="" />
						<?php else : ?>
							<div class="ta-c">Фото отсутствует</div>
						<?php endif ?>
					</div>
					

				</div>
				<div class="content">
					
					<p class="title"><?=$unit->title ?><span class="inf">(<?=$unit->unit->title ?>)</span></p>
					
					<?php
					if($unit->location) : ?>
					<p class="addr"><?php echo $unit->location->city ?>
						<?php if (trim($unit->location->address) != '') : ?>
							, <?php echo $unit->location->address; ?> <span class="show-map toggle"><span class="show">на карте</span><span>свернуть карту</span></span>
						<?php endif ?>
					</p>
					<div class="map-bl">
						<div class="map"><div id="ymap_<?=$unit->id?>" style="width: 372px; height: 236px;"></div>
						<script>
							var myMap_<?=$unit->id?>;
							ymaps.ready(function(){
								var myGeocoder = ymaps.geocode('<?=$unit->location->city.", ".$unit->location->address?>');
								myGeocoder.then(
									function (res) {
										var coords = res.geoObjects.get(0).geometry.getCoordinates(); 
										myMap_<?=$unit->id?> = new ymaps.Map ("ymap_<?=$unit->id?>", {
											center: coords,
											zoom: 15,
										});

										myMap_<?=$unit->id?>.controls.add("zoomControl").add("mapTools").add(new ymaps.control.TypeSelector(["yandex#map", "yandex#satellite", "yandex#hybrid", "yandex#publicMap"]));


										myMap_<?=$unit->id?>.geoObjects.add(new ymaps.Placemark(coords, { 
											hintContent: '<?=$unit->title?>', 
											balloonContent: '<?=$unit->title.", ".$unit->location->city.", ".$unit->location->address?>' 
										}));
									}
									); 
							});


						</script>
					</div>
				</div><?php endif; ?>
				<?php if( ! empty($unit->description)) : ?><p class="pt10">
					<?=nl2br($unit->description);?>
				</p><?php endif; ?>
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
	<?php } ?>
</section>
<? } ?>
