/*global define */
define([
    "templates",
    "views/partials/behaviors/favourite",
    "views/partials/behaviors/search",
    "views/partials/behaviors/ocontrol",
    "views/partials/behaviors/services",
    "views/partials/behaviors/ads",
    'modules/writeToAuthor/main',
    'modules/vkMessagesNotifier/main',
    'modules/lastViews/main',
    'modules/complain/main'
], function (templates, FavouriteBehavior, SearchBehavior, OControlBehavior, ServicesBehavior, AdsBehavior, WriteToAuthor, VkMessagesNotifier, LastViewsMainView, Complain) {
    "use strict";


     return Marionette.LayoutView.extend({
        ui: {
            map: "#map",
            backcallButton: ".js-backcall-button",
            showMapButton: ".js-show-map"
        },
        events: {
            "click @ui.backcallButton": "backcallButtonClick",
            "click @ui.showMapButton": "showMapButtonClick"
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

            /* initialize last views module */

            if ($('.last-views').length) {
                this.lastViewsModule = new LastViewsMainView({
                    el: $('.last-views')
                });
            }
            /* initialize last views module done */

            if (!this.ui.showMapButton.length) {

               this.initMap();
            }

            /* initialize write to author widget */
            var $temp = this.$el.find('[data-role=writeToAuthor]');
            if ($temp.length) {
                new WriteToAuthor({
                    el: $temp
                }).render();
            }

            /* initialize vk messages notifier */
            if (typeof(VK) !== 'undefined') {
                new VkMessagesNotifier();
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
        },

        showMapButtonClick: function(e) {

            var top = $('#flag').offset().top;
            var s = this;
            $('body,html').animate({scrollTop: top-50}, 750);

                $('#map-cont').slideDown();

                s.initMap();

            $(this).remove();
        },

        initMap: function() {
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
    });
});

