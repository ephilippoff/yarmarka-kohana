/*global define */
define([
    "marionette",
    "templates",
    "jssorSlider",
    "views/partials/behaviors/favourite",
    "views/partials/behaviors/search"
], function (Marionette, templates, jssorSlider, FavouriteBehavior, SearchBehavior) {
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