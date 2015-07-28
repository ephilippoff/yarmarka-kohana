/*global define */
define([
    "templates",
    "jssorSlider",
    "partials/behaviors/favourite",
    "partials/behaviors/search"
], function (templates, jssorSlider, FavouriteBehavior, SearchBehavior) {
    "use strict";

     return Marionette.LayoutView.extend({

        behaviors: {
            FavouriteBehavior: {
                behaviorClass: FavouriteBehavior
            },
            SearchBehavior: {
                behaviorClass: SearchBehavior
            }
        },

        initialize: function() {
            this.bindUIElements();
        }
    });
});