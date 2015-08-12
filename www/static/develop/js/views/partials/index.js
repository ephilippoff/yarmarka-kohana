/*global define */
define([
    "templates",
    "views/partials/behaviors/search",
    "views/partials/behaviors/ads"
], function (templates, SearchBehavior, AdsBehavior) {
    "use strict";


     return Marionette.LayoutView.extend({

        behaviors: {
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