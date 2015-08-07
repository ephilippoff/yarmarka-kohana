/*global define */
define([
    "marionette",
    "backbone",
    "templates"
], function (Marionette, Backbone, templates) {
    'use strict';

    var ServiceModel = Backbone.Model.extend({

    });

    var ServiceUpView = Marionette.ItemView.extend({
        template: templates.components.services.up
    });

    var ServiceBuyObjectView = Marionette.ItemView.extend({
        template: templates.components.services.buyObject
    });

    return Marionette.Module.extend({
        up: function(id, options) {
            
            var serviceModel = new ServiceModel();
            serviceModel.urlRoot = "/rest_service/check_freeup";
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            serviceModel.save({
                id: id
            }, {
                success: function(model) {
                    var resp = model.toJSON();

                    app.windows.vent.trigger("showWindow", "service", {
                        title: "Услуга поднятия объявления",
                        serviceView : new ServiceUpView({
                            model: new ServiceModel({
                                info: resp
                            })
                        }),
                        code: resp.code,
                        success: options.success,
                        error: options.error
                    });
                }
            });
        },
        buyObject: function(id, options) {
            
            var serviceModel = new ServiceModel();
            serviceModel.urlRoot = "/rest_service/check_buy_object";
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            serviceModel.save({
                id: id
            }, {
                success: function(model) {
                    var resp = model.toJSON();
                    app.windows.vent.trigger("showWindow", "service", {
                        title: resp.object.title,
                        serviceView : new ServiceBuyObjectView({
                            model: new ServiceModel({
                                info: resp
                            })
                        }),
                        code: resp.code,
                        success: options.success,
                        error: options.error
                    });
                }
            });
        }
    });

});
