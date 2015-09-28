/*global define */
define([
    "marionette",
    "backbone",
    "templates",
    "views/components/services/up",
    "views/components/services/premium",
    "views/components/services/lider",
    "views/components/services/kupon",
    "views/components/services/newspaper"
], function (Marionette, Backbone, templates, ServiceUpView, ServicePremiumView, ServiceLiderView, KuponView, NewspaperView) {
    'use strict';

    var CartModel = Backbone.Model.extend({});
    var ServiceModel = Backbone.Model.extend({});

    return Marionette.Module.extend({
        initialize: function()
        {
            // var key = $.cookie("cartKey");
            // if (key) {
            //     updateCart();
            //     setInterval(function() {
            //         updateCart();
            //     }, 10000);
            // }
        },
        updateCart: function(){
            var cartModel = new CartModel();
            cartModel.urlRoot = "/rest_service/cart_count";
            cartModel.save({},
                {
                    success: function(model) {
                        var resp = model.toJSON();
                        $(".js-cart-counter").text(resp.count);
                        if (resp.sum) {
                            $(".js-cart-summ").text(resp.sum + " руб.");
                        }
                    }
                }
            );
        },
        up: function(id, options) {
            
            var serviceModel = new ServiceModel();
            serviceModel.urlRoot = "/rest_service/check_freeup";
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            serviceModel.save({
                id: id,
                ids: options.ids
            }, {
                success: function(model) {
                    var resp = model.toJSON();

                    app.windows.vent.trigger("showWindow", "service", {
                        title: "Услуга поднятия объявления",
                        serviceView : new ServiceUpView({
                            model: new ServiceModel({
                                info: resp,
                                is_edit: options.is_edit,
                                edit_params: options.edit_params
                            })
                        }),
                        code: resp.code,
                        success: options.success,
                        error: options.error,
                        is_edit: options.is_edit
                    });
                }
            });
        },
        premium: function(id, options) {
            
            var serviceModel = new ServiceModel();
            serviceModel.urlRoot = "/rest_service/check_premium";
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            serviceModel.save({
                id: id,
                ids: options.ids
            }, {
                success: function(model) {
                    var resp = model.toJSON();

                    app.windows.vent.trigger("showWindow", "service", {
                        title: "Услуга - премиум объявление",
                        serviceView : new ServicePremiumView({
                            model: new ServiceModel({
                                info: resp,
                                is_edit: options.is_edit,
                                edit_params: options.edit_params
                            })
                        }),
                        code: resp.code,
                        success: options.success,
                        error: options.error,
                        is_edit: options.is_edit
                    });
                }
            });
        },
        lider: function(id, options) {
            
            var serviceModel = new ServiceModel();
            serviceModel.urlRoot = "/rest_service/check_lider";
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            serviceModel.save({
                id: id,
                ids: options.ids
            }, {
                success: function(model) {
                    var resp = model.toJSON();

                    app.windows.vent.trigger("showWindow", "service", {
                        title: "Услуга - объявление 'Лидер'",
                        serviceView : new ServiceLiderView({
                            model: new ServiceModel({
                                info: resp,
                                is_edit: options.is_edit,
                                edit_params: options.edit_params
                            })
                        }),
                        code: resp.code,
                        success: options.success,
                        error: options.error,
                        is_edit: options.is_edit
                    });
                }
            });
        },
        newspaper: function(id, options) {
            
            var serviceModel = new ServiceModel();
            serviceModel.urlRoot = "/rest_service/check_newspaper";
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            serviceModel.save({
                id: id,
                ids: options.ids
            }, {
                success: function(model) {
                    var resp = model.toJSON();

                    app.windows.vent.trigger("showWindow", "service", {
                        title: "Услуга - Объявление в газету",
                        serviceView : new NewspaperView({
                            model: new ServiceModel({
                                info: resp,
                                is_edit: options.is_edit,
                                edit_params: options.edit_params,
                                city_id: options.city_id
                            })
                        }),
                        code: resp.code,
                        success: options.success,
                        error: options.error,
                        is_edit: options.is_edit
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
                        serviceView : new KuponView({
                            model: new ServiceModel({
                                info: resp,
                                is_edit: options.is_edit,
                                edit_params: options.edit_params
                            })
                        }),
                        code: resp.code,
                        success: options.success,
                        error: options.error,
                        is_edit: options.is_edit
                    });
                }
            });
        },
        kupon: function(id, options) {
            
            var serviceModel = new ServiceModel();
            serviceModel.urlRoot = "/rest_service/check_kupon";
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            serviceModel.save({
                id: id
            }, {
                success: function(model) {
                    var resp = model.toJSON();
                    app.windows.vent.trigger("showWindow", "service", {
                        title: resp.object.title,
                        serviceView : new KuponView({
                            model: new ServiceModel({
                                info: resp,
                                is_edit: options.is_edit,
                                edit_params: options.edit_params
                            })
                        }),
                        code: resp.code,
                        success: options.success,
                        error: options.error,
                        is_edit: options.is_edit
                    });
                }
            });
        }
    });

});
