/*global define */
define([
    "marionette",
    "templates",
    "views/partials/behaviors/favourite",
    "views/partials/behaviors/search",
    "views/partials/behaviors/ads"
], function (Marionette, templates, FavouriteBehavior, SearchBehavior, AdsBehavior) {
    "use strict";

    return Marionette.LayoutView.extend({
        ui: {
            tr:"tr.tr",
            map: "#map",
            banners: ".js-banners",
            liders: ".js-liders"
        },
        events: {
            "mouseover @ui.tr": function(e) {
                e.preventDefault();
                $(e.currentTarget).addClass("hover");
            },
            "mouseleave @ui.tr": function(e) {
                 $(e.currentTarget).removeClass("hover");
            }
        },
        behaviors: {
            FavouriteBehavior: {
                behaviorClass: FavouriteBehavior
            },
            SearchBehavior: {
                behaviorClass: SearchBehavior
            },
            AdsBehavior: {
                behaviorClass: AdsBehavior
            }
        },

        initialize: function() {
            this.bindUIElements();
            if (this.ui.map.length) {
                this.initMap();
                var maxOffsetY = _.max([this.ui.banners.height(), this.ui.liders.height()]) + this.ui.map.height();
                if (this.ui.liders.length == 0) {
                    this.fixBlock(this.ui.map.parent(), 0, 0 );
                } else {
                    this.fixBlock(this.ui.map.parent(), maxOffsetY, 0 );
                }
            }

            if (this.ui.banners.length) {
                if (this.ui.liders.length == 0) {
                    this.fixBlock(this.ui.banners, -this.ui.map.height() - 70, this.ui.map.height() + 10);
                } else {
                    this.fixBlock(this.ui.banners, this.ui.map.height(), this.ui.map.height() + 10);
                }
            }
        },

        initMap: function() {
            var mapObjects = [];
            try {
                mapObjects = JSON.parse(app.settings.objects_for_map);
            } catch (e) {
                mapObjects = [];
            }
            var s = this;
            var lat = +this.ui.map.data("lat");
            var lon = +this.ui.map.data("lon");
            var baloonTemplate = templates.components.detail.baloon;
            app.map.getMap({ elid: "map", lat: lat, lon: lon, zoom: 10}, function(map){
                var collection =  new ymaps.GeoObjectCollection();
                var objects = {};
                _.each(mapObjects, function(item){
                    if (!item.coords[0]) return;
                    var placemark = app.map.createPlacemark([item.coords[0],item.coords[1]],{
                        style: app.map.getIconSettings( (item.type) ? "defRed" : "defTwitter" ),
                        content: {
                            hintContent: item.title,
                            balloonContent: _.template(baloonTemplate)(item)
                        }
                    });
                    objects[item.id] = placemark;
                    collection.add(placemark);
                });
                map.geoObjects.add(collection);
                
                map.setBounds(collection.getBounds(), {
                    checkZoomRange: true
                });

                s.ui.tr.on("mouseover", function(e){
                    e.preventDefault();
                    var object_id = $(e.currentTarget).data("id");
                    objects[object_id].options.set( app.map.getIconSettings("defTwitterActive") );
                });

                s.ui.tr.on("mouseleave", function(e){
                    e.preventDefault();
                    var object_id = $(e.currentTarget).data("id");
                    objects[object_id].options.set( app.map.getIconSettings("defTwitter") );
                });
            });
        },

        fixBlock: function(elem, offsetY, topY) {
            if (elem.length == 0) return;
            var topFix = elem.offset().top;

            $(window).on('scroll', function(){
                if ((topFix + offsetY - $(window).scrollTop()) <= 0) {
                    if (elem.css('position') != 'fixed') {
                        elem.hide().fadeIn();
                    }
                    elem.css('position','fixed').css('top', topY+'px');
                } else {
                    elem.css('position', 'inherit').css('top', 'inherit')
                }
           });
        }
    });
});