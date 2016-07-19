/*global define */
define([
    "templates",
    "views/partials/behaviors/favourite",
    "views/partials/behaviors/ads",
    "views/partials/behaviors/ocontrol",
    "views/partials/behaviors/services"

], function (templates, FavouriteBehavior, AdsBehavior, OControlBehavior, ServicesBehavior) {
    "use strict";


     return Marionette.LayoutView.extend({
        ui: {
            map: "#map"
        },

        behaviors: {
            FavouriteBehavior: {
                behaviorClass: FavouriteBehavior
            },
            AdsBehavior: {
                behaviorClass: AdsBehavior
            },
            OControlBehavior: {
                behaviorClass: OControlBehavior
            },
            ServicesBehavior: {
                behaviorClass: ServicesBehavior
            },
        },

        initialize: function() {
            this.bindUIElements();

            if (this.ui.map.length) {
                var lat = +this.ui.map.data("lat");
                var lon = +this.ui.map.data("lon");
                var baloonTemplate = templates.components.detail.baloon;
            
                app.map.getMap({ elid: "map", lat: lat, lon: lon, zoom: 15}, function(map){

                    var collection = new ymaps.GeoObjectCollection();

                    collection.add( app.map.createPlacemark([lat,lon], {
                        style: app.map.getIconSettings("house"),
                        content: {
                            hintContent: 'Расположение объекта'
                        }
                    }));

                    map.geoObjects.add(collection);
                    
                    map.setBounds(collection.getBounds(), {
                        checkZoomRange: true
                    });
                });
            }
        }
    });
});