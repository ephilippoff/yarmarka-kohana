/*global define */
define([
    "templates",
    "isotope",
    "views/partials/behaviors/search",
    "views/partials/behaviors/ads"
], function (templates, Isotope, SearchBehavior, AdsBehavior) {
    "use strict";


     return Marionette.LayoutView.extend({

        behaviors: {
            SearchBehavior: {
                behaviorClass: SearchBehavior
            },
            AdsBehavior: {
                behaviorClass: AdsBehavior
            }
        },

        initialize: function() {
            this.bindUIElements();
            var s = this;

            this.changeSizes();
            
            this.iso = new Isotope(  document.querySelector('.js-promo-thumbnails'), {
                // options
                itemSelector: 'img',
                layoutMode: 'masonry',
                isOriginLeft: false,
                isOriginTop: false,
                masonry: {
                  columnWidth: 50,
                  gutter: 1
                },
                getSortData: {
                    active: '[data-weight] parseInt'
                },
                //sortBy: 'weight'
            });

            this.changeSizes(true);
            this.iso.layout();
            this.promoTimer = this.startPromo();
            $(".js-promo-thumbnails").on("mouseover", function(){
                clearInterval(s.promoTimer);
            });
            $(".js-promo-thumbnails").on("mouseout", function(){
                 s.promoTimer = s.startPromo();
            });
        },

        startPromo: function() {
            var s = this;
            return setInterval(function(){
               s.changeSizes();
               s.iso.layout();
            }, 5000);
        },

        changeSizes: function(show) {
            var sizes = [50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150, 160, 170, 180];
            _.each($('.js-promo-thumbnails img'), function(item){
                var width = sizes[_.random(0,12)];
                $(item).width(width);
                if (show) {
                     $(item).fadeIn();
                }
            });
        }
    });
});