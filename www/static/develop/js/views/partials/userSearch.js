/*global define */
define([
    "marionette",
    "templates",
    "jssorSlider",
    "views/partials/behaviors/favourite",
    "views/partials/behaviors/search",
    "views/partials/behaviors/ocontrol",
    "views/partials/behaviors/services"
], function (Marionette, templates, jssorSlider, FavouriteBehavior, SearchBehavior, OControlBehavior, ServicesBehavior) {
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