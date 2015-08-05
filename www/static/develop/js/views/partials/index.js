/*global define */
define([
    "templates",
    "jssorSlider",
    "views/partials/behaviors/search"
], function (templates, jssorSlider, SearchBehavior) {
    "use strict";

     return Marionette.LayoutView.extend({

        behaviors: {
            SearchBehavior: {
                behaviorClass: SearchBehavior
            }
        },

        initialize: function() {
            this.bindUIElements();
        }
    });
});