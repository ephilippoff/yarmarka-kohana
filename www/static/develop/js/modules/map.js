/*global define */
define([
    'marionette',
    'ymap'
], function (Marionette) {
    'use strict';

    var iconSettings = {
        house: {
            iconLayout: 'default#image',
            iconImageHref: '/static/develop/images/house.png',
            iconImageSize: [27,39],
            iconImageOffset: [-13.5, -39],
            zIndex: 1000
        },
        defBlue: {
            preset: 'islands#icon',
            iconColor: '#2D5169'
        },
        defRed: {
            preset: 'islands#icon',
            iconColor: '#D30000'
        },
        defTwitter: {
            preset: 'islands#circleDotIcon',
            iconColor: '#1faee9'
        }
    }

    return Marionette.Module.extend({
        initialize: function() {
            ymaps.ready(function () {
                console.log("ymap ready");
            });
        },

        getIconSettings: function(name) {
            return iconSettings[name];
        },

        getMap: function(options, ready) {

            var clusterer,
                elid = options.elid,
                lat = options.lat || 55.76,
                lon = options.lon || 37.64,
                zoom = options.zoom || 10,
                settings = options.settings || {};

            ymaps.ready(function () {
                var map = new ymaps.Map(elid, _.extend({
                    center: [lat, lon],
                    zoom: zoom,
                    controls: ['smallMapDefaultSet']
                }, settings));

                ready(map);
            });
        },

        destroyMap: function(map) {
            map.destroy();
            map = null;
        },

        setCenter: function (map, lat, lon) {
             map.setCenter([lat, lon]);
        },

        createPlacemark: function (coords, options) {
            options = options || {};
            return new ymaps.Placemark(coords, options.content, options.style);
        },

        setOptimalBounds: function(map) {
            map.setBounds(map.getBounds(), {
                checkZoomRange: true
            });
        },

        getUserLocation: function(callback) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var location = {
                        lat: position.coords.latitude,
                        lon: position.coords.longitude
                    };
                    callback(location);
                });
            }
        },

        geocode: function(address, options){
            options.error = options.error || function(){};
            var geocoder = ymaps.geocode(address, {
                results: 1
            }).then(
                function (result) {
                    options.success(result.geoObjects.get(0));
                },
                function (err) {
                    options.error();
                }
            );
        }
    });
});