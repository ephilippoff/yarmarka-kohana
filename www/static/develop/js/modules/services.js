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
            app.settings.khQuery = true;
            serviceModel.save({
                id: id
            }, {
                success: function(model) {
                    app.settings.khQuery = false;
                    var resp = model.toJSON();
                    options.success();
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
                }, error: function() {
                    app.settings.khQuery = false;
                    options.error();
                }
            });
        },

        kuponGroup: function(id, groupId, options) {
            
            var serviceModel = new ServiceModel();
            serviceModel.urlRoot = "/rest_service/check_kupon";
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            app.settings.khQuery = true;
            serviceModel.save({
                id: id
            }, {
                success: function(model) {
                    app.settings.khQuery = false;
                    var resp = model.toJSON(),
                        result = {};
                    
                    result.info = resp;

                    var group = _.findWhere(result.info.groups, {id: groupId});

                    if (options.justCheck) {
                        if (group && group.available) {
                            options.success();
                        } else {
                            options.error();
                        }
                    } else {

                        result.result = {
                           quantity: 1,
                           sum: group.service.price,
                           id: groupId
                        };
                        _.extend(result.info, group);


                        var key = $.cookie("cartKey");
                        if (!key) {
                            var session_id = $.cookie("yarmarka");
                            var key = (session_id + Date.now());
                            $.cookie("cartKey", key, { expires: 7, path: '/', domain: '.'+app.settings.mainHost})
                        }
                        console.log(result);
                        var addServiceModel = new ServiceModel();
                        addServiceModel.urlRoot = "/rest_service/save";
                        app.settings.khQuery = true;
                        addServiceModel.save({serviceData : result, key : key},{
                            success: function(model) {
                                app.settings.khQuery = false;
                                var resp = model.toJSON();
                                if (resp.code == 200) {
                                    app.services.updateCart();
                                    options.success();
                                    $(window).attr("location", "/cart");
                                } else {
                                    options.error();
                                    console.log("Ошибка при сохранении услуги");
                                }
                            }, 
                            error: function(text) {
                                app.settings.khQuery = false;
                                s.getOption("error")("Ошибка при сохранении услуги");
                                console.log("Ошибка при сохранении услуги");
                            }
                        })
                    }


                }, error: function() {
                    app.settings.khQuery = false;
                    options.error();
                }
            });
        },
    });

});
