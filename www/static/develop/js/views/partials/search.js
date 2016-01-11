/*global define */
define([
    "marionette",
    "templates",
    "views/partials/behaviors/favourite",
    "views/partials/behaviors/search",
    "views/partials/behaviors/ads",
    "base/utils"
], function (Marionette, templates, FavouriteBehavior, SearchBehavior, AdsBehavior, utils) {
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
            var s = this;
            this.bindUIElements();
            
            
            if (this.ui.map.length) {
                this.initMap(function(){
                    var map_height = s.ui.map.height();
                    var banners_height = s.ui.map.height();
                    var liders_height = s.ui.liders.height();
                    var commonHeight = map_height;
                    var maxOffsetY = _.max([banners_height, liders_height]) + map_height;
                    if (s.ui.liders.length == 0) {
                        s.fixBlock(s.ui.map.parent(), 0, 0 );
                    } else {
                        commonHeight +=liders_height;
                        s.fixBlock(s.ui.map.parent(), maxOffsetY, 0 );
                    }

                    if (s.ui.banners.length) {
                        if (s.ui.liders.length == 0) {
                            s.fixBlock(s.ui.banners, -map_height - 70, map_height + 20);
                        } else {
                            commonHeight +=banners_height;
                            s.fixBlock(s.ui.banners, map_height, map_height + 20);
                        }
                    }
                    s.commonHeight = commonHeight;

                });
                
            } else {
                var banners_height = s.ui.map.height();
                var liders_height = s.ui.liders.height();
                var commonHeight = banners_height;
                if (s.ui.banners.length) {
                    if (s.ui.liders.length == 0) {
                        s.fixBlock(s.ui.banners, 70, 20);
                    } else {
                        commonHeight += liders_height;
                        s.fixBlock(s.ui.banners, 0, 20);
                    }
                }
                s.commonHeight = commonHeight;
            }

            

            this.citySelect();
        },

        citySelect: function() {
            var city_id = utils.getRemembedCity();

        },

        initMap: function(success) {
            var mapObjects = [];
            success = success || function(){};
            if ($("#objects_for_map").length) {
                try {
                    mapObjects = JSON.parse($("#objects_for_map").text());
                } catch (e) {
                    mapObjects = [];
                }
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

                    if (objects[object_id] && objects[object_id].options) {
                        objects[object_id].options.set( app.map.getIconSettings("defTwitterActive") );
                    }
                });

                s.ui.tr.on("mouseleave", function(e){
                    e.preventDefault();
                    var object_id = $(e.currentTarget).data("id");
                    if (objects[object_id] && objects[object_id].options) {
                        objects[object_id].options.set( app.map.getIconSettings("defTwitter") );
                    }
                });
                success(map);
            });
        },

        fixBlock: function(elem, offsetY, topY) {
            if (elem.length == 0) return;
            var windowHeight = $(window).height(),
                documentHeight = $(document).height(),
                sidebar = $('.right-side'),
                sidebarHeight = sidebar.height(),
                sidebarTopY = sidebar.offset().top,
                sidebarRightX = sidebar.offset().left,
                sidebarWidth = sidebar.outerWidth(),
                mainBlockBottomY = documentHeight - ($('.search_wrap').offset().top + $('.search_wrap').outerHeight()),
                mainBlockTopY = $('.search_wrap').offset().top + $('.search_wrap').outerHeight(),
                scrollBottom,
                y0 = 0,
                myScreen = window.innerWidth;


            if (myScreen => 993) { // Если экран больше мобильного
                $(window).on('scroll', function(){
                    var y = $(document).scrollTop();
                    scrollBottom = documentHeight - $(window).scrollTop() - windowHeight;
                        if (y > y0) { //Если листаем вниз
                            if (documentHeight - (sidebarTopY + sidebarHeight) >= scrollBottom) {
                                if (sidebar.css('top') != 'auto' && sidebar.css('top') != '0px' && sidebar.css('position') == 'absolute') {
                                    var sidebarTopY2 = sidebar.offset().top;
                                    console.log(sidebarTopY2);
                                    sidebar.css({
                                        'position' : 'absolute',
                                        'top' : sidebarTopY2+'px'
                                    });
                                }
                                sidebar.css({
                                    'position' : 'fixed',
                                    'bottom' : '0px',
                                    'top' : 'auto',
                                    'left' : sidebarRightX+'px',
                                    'width' : sidebarWidth+'px'
                                });
                                y0 = y;
                                if (scrollBottom <= mainBlockBottomY) {
                                    sidebar.css({'position' : 'absolute', 'bottom' : -(mainBlockTopY - windowHeight)+'px'});
                                }
                            }
                        }
                        else{ //Если листаем вверх
                            if (y >= sidebarTopY) {
                                var sidebarTopY1 = $('.right-side').offset().top;
                                sidebar.css({
                                    'position' : 'absolute',
                                    'bottom' : 'auto',
                                    'top' : sidebarTopY1+'px'
                                });
                                y0 = y;
                                if (y <= sidebarTopY1) {
                                    sidebar.css({
                                        'position' : 'fixed',
                                        'top' : '0px'
                                    });
                                }
                            }else{
                                sidebar.css('position', 'static');
                            }
                        }
               });
            }
        }
    });
});