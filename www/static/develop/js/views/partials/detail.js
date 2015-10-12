/*global define */
define([
    "templates",
    "views/partials/behaviors/services",
    "views/partials/behaviors/comments"
], function (templates, ServicesBehavior, CommentsBehavior) {
    "use strict";


     return Marionette.LayoutView.extend({
        ui: {
        },
        events: {
            
        },
        behaviors: {
            ServicesBehavior: {
                behaviorClass: ServicesBehavior
            },
            CommentsBehavior: {
                behaviorClass: CommentsBehavior
            }
        },

        initialize: function() {
            this.bindUIElements();

        },
    });
});