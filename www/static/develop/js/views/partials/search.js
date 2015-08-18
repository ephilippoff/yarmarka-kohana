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
            tr:"tr.tr"
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
        }
    });
});