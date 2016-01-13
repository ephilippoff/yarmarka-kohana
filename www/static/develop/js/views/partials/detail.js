/*global define */
define([
    "templates",
    "views/partials/behaviors/favourite",
    "views/partials/behaviors/search",
    "views/partials/behaviors/ocontrol",
    "views/partials/behaviors/services",
    "views/partials/behaviors/ads",
    "gisMap",
    'modules/writeToAuthor/main',
    'modules/complain/main'
], function (templates, FavouriteBehavior, SearchBehavior, OControlBehavior, ServicesBehavior, AdsBehavior, gisMap, WriteToAuthor, Complain) {
    "use strict";


     return Marionette.LayoutView.extend({
        ui: {
            map: "#map",
            backcallButton: ".js-backcall-button"
        },
        events: {
            "click @ui.backcallButton": "backcallButtonClick"
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

            if (this.ui.map.length) {
                var similarObjects = [];
                if ($("#objects_for_map").length) {
                    try {
                        similarObjects = JSON.parse($("#objects_for_map").text());
                    } catch (e) {
                        similarObjects = [];
                    }
                }
                var lat = +this.ui.map.data("lat");
                var lon = +this.ui.map.data("lon");
                var baloonTemplate = templates.components.detail.baloon;
                app.map.get2GisMap({ elid: "map", lat: lat, lon: lon, zoom: 15}, function(map){

                    DG.marker([lat,lon]).addTo(map).bindPopup('Расположение объекта');
                    
                    // var collection = new ymaps.GeoObjectCollection();

                    // collection.add( app.map.createPlacemark([lat,lon], {
                    //     style: app.map.getIconSettings("house"),
                    //     content: {
                    //         hintContent: 'Расположение объекта'
                    //     }
                    // }));

                    // _.each(similarObjects, function(item){
                    //     if (!item.coords[0]) return;
                    //     var placemark = app.map.createPlacemark([item.coords[0],item.coords[1]],{
                    //         style: app.map.getIconSettings("defTwitter"),
                    //         content: {
                    //             hintContent: item.title,
                    //             balloonContent: _.template(baloonTemplate)(item)
                    //         }
                    //     });
                    //     collection.add(placemark);
                    // });

                    // map.geoObjects.add(collection);
                    
                    // map.setBounds(collection.getBounds(), {
                    //     checkZoomRange: true
                    // });
                });
            }

            /* initialize write to author widget */
            var $temp = this.$el.find('[data-role=writeToAuthor]');
            if ($temp.length) {
                new WriteToAuthor({
                    el: $temp
                }).render();
            }

            /* initialize complain widget */
            $temp = this.$el.find('[data-role=complain]');
            if ($temp.length) {
                new Complain({
                    el: $temp
                }).render();
            }
        },

        backcallButtonClick: function(e) {
            e.preventDefault();
            var object_id = $(e.currentTarget).data("id");
            app.windows.vent.trigger("showWindow","backcall", {object_id: object_id, key: "kupon"});
        }
    });
});