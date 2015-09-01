<div class="kupon-print-cont">
	<div class="row mb30">
		<div class="logo fl"><img src="/images/black_logo.png"></div>
		<div class="fr"></div>
	</div>
	<p class="tt-u ta-c fs20 fw-b mb5">Скидочный купон №<?=Text::format_kupon_number($kupon->number)?></p>
	<p class="ta-c color-gray mb20">Предъявите этот купон на месте, чтобы получить услугу</p>
	<hr class="mb10">
	<p class="ta-c tt-u fs26 mb5">ПИН-КОД <?=$kupon->code?></p>
	<p class="ta-c tt-u color-gray mb15">Внимание! Не сообщайте пин-код до момента получения товара или услуги</p>
	<hr class="mb20">
	<p class="mb20 fs16 fw-b ta-c"><a href="<?='http://'.Kohana::$config->load('common.main_domain')?>/detail/<?=$object->id?>"><?=strip_tags($object->title)?></a></p>
	<hr class="mb10">
	<div class="row mb10">
		<div class="fl w49p">
			<p class="fw-b mb20"><?=$kupon->text?></p>
			<?php 
				$contact_types = array(1 => 'Тел.', 2 => 'Тел.', 5 => 'Email');
				foreach ($object->get_contacts() as $contact) : ?>
			<p><?=$contact_types[$contact->contact_type_id]?>: <?=Contact::format_phone($contact->contact)?></p>
			<?php endforeach; ?>			
				<p>Адрес: <?=strip_tags($attributes_values['adres-raion'])?></p>
			<p class="mb20"><?=strip_tags($attributes_values['adres-detail'])?></p>
			<p>Воспользоваться купоном можно до:</p>
			<p class="fw-b"><?=strip_tags($attributes_values['goden-do'])?></p>
		</div>
		<div class="fr w49p">
			<script src="http://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU&coordorder=latlong" type="text/javascript"></script>
			<script type="text/javascript">
				var map;
				var placemark;

				ymaps.ready(function(){
					map = new ymaps.Map("card-map", {
						center: [<?=$object->geo_loc?>],
						zoom: 14
					});

					map.controls.add('zoomControl', { top: 5, left: 5 });
					map.controls.add(new ymaps.control.TypeSelector(['yandex#map', 'yandex#satellite', 'yandex#hybrid', 'yandex#publicMap', 'yandex#publicMapHybrid']));

					placemark = new ymaps.Placemark([<?=$object->geo_loc?>], {
						},
						{
							draggable: false,
							iconImageHref: '/images/map_mark_active.png',
							iconImageSize: [47,47],
							iconImageOffset: [-15, -45],

							iconContentOffset: [],
							hintHideTimeout: 0

						});

					map.geoObjects.add(placemark);
				});

			</script>

			<div id="card-map" style="width: 100%; height: 200px;"></div>			
		</div>
	</div>
	<hr>
	<p class="mb10">Дополнительная информация:</p>
	<div class="text-cont mb15">
		<?=$attributes_values['support-info']?>
	</div>
	<hr class="mb15">
	<p class="ta-c fs16 color-gray mb10">Служба поддержки Ярмарка-Скидки:</p>
	<p class="ta-c fs16 color-gray">в Нижневартовске: +7(3466)29-28-77</p>
	<p class="ta-c fs16 color-gray">в Сургуте: +7(3462)21-92-77</p>
	<p class="ta-c fs16 color-gray">в Тюмени: +7(3452)49-21-21</p>
	<p class="ta-c fs16 color-gray">email: skidki@yarmarka.biz</p>
</div>