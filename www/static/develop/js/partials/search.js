/*global define */
define([
    "marionette",
    "templates",
    "jssorSlider",
    "partials/behaviors/favourite"
], function (Marionette, templates, jssorSlider, FavouriteBehavior) {
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