/*global define */
define([
    'backbone',
    'marionette',
    'templates',
    'base/validation'
], function (Backbone, Marionette, templates, validation) {
    'use strict';

    var ModerateModel = Backbone.Model.extend({
        urlRoot: "/rest_object/moderate",
        settings: {
            type : ["require"],
            comment : ["require"]
        },
        defaults: {
            type : null,
            comment : null
        },

        validate: function (attrs, options) {
            var s = this, result, errors = [];
            _.each(_.keys(attrs), function(item){
                result = validation.isValid(s.settings[item], item, attrs[item]);
                if (result) {
                    errors.push(result);
                }
            });
            return (errors.length) ? errors : false;
        }
    });

    return Marionette.ItemView.extend({
        template: templates.components.windows.moderate,
        className: "popup-wrp z400",
        errors: {},
        ui: {
            close: ".js-close",
            took: ".js-ok",
            form: ".js-form",
            reasonList: ".js-reason",
            comment: ".js-comment"
        },

        events: {
            "click @ui.took": "save",
            "click @ui.close": "close",
            "change @ui.reasonList": "changeReasonList"
        },

        close: function() {
            app.windows.vent.trigger("closeWindow","moderate");
        },

        changeReasonList: function(e) {
            this.ui.comment.val($(e.currentTarget).val());
        },

        templateHelpers: function() {
            var s = this;
            return {
               getError: function(name){
                    return (_.has(s.errors, name) && s.errors[name]) ? s.errors[name] : false;
                }
            }
        },

        initialize: function(options) {
            this.model = new ModerateModel({
                object_id: options.id,
                reasons: options.reasons,
                params: options.params
            });
        },

        save: function(e) {
            e.preventDefault();
            var s = this, f = {};
            this.ui.form.serializeArray().map(function(x){f[x.name] = x.value;});
            this.model.set(f);
            if ( !this.model.isValid() ) {
                s.errors = {};
                _.each(this.model.validationError, function(item, key){
                    s.errors[item.name] = item.text;
                });
                this.render();
            } else {
               this.model.save({},{
                    success: function(model) {
                        var resp = model.toJSON();
                        if (resp.code == 200) {
                            s.close();
                           app.windows.vent.trigger("showWindow","message",{
                               title: "Модерация",
                               text: "Успешно"
                           });
                        } else {
                            app.windows.vent.trigger("showWindow","message",{
                                title: "Модерация",
                                text: "Ошибка"
                            });
                        }
                    },
                    error: function(model) {
                         app.windows.vent.trigger("showWindow","message",{
                            title: "Модерация",
                            text: "Ошибка"
                        });
                    }
               });
            }

        }
    });

});