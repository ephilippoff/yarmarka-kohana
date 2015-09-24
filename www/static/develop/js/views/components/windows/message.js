/*global define */
define([
    'marionette',
    'templates'
], function (Marionette, templates) {
    'use strict';

    return Marionette.ItemView.extend({
        template: templates.components.windows.message,
        className: "popup-wrp z400",
        errors: {},
        ui: {
            ok: ".js-ok"
        },

        events: {
            'click @ui.ok': 'ok',
        },

        templateHelpers: {
            getTitle: function () { return this.context.getOption("title");},
            getText: function () { return this.context.getOption("text");},
            getDisableOk: function () { return this.context.getOption("disableOk");}
        },

        initialize: function(options) {
            this.bindUIElements();
        },

        serializeData: function(){
            var data = Backbone.Marionette.ItemView.prototype.serializeData.apply(this, arguments);
            data.context = this;
            return data;
        },

        ok: function (e) {
            e.preventDefault();
            app.windows.vent.trigger("closeWindow","message");
        }
    });

});