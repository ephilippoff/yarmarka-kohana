/*global define */
define([
    "marionette",
    "backbone",
    "templates"
], function (Marionette, Backbone, templates) {
    'use strict';

    var CartModel = Backbone.Model.extend({});
    var ServiceModel = Backbone.Model.extend({});

    var ServiceUpView = Marionette.ItemView.extend({
        template: templates.components.services.up,
        ui: {
            quantity: ".js-quantity",
            price: ".js-price"
        },
        events: {
            "change @ui.quantity": "changeQuantity"
        },
        changeQuantity: function() {
            var price = this.model.get("info").service.price;
            var available = this.model.get("info").available;
            var quantity = this.ui.quantity.val();
            var result = {
                quantity: quantity,
                sum: price * quantity
            }
            this.model.set("result", result);
            if (quantity > 1) {
                this.ui.price.text(price * quantity + " руб.");
            } else {
                if (available) {
                    this.ui.price.text("Бесплатно");
                } else {
                    this.ui.price.text(price + " руб.");
                }
            }
        },
        onRender:function() {
            this.bindUIElements();
            this.changeQuantity();
        },
        check: function() {
            return false;
        }
    });

    var ServicePremiumView = Marionette.ItemView.extend({
        template: templates.components.services.premium,
        ui: {
            quantity: ".js-quantity",
            price: ".js-price"
        },
        events: {
            "change @ui.quantity": "changeQuantity"
        },
        changeQuantity: function() {
            var price = parseFloat(this.model.get("info").service.price);
            var available = this.model.get("info").available;
            var quantity = parseInt(this.ui.quantity.val());
            var result = {
                quantity: parseInt(quantity),
                sum: parseFloat(price * quantity)
            }
            this.model.set("result", result);
            if (quantity > 1) {
                this.ui.price.text(price * quantity + " руб.");
            } else {
                if (available) {
                    this.ui.price.text("Бесплатно");
                } else {
                    this.ui.price.text(price + " руб.");
                }
            }
        },
        onRender:function() {
            this.bindUIElements();
            this.changeQuantity();
        },
        check: function() {
            return false;
        }
    });

    var ServiceLiderView = Marionette.ItemView.extend({
        template: templates.components.services.lider,
        ui: {
            quantity: ".js-quantity",
            price: ".js-price"
        },
        events: {
            "change @ui.quantity": "changeQuantity"
        },
        changeQuantity: function() {
            var price = this.model.get("info").service.price;
            var quantity = this.ui.quantity.val();
            var result = {
                quantity: quantity,
                sum: price * quantity
            }
            this.model.set("result", result);
            if (quantity > 1) {
                this.ui.price.text(price * quantity + " руб.");
            } else {
                this.ui.price.text(price + " руб.");
            }
        },
        onRender:function() {
            this.bindUIElements();
            this.changeQuantity();
        },
        check: function() {
            return false;
        }
    });

    var ServiceBuyObjectView = Marionette.ItemView.extend({
        template: templates.components.services.buyObject
    });

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
                id: id
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
                id: id
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
                id: id
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
        object: function(id, options) {
            
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
