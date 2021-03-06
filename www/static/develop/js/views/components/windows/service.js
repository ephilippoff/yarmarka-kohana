/*global define */
define([
    'backbone',
    'marionette',
    'templates',
    'jcookie'
], function (Backbone, Marionette, templates) {
    'use strict';

    var ServiceModel = Backbone.Model.extend({
        urlRoot: "/rest_service/save"
    });

    return Marionette.LayoutView.extend({
        template: templates.components.windows.service,
        className: "popup-wrp z400",
        errors: {},
        ui: {
            close: ".js-close",
            tocontinue: ".js-tocontinue",
            tocart: ".js-tocart",
            took: ".js-ok",
            form: ".js-form"
        },

        events: {
            "click @ui.tocontinue": "save",
            "click @ui.took": "save",
            "click @ui.tocart": "saveAndRedirect",
            "click @ui.close": "close"
        },

        regions : {
            service: ".js-service-cont"
        },

        close: function() {
            app.windows.vent.trigger("closeWindow","service");
        },

        templateHelpers: function() {
            var s = this;
            return {
               getTitle: function(){
                    return s.options.title;
               }
            }
        },

        initialize: function(options) {
            if (options.is_edit) {
                this.template = templates.components.windows.editService;
            }

            this.model = new ServiceModel({
                title: options.title,
                code: options.code,
                is_edit: options.is_edit,
                edit_params: options.edit_params,
                parentView: this.$el
            });
        },

        onRender: function() {
            this.service.show(this.getOption("serviceView"));
        },

        saveAndRedirect: function(e) {
            e.preventDefault();
            var s = this;
            s.save(e, true);
        },

        save: function(e, redirect) {
            e.preventDefault();
            var s = this;
            var view = this.service.currentView;
            if (view.resultValid && !view.resultValid()) {
                return;
            }
            var serviceModel = new ServiceModel();

            var serviceData = view.model.toJSON();
            serviceModel.urlRoot = serviceData.urlRoot || serviceModel.urlRoot;

            if (this.getOption("is_edit")) {
                serviceData.temp_order_item_id = this.getOption("is_edit");
            }
            
            serviceModel.save({serviceData : serviceData},{
                success: function(model) {
                    var resp = model.toJSON();
                    if (resp.code == 200) {
                        app.services.updateCart();
                        app.windows.vent.trigger("closeWindow","service");
                        s.getOption("success")(model);
                        if (redirect) {
                            $(window).attr("location", "/cart");
                        }
                    } else {
                        s.getOption("error")("Ошибка при сохранении услуги");
                        console.log("Ошибка при сохранении услуги");
                    }
                }, 
                error: function(text) {
                    s.getOption("error")("Ошибка при сохранении услуги");
                    console.log("Ошибка при сохранении услуги");
                }
            })

            // (this.getOption("success")) ? this.getOption("success")(): function (){}();
        }
    });

});