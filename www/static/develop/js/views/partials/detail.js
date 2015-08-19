/*global define */
define([
    "templates",
    "views/partials/behaviors/favourite",
    "views/partials/behaviors/search",
    "views/partials/behaviors/ocontrol",
    "views/partials/behaviors/services",
    "views/partials/behaviors/ads"
], function (templates, FavouriteBehavior, SearchBehavior, OControlBehavior, ServicesBehavior, AdsBehavior) {
    "use strict";


     return Marionette.LayoutView.extend({
        ui: {
            map: "#map"
        },
        behaviors: {
            FavouriteBehavior: {
                behaviorClass: FavouriteBehavior
            },
            SearchBehavior: {
                behaviorClass: SearchBehavior
            },
            OControlBehavior: {
                behaviorClass: OControlBehavior
            },
            ServicesBehavior: {
                behaviorClass: ServicesBehavior
            },
            AdsBehavior: {
                behaviorClass: AdsBehavior
            }
        },

        initialize: function() {
            this.bindUIElements();

            var similarObjects = [];
            try {
                similarObjects = JSON.parse(app.settings.objects_for_map);
            } catch (e) {
                similarObjects = [];
            }
            var lat = +this.ui.map.data("lat");
            var lon = +this.ui.map.data("lon");
            var baloonTemplate = templates.components.detail.baloon;
            app.map.getMap({ elid: "map", lat: lat, lon: lon, zoom: 12}, function(map){
                var collection = new ymaps.GeoObjectCollection();

                collection.add( app.map.createPlacemark([lat,lon], {
                    style: app.map.getIconSettings("house"),
                    content: {
                        hintContent: 'Расположение объекта'
                    }
                }));

                _.each(similarObjects, function(item){
                    if (!item.coords[0]) return;
                    var placemark = app.map.createPlacemark([item.coords[0],item.coords[1]],{
                        style: app.map.getIconSettings("defTwitter"),
                        content: {
                            hintContent: item.title,
                            balloonContent: _.template(baloonTemplate)(item)
                        }
                    });
                    collection.add(placemark);
                });

                map.geoObjects.add(collection);
                
                map.setBounds(collection.getBounds(), {
                    checkZoomRange: true
                });
            });
        }
    });
});