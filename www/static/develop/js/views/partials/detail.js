/*global define */
define([
    "templates",
    "views/partials/behaviors/favourite",
    "views/partials/behaviors/search",
    "views/partials/behaviors/ocontrol",
    "views/partials/behaviors/services",
    "views/partials/behaviors/ads"
], function (templates, FavouriteBehavior, SearchBehavior, OControlBehavior, ServicesBehavior, AdsBehavior) {
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