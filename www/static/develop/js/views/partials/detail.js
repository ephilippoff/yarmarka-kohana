/*global define */
define([
    "templates",
    "views/partials/behaviors/services"
], function (templates, ServicesBehavior) {
    "use strict";


     return Marionette.LayoutView.extend({
        ui: {
        },
        events: {
            "click @ui.backcallButton": "backcallButtonClick"
        },
        behaviors: {
            ServicesBehavior: {
                behaviorClass: ServicesBehavior
            }
        },

        initialize: function() {
            this.bindUIElements();

           
        },
    });
});