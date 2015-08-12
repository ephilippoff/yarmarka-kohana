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