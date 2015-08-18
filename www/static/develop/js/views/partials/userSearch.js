/*global define */
define([
    "marionette",
    "templates",
    "views/partials/behaviors/favourite",
    "views/partials/behaviors/search",
    "views/partials/behaviors/ocontrol",
    "views/partials/behaviors/services"
], function (Marionette, templates, FavouriteBehavior, SearchBehavior, OControlBehavior, ServicesBehavior) {
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
            "mouseout @ui.tr": function(e) {
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