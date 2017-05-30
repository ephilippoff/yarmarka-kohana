/*global define */
define([
    "marionette",
    "./behaviors/user/change-email"
], function (Marionette, ChangeEmailBehavior) {
    "use strict";

    return Marionette.LayoutView.extend({
        behaviors: {
            ChangeEmailBehavior: {
                behaviorClass: ChangeEmailBehavior
            },
        },

        initialize: function() {
            this.bindUIElements();
        }
    });
});