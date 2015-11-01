var bound, zoom;
var delayInterval;
// получаем query string
var qs = (function(a) {
    if (a == "") return {};
    var b = {};
    for (var i = 0; i < a.length; ++i)
    {
        var p=a[i].split('=');
        if (p.length != 2) continue;
        b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
    }
    return b;
})(window.location.search.substr(1).split('&'));
var myMap;
function initYaMaps() {
	var geo_loc = $('#map-initialization-info').data('geoloc').split(',');
	var zoom = $('#map-initialization-info').data('zoom');

	myMap = new ymaps.Map("ad-list-map", {
			center: geo_loc,
			zoom: zoom,
			behaviors: ['default']	
			// behaviors: ['default', 'scrollZoom']	
		});	
       
	myMap.controls.add('zoomControl', { top: 40, left: 8 });
	myMap.controls.add(new ymaps.control.TypeSelector(['yandex#map', 'yandex#satellite', 'yandex#hybrid', 'yandex#publicMap', 'yandex#publicMapHybrid']));
	myMap.controls.add('mapTools');
	myMap.controls.add('routeEditor');
	myMap.controls.add('scaleLine');
	myMap.controls.add(new ymaps.control.SearchControl(), {right: '90px', top: '6px'});
	
	myMap.events.add('boundschange', function (event) {
		if (delayInterval != null) clearTimeout(delayInterval);
		bound = event.get('newBounds');
		zoom = event.get('newZoom');

		if (event.get('newZoom') != event.get('oldZoom')) {
			// убираем предыдущие балуны если был изменен масштаб
			var iter = myMap.geoObjects.getIterator();
			var obj = null;
			while ((obj = iter.getNext()) != null)
			{
				myMap.geoObjects.remove(obj);
			}
		}
		// выводим новые
		delayInterval = setTimeout(function() { getDataFunction(myMap,bound, zoom); }, 100);
	});
	
	getDataFunction(myMap,myMap.getBounds(), myMap.getZoom());
}

function getDataFunction(myMap, bound, zoom) {
	if (false) {
		return getDataCities(myMap, bound, zoom);
	} else {
		return getData(myMap, bound, zoom);
	}
}

function getData(myMap,bound,zoom) {
	var url_hash = $('#map-initialization-info').data('hash');
	var url = "/ajax/map_get_bounds_for_search/"+url_hash+"?l="+bound[0][1]+"&t="+bound[0][0]+"&r="+bound[1][1]+"&b="+bound[1][0]+"&z="+zoom;

	timeElapsed = new Date();

	$.getJSON( url, function (data)
	{
		timeElapsed = new Date(new Date() - timeElapsed);
		//document.title = (timeElapsed.getSeconds() +":"+ timeElapsed.getMilliseconds());
		//надо гденить показать под картой за сколько загрузилось
		
		//alert(data.points.length);
		
		var iter = myMap.geoObjects.getIterator();
		var obj = null;
		var objects_to_remove = []; // старые объекты удалим когда уже нарисуем новые, чтобы не моргала карта
		while ((obj = iter.getNext()) != null)
		{
			// myMap.geoObjects.remove(obj);
			objects_to_remove[objects_to_remove.length] = obj;
		}
		if (data.points != null && data.points.length > 0)
		{
			for (var i = 0; i < data.points.length; i++)
			{
				var curData = data.points[i];
				var coords = curData.coordinates;
				var count = curData.count;
				var options = {};
				var param = {};

				if (qs.bound == curData.bound)
				{
					options = {
						iconImageHref: "/images/map_cluster_active.png",
						iconImageSize: [45, 44],
						iconImageOffset: [-20, -25],
						iconContentLayout: ymaps.templateLayoutFactory.createClass("<div style='width: 31px; height: 35px; text-align: center; font-weight:bold; margin-top:0px; font-size: 12px; line-height: 32px;'>$[properties.iconContent]</div>")
					};
					param = {
						iconContent: data.points[i].count
					};
				}
				else if (count !== "1")
				{
					options = {
						iconImageHref: "/images/map_cluster.png",
						iconImageSize: [45, 44],
						iconImageOffset: [-20, -25],
						iconContentLayout: ymaps.templateLayoutFactory.createClass("<div style='width: 31px; height: 35px; text-align: center; font-weight:bold; margin-top:0px; font-size: 12px; line-height: 32px;'>$[properties.iconContent]</div>")
					};

					var search_url = '';
					if (data.url.indexOf('?') == -1) {
						search_url = data.url+'?';
					} else {
						search_url = data.url+'&';
					}

					param = {
						iconContent: data.points[i].count,
						balloonContentHeader: "Найдено "+count+" объектов",
						balloonContentBody: "<a href='"+search_url+"bound="+curData.bound+"&zoom="+zoom+"&center="+myMap.getCenter()+"'>Показать</a>"
							 +"<br /><a href='"+search_url+"bound="+curData.bound+"&zoom="+zoom+"&center="+myMap.getCenter()+"&from_center="+curData.coordinates.join(',')+"'>По удаленности отсюда</a>"
						// balloonContentFooter: "Подвал",
						// hintContent: "Хинт метки"
					};
				}
				else
				{
					BalloonContentLayout = ymaps.templateLayoutFactory.createClass(
						'<div id="balloon_content" style=" margin: 10px;width: 400px; height: 120px;"></div>',
						{
							build: function (event) {
								BalloonContentLayout.superclass.build.call(this);
								$('#balloon_content').html('<div align="center"><img src="/images/loader.gif" align="center" /></div>');
								var data = this.getData();
								var local_bound = data.properties.get('bound');

								var url = "/ajax/map_get_one_bound_object?bound="+encodeURIComponent(local_bound)+"&z="+myMap.getZoom()+"&url_hash="+url_hash;
								
								$.getJSON(url, function (json) {
									var html = '<div style="height: 100%; max-width:120px;; float:left;">';
									html += '<p class="pb5 gray">'+json.object.date_tpl+'</p>';
									if (json.file) {
										
										html += '<img style="max-width:120px;margin-top: 8px;" src="/'+json.file+'" align="left" />';
									}
										
									html += '</div>';
									
									html += '<div style="float: left; height: 100%; padding-left: 10px; max-width: 250px">'
									html += '<a href="/detail/'+json.object.id+'" target="_blank">'+json.object.title+'</a>';
									html += '<p style="padding:10px 0px">'+json.object.short_text+'</p>';
									if (json.object.price_tpl) 
										html += '<p><b>'+json.object.price_tpl+' р.</b></p>';
									html += '</div>';
									
									
									$('#balloon_content').html(html);
								});
							}
						}
					);
					options = {
						iconImageHref: "/images/map_mark.png",
						iconImageSize: [47, 47],
						iconImageOffset: [-15, -45],
						balloonContentLayout: BalloonContentLayout,
						iconContentLayout: ymaps.templateLayoutFactory.createClass("<div style='text-align: center; font-weight:bold; color:#206396; margin-top:-2px; font-size: 12px; line-height: 32px;'>1</div>")
					};
				}
				
				param.bound = curData.bound;

					
				var myPlacemark = new ymaps.Placemark([coords[1], coords[0]], param, options);

				// @todo пока не понятно как дотянуться до html балуна чтобы менять его на лету
				// myPlacemark.events.add('mouseenter', function(event, var2){
					// console.log(event.get('target').options);
				// });

				// myPlacemark.events.add('click', function (e) {
				// 	e.preventDefault();
				// 	var object = e.get('target');
				// 	getPointData(object.properties.get('bound'), myMap.getZoom());
				// });
				
				myMap.geoObjects.add(myPlacemark);
			}
		}

		setTimeout(function(){
			for (var i = 0; i < objects_to_remove.length; i++)
			{
				myMap.geoObjects.remove(objects_to_remove[i]);
			}
		}, 100);
	});
}

function getDataCities(myMap,bound,zoom) {
	var url_hash = $('#map-initialization-info').data('hash');
	var url = "/ajax/map_get_city_bounds?l="+bound[0][1]+"&t="+bound[0][0]+"&r="+bound[1][1]+"&b="+bound[1][0]+"&z="+zoom+"&url_hash="+url_hash;

	timeElapsed = new Date();

	$.getJSON( url, function (data)
	{
		timeElapsed = new Date(new Date() - timeElapsed);
		
		var iter = myMap.geoObjects.getIterator();
		var obj = null;
		var objects_to_remove = []; // старые объекты удалим когда уже нарисуем новые, чтобы не моргала карта
		while ((obj = iter.getNext()) != null)
		{
			// myMap.geoObjects.remove(obj);
			objects_to_remove[objects_to_remove.length] = obj;
		}

		if (data.points != null && data.points.length > 0)
		{
			for (var i = 0; i < data.points.length; i++)
			{
				var curData = data.points[i];
				var coords = curData.coordinates;
				//alert(coords);
				var count = curData.count;
				var options = {};
				var param = {};
				var search_url = '';

				if (curData.main_city)
				{
					search_url = curData.url+"?bound="+curData.bound+"&zoom="+zoom+"&center="+myMap.getCenter();
					options = {
						iconImageHref: "/images/map_city_active.png",
						iconImageSize: [32, 30],
						iconImageOffset: [-20, -25],
						iconContentLayout: ymaps.templateLayoutFactory.createClass("<a href='"+search_url+"' style='display:block; width: 35px; height: 32px;'></a>"),
					};

					param = {
						hintContent : '<b>'+curData.title+'</b><br />нажмите, чтобы перейти на странциу города',
					};
				}
				else
				{
					search_url = curData.url+"?bound="+curData.bound+"&zoom="+zoom+"&center="+myMap.getCenter();
					options = {
						iconImageHref: "/images/map_city.png",
						iconImageSize: [32, 30],
						iconImageOffset: [-20, -25],
						iconContentLayout: ymaps.templateLayoutFactory.createClass("<a href='"+search_url+"' style='display:block; width: 35px; height: 32px;'></a>"),
					};

					param = {
						hintContent : '<b>'+curData.title+'</b><br />нажмите, чтобы перейти на странциу города',
					};
				}
					
				var myPlacemark = new ymaps.Placemark([coords[1], coords[0]], param, options);

				// myPlacemark.events.add('click', function (e) {
				// 	e.preventDefault();
				// 	var object = e.get('target');
				// 	getPointData(object.properties.get('bound'), myMap.getZoom());
				// });
				
				myMap.geoObjects.add(myPlacemark);
			}
		}

		setTimeout(function(){
			for (var i = 0; i < objects_to_remove.length; i++)
			{
				myMap.geoObjects.remove(objects_to_remove[i]);
			}
		}, 100);
	});
}

function getPointData(bound, zoom)
{
	var url = "/ajax/map_get_bound_objects?bound="+encodeURIComponent('"'+bound+'"')+"&z="+zoom;
	
	$.getJSON(url, function (data)
	{
		var res = document.getElementById("res");
		var html = "<table border=1>";
		for (var i = 0; i < data.length; i++)
		{
			var curData = data[i];
			html += "<tr>" + "<td>"+(i+1)+"</td>" + "<td>gid: " + curData.gid + "</td><td>Имя: " + curData.name + "</td><td>Геометрия: "+ curData.geom +"</td></tr>";
		}
		html += "</table>";
		res.innerHTML = html;
	}
	).fail(function( jqxhr, textStatus, error ) {
		var err = textStatus + ', ' + error;
		console.log("Request Failed: " + err);
	});
}
