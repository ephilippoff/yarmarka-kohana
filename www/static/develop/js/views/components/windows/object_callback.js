/*global define */
define([
    'backbone',
    'marionette',
    'templates',
    'base/validation'
], function (Backbone, Marionette, templates, validation) {
    'use strict';

    var ObjectCallbackModel = Backbone.Model.extend({
        urlRoot: "/rest_object/callback"
    });

    return Marionette.ItemView.extend({
        template: templates.components.windows.object_callback,
        className: "popup-wrp z400",
        errors: {},
        ui: {
            close: ".js-close",
            took: ".js-ok",
            form: ".js-form"
        },

        events: {
            "click @ui.took": "save",
            "click @ui.close": "close"
        },

        close: function() {
            app.windows.vent.trigger("closeWindow","object_callback");
        },

        initialize: function(options) {
            this.model = new ObjectCallbackModel({
                ids: (options.ids) ? options.ids : [options.id]
            });
        },

        save: function(e) {
            e.preventDefault();
            var s = this, f = {};
            this.ui.form.serializeArray().map(function(x){f[x.name] = x.value;});
            if (!f.reason) {
                return;
            }
            this.model.set(f);

            this.model.save({},{
                success: function(model) {
                    var resp = model.toJSON();
                    if (resp.code == 200) {
                        s.close();
                        app.windows.vent.trigger("showWindow","message",{
                            title: "Снятие объявлений",
                            text: "Спасибо! За то что воспользовались нашими услугами"
                        });
                    } else {
                        s.errors["common"] = "Ошибка при сохранении";
                        s.render();
                    }
                },
                error: function(model) {
                    s.errors["common"] = "Ошибка при сохранении";
                    s.render();
                }
            });

        }
    });

});