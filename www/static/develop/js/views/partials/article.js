/*global define */
define([
    "templates",
    "views/partials/behaviors/favourite",
    "views/partials/behaviors/ads",
    "views/partials/behaviors/ocontrol",
    "views/partials/behaviors/services",
    "gisMap"

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
                app.map.get2GisMap({ elid: "map", lat: lat, lon: lon, zoom: 12}, function(map){

                    DG.marker([lat,lon]).addTo(map).bindPopup('Расположение объекта');
                    
                });
            }
        }
    });
});