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
        mapInstance: null,
        fullscreenControl: null,
        objectManager: null,
        ui: {
            tr:"tr.tr",
            map: ".js-map",
            mapWrapper: ".js-map-cont",
            mapCount: ".js-map-count",
            banners: ".js-banners",
            liders: ".js-liders",
            expandMapButton: '.js-map-expand',
            collapseMapButton: '.js-map-collapse'
            // button: '#map-button',
            // wrap: '#map-wrap'
        },
        events: {
            "mouseover @ui.tr": function(e) {
                e.preventDefault();
                $(e.currentTarget).addClass("hover");
            },
            "mouseleave @ui.tr": function(e) {
                 $(e.currentTarget).removeClass("hover");
            },
            "click @ui.button": function(e) {
                //this.buttonCheck();
            },
            "click @ui.expandMapButton": function(e) {
                this.expandMap();
            },
            "click @ui.collapseMapButton": function(e) {
                this.collapseMap();
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
            //this.buttonCheck();

            if (this.ui.map.length) {
                this.initMap();
            }
        },

        expandMap: function() {
            var s = this;

            var screen_width = document.documentElement.clientWidth;

            var is_mobile = (screen_width < 1000);

            ymaps.ready(function () {

                var objectManager = s.objectManager = new ymaps.ObjectManager({
                    clusterize: true,
                    gridSize: 32
                });

                objectManager.objects.options.set('preset', 'islands#greenDotIcon');
                objectManager.clusters.options.set('preset', 'islands#greenClusterIcons');

                s.mapInstance.geoObjects.add(objectManager);

                if (!is_mobile) {
                    s.ui.expandMapButton.addClass('hidden');
                    s.ui.mapWrapper.addClass('expanded');
                    s.ui.mapCount.removeClass('hidden');
                    s.ui.collapseMapButton.removeClass('hidden');
                } else {
                    
                    s.fullscreenControl.enterFullscreen();
                }

                $.ajax({
                    url: '/search_category/map',
                    type: 'POST',
                    data: { 
                        category_id: _globalSettings.category_id, 
                        city_id: _globalSettings.city_id,
                        search_filters: _globalSettings.search_filters
                    }, 
                    success: function (data) {
                        objectManager.add(data);
                    },
                    dataType:'json'
                });

            });
        },

        collapseMap: function() {

            this.ui.expandMapButton.removeClass('hidden');
            this.ui.mapWrapper.removeClass('expanded');
            this.ui.mapCount.addClass('hidden');
            this.ui.collapseMapButton.addClass('hidden');

            if (this.objectManager) {
                this.objectManager.removeAll();
                this.objectManager = null;
            }

        },

        initMap: function() {
            var mapObjects = [];

            var s = this;
            var lat = +this.ui.map.data("lat");
            var lon = +this.ui.map.data("lon");
            var baloonTemplate = templates.components.detail.baloon;

            ymaps.ready(function () {

                s.mapInstance = new ymaps.Map("map", {
                    center: [lat, lon],
                    zoom: 12,
                    controls: ["zoomControl"]
                });

                var fullscreenControl = new ymaps.control.FullscreenControl();
                s.fullscreenControl = fullscreenControl;
                s.mapInstance.controls.add(fullscreenControl);

            });


            //app.map.getMap({ elid: "map", lat: lat, lon: lon, zoom: 12}, function(map){
                

                // var collection =  new ymaps.GeoObjectCollection();
                // var objects = {};
                // _.each(mapObjects, function(item){
                //     if (!item.coords[0]) return;
                //     var placemark = app.map.createPlacemark([item.coords[0],item.coords[1]],{
                //         style: app.map.getIconSettings( (item.type) ? "defRed" : "defTwitter" ),
                //         content: {
                //             hintContent: item.title,
                //             balloonContent: _.template(baloonTemplate)(item)
                //         }
                //     });
                //     objects[item.id] = placemark;
                //     collection.add(placemark);
                // });
                // map.geoObjects.add(collection);
                
                // map.setBounds(collection.getBounds(), {
                //     checkZoomRange: true
                // });

                // s.ui.tr.on("mouseover", function(e){
                //     e.preventDefault();
                //     var object_id = $(e.currentTarget).data("id");

                //     if (objects[object_id] && objects[object_id].options) {
                //         objects[object_id].options.set( app.map.getIconSettings("defTwitterActive") );
                //     }
                // });

                // s.ui.tr.on("mouseleave", function(e){
                //     e.preventDefault();
                //     var object_id = $(e.currentTarget).data("id");
                //     if (objects[object_id] && objects[object_id].options) {
                //         objects[object_id].options.set( app.map.getIconSettings("defTwitter") );
                //     }
                // });

           // });
        },

        fixBlock: function(elem, offsetY, topY) {
            // if (elem.length == 0) return;
            // var windowHeight = $(window).height(),
            //     documentHeight = $(document).height(),
            //     sidebar = $('.right-side'),
            //     sidebarHeight = sidebar.height(),
            //     sidebarTopY = sidebar.offset().top,
            //     sidebarRightX = sidebar.offset().left,
            //     sidebarWidth = sidebar.outerWidth(),
            //     mainBlockBottomY = documentHeight - ($('.search_wrap').offset().top + $('.search_wrap').outerHeight()),
            //     mainBlockTopY = $('.search_wrap').offset().top + $('.search_wrap').outerHeight(),
            //     scrollBottom,
            //     y0 = 0,
            //     myScreen = window.innerWidth;


            // if (myScreen => 993) { // Если экран больше мобильного
            //     $(window).on('scroll', function(){
            //         var y = $(document).scrollTop();
            //         scrollBottom = documentHeight - $(window).scrollTop() - windowHeight;
            //             if (y > y0) { //Если листаем вниз
            //                 if (documentHeight - (sidebarTopY + sidebarHeight) >= scrollBottom) {
            //                     if (sidebar.css('top') != 'auto' && sidebar.css('top') != '0px' && sidebar.css('position') == 'absolute') {
            //                         var sidebarTopY2 = sidebar.offset().top;
            //                         console.log(sidebarTopY2);
            //                         sidebar.css({
            //                             'position' : 'absolute',
            //                             'top' : sidebarTopY2+'px'
            //                         });
            //                     }
            //                     sidebar.css({
            //                         'position' : 'fixed',
            //                         'bottom' : '0px',
            //                         'top' : 'auto',
            //                         'left' : sidebarRightX+'px',
            //                         'width' : sidebarWidth+'px'
            //                     });
            //                     y0 = y;
            //                     if (scrollBottom <= mainBlockBottomY) {
            //                         sidebar.css({'position' : 'absolute', 'bottom' : -(mainBlockTopY - windowHeight)+'px'});
            //                     }
            //                 }
            //             }
            //             else{ //Если листаем вверх
            //                 if (y >= sidebarTopY) {
            //                     var sidebarTopY1 = $('.right-side').offset().top;
            //                     sidebar.css({
            //                         'position' : 'absolute',
            //                         'bottom' : 'auto',
            //                         'top' : sidebarTopY1+'px'
            //                     });
            //                     y0 = y;
            //                     if (y <= sidebarTopY1) {
            //                         sidebar.css({
            //                             'position' : 'fixed',
            //                             'top' : '0px'
            //                         });
            //                     }
            //                 }else{
            //                     sidebar.css('position', 'static');
            //                 }
            //             }
            //    });
            // }
        }
    });
});