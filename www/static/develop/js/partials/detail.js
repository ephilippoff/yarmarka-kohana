/*global define */
define([
    "templates",
    "jssorSlider",
    "partials/behaviors/favourite"
], function (templates, jssorSlider, FavouriteBehavior) {
    "use strict";

     return Marionette.LayoutView.extend({

        behaviors: {
            FavouriteBehavior: {
                behaviorClass: FavouriteBehavior
            }
        },

        initialize: function() {
            this.bindUIElements();
        }
    });
});