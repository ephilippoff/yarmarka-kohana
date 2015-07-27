function init_userpage_map(ymaps) {
	var coord = $.trim($('#coord').val());
	var org_address = $('#org_address').val();

	if (coord) { // если есть координаты
		coord = coord.split(',');
		init_map(ymaps);
	} else { // если нет, то пытаемся найти по адресу
		var myGeocoder = ymaps.geocode(org_address, { results: 1, json: true });
		myGeocoder.then(
			function(res) {
				if (res.GeoObjectCollection.featureMember.length == 0) {
					$('#ymaps-map-id').hide();
				} else {

					var points = res.GeoObjectCollection.featureMember[0].GeoObject.Point.pos.split(" ");
					coord = [Number(points[0]), Number(points[1])];
					init_map(ymaps);
				}
			},
			function(err) {
				alert('Не удалось определить координаты');
			}
		);
	}

	function init_map(ymaps) {
		var map = new ymaps.Map("ymaps-map-id", {
			center: coord,
			zoom: 14,
			type: "yandex#map"
		});

		map.controls.add("zoomControl", {top:45, left:8})
			.add("mapTools")
			.add(new ymaps.control.TypeSelector(["yandex#map", "yandex#satellite", "yandex#hybrid", "yandex#publicMap"]));


		var placemark = new ymaps.Placemark(coord, {},
		{
			draggable: false,
			iconImageHref: '/images/house.png',
			iconImageSize: [27,39],
			iconImageOffset: [-13.5, -39],

			iconContentOffset: [],
			hintHideTimeout: 0

		});

		map.geoObjects.add(placemark);
	}

}
function init_user_profile_map(ymaps) {
	var coord = $.trim($('#coord').val());
	if ( ! coord.length) {
		coord = '70.013552,57.649009'; // тюменская область
	}
	coord = coord.split(',');

	var zoom = 8;
	var org_address = $.trim($('input[name=org_address]').val());
	if (org_address == 'Введите улицу') {
		org_address = '';
	}
	if (org_address) {
		zoom = 14;
	}
	var map = new ymaps.Map("ymaps-map-id", {
		center: coord,
		zoom: zoom,
		type: "yandex#map"
	});

	map.controls.add("zoomControl", {top:45, left:8})
		.add("mapTools")
		.add(new ymaps.control.TypeSelector(["yandex#map", "yandex#satellite", "yandex#hybrid", "yandex#publicMap"]));


	var placemark = new ymaps.Placemark(coord, {},
	{
		draggable: true,
		iconImageHref: '/images/house.png',
		iconImageSize: [27,39],
		iconImageOffset: [-13.5, -39],

		iconContentOffset: [],
		hintHideTimeout: 0

	});

	placemark.events.add('dragend', function (e) {
		$('#coord').val(placemark.geometry.getCoordinates());
	});

	map.events.add('click', function (e) {
		placemark.geometry.setCoordinates(e.get('coordPosition'));
		$('#coord').val(placemark.geometry.getCoordinates());
	});

	map.geoObjects.add(placemark);
	$('#coord').val(placemark.geometry.getCoordinates());

	$('#detect_geoloc').click(function(e){
		e.preventDefault();
		detect_geoloc();
	});

	$('#coord').change(function(){
		var geoloc = $(this).val().split(',');
		zoom = 8;

		var org_address = $.trim($('input[name=address]').val());
		if (org_address == 'Введите улицу') {
			org_address = '';
		}
		if (org_address) {
			zoom = 14;
		}

		// ставим placemark
		placemark.geometry.setCoordinates(geoloc);
		// центруем карту
		map.setCenter(geoloc, zoom);
	});
}

function detect_geoloc() {
	var geoloc = [];
	var address = '';

	address = $('#city_selector').val();

	var org_address = $.trim($('input[name=address]').val());
	if (org_address == 'Введите улицу') {
		org_address = '';
	}
	if (org_address) {
		address += ','+$('[name=address]').val();
	}

	var myGeocoder = ymaps.geocode(address, { results: 1, json: true });
	myGeocoder.then(
		function(res) {
			if (res.GeoObjectCollection.featureMember.length == 0) {
				// alert('Не удалось определить координаты');
			} else {
				var gobj = res.GeoObjectCollection.featureMember[0].GeoObject;
				var points = gobj.Point.pos.split(" ");

				geoloc = [Number(points[0]), Number(points[1])];
				// сохраняем координаты в input и запускаем триггер
				$('#coord').val(geoloc).change(); 
			}
		},
		function(err) {
			alert('Не удалось определить координаты');
		}
	);
}