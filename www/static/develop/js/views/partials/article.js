/*global define */
define([
    "templates",
    "views/partials/behaviors/favourite",
    "views/partials/behaviors/ads",
    "views/partials/behaviors/ocontrol",
    "views/partials/behaviors/services",

], function (templates, FavouriteBehavior, AdsBehavior, OControlBehavior, ServicesBehavior) {
    "use strict";


     return Marionette.LayoutView.extend({

        behaviors: {
            FavouriteBehavior: {
                behaviorClass: FavouriteBehavior
            },
            AdsBehavior: {
                behaviorClass: AdsBehavior
            },
            OControlBehavior: {
                behaviorClass: OControlBehavior
            },
            ServicesBehavior: {
                behaviorClass: ServicesBehavior
            },
        },

        initialize: function() {
            this.bindUIElements();
        }
    });
});