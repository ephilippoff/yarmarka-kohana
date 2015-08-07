/*global define */
define([
    'backbone',
    'marionette',
    'templates'
], function (Backbone, Marionette, templates) {
    'use strict';

    var ServiceModel = Backbone.Model.extend({
        urlRoot: "/rest_service/save"
    });

    return Marionette.LayoutView.extend({
        template: templates.components.windows.service,
        className: "popup-wrp z1500",
        errors: {},
        ui: {
            close: ".js-close",
            tocontinue: ".js-tocontinue",
            tocart: ".js-tocart",
            form: ".js-form"
        },

        events: {
            "click @ui.tocontinue": "save",
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
               
            }
        },

        initialize: function(options) {
            this.model = new ServiceModel({
                title: options.title,
                code: options.code
            });
        },

        onRender: function() {
            this.service.show(this.getOption("serviceView"));
        },

        saveAndRedirect: function(e) {
            e.preventDefault();
            this.save(e);
            $(window).attr("location", "/cart");
        },

        save: function(e) {
            e.preventDefault();
            
            var serviceModel = new ServiceModel();
            var serviceData = this.service.currentView.model.toJSON();
            serviceModel.save({serviceData : serviceData},{
                success: function(model) {
                    var resp = model.toJSON();
                    if (resp.code == 200) {
                        app.services.updateCart();
                        app.windows.vent.trigger("closeWindow","service");
                    }
                }, 
                error: function() {

                }
            })

            // (this.getOption("success")) ? this.getOption("success")(): function (){}();
        }
    });

});