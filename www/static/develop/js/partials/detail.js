/*global define */
define([
    "templates",
    "jssorSlider",
    "partials/behaviors/favourite",
    "partials/behaviors/search",
    "partials/behaviors/ocontrol",
    "partials/behaviors/services"
], function (templates, jssorSlider, FavouriteBehavior, SearchBehavior, OControlBehavior, ServicesBehavior) {
    "use strict";

     return Marionette.LayoutView.extend({

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
            }
        },

        initialize: function() {
            this.bindUIElements();
        }
    });
});