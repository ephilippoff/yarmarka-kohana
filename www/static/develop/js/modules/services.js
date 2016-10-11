/*global define */
define([
    "marionette",
    "backbone",
    "templates",
    "views/components/services/up",
    "views/components/services/premium",
    "views/components/services/lider",
    "views/components/services/kupon",
    "views/components/services/newspaper",
    "views/components/services/cities",
    "views/components/services/email",
    "views/components/services/tglink"
], function (Marionette, 
            Backbone, 
            templates,
            ServiceUpView, 
            ServicePremiumView, 
            ServiceLiderView, 
            KuponView,
            NewspaperView, 
            CitiesView, 
            ServiceEmailView,
            ServiceTglinkView
        ) {
    'use strict';

    var CartModel = Backbone.Model.extend({});
    var ServiceModel = Backbone.Model.extend({

        getQuantity: function(service) {
            return this.get('quantity');
        },

        getCountObjects: function() {
            return this.get("info").objects.length;
        },

        getTitle: function(){
             var info = this.get("info");
             if (this.getCountObjects() == 1) {
                 return "для объявления '" + info.object.title + "'";
             } else {
                 return "для "+this.getCountObjects()+" объявлений(ия)";
             }
        },

        getAmount: function() {

            var quantity = this.getQuantity(this.serviceName);
            var price = parseFloat(this.get("info").services[this.serviceName].price);

            return (this.getCountObjects() == 1) ? (price * quantity + " руб.") : "(?)";
        }
    });


    var PremiumModel = ServiceModel.extend({
        urlRoot: "/rest_service/save_premium",
        serviceName: 'premium',
        getQuantity: function(service) {
            return this.get(service + 'Quantity');
        },

        getAmount: function(service) {

            var quantity = this.getQuantity(service);

            if (this.getCountObjects() == 1) {
                var price = parseFloat(this.get("info").services[service].price);
                var discount_reason = this.get("info").services[service].discount_reason;
                var discount_name = this.get("info").services[service].discount_name;

                if (quantity > 1) {
                    return price * quantity + " руб.";
                } else {
                    if (discount_name) {
                        return discount_reason;
                    } else {
                        return price * quantity + " руб.";
                    }
                }
            } else {

                if (quantity > 1) {
                   return "(?)";
                } else {
                   return (this.getCountObjects() > this.get("info").count) ? "(?)" : "(бесплатно)";
                }

            }
        }

    });

    var EmailModel = ServiceModel.extend({
        serviceName: 'email',
        urlRoot: "/rest_service/save_email"
    });

    var LiderModel = PremiumModel.extend({
        serviceName: 'lider',
        urlRoot: "/rest_service/save_lider",

        //  getAmount: function(service) {

        //     var quantity = this.getQuantity(service);
        //     var price = parseFloat(this.get("info").services[service].price);

        //     return (this.getCountObjects() == 1) ? (price * quantity + " руб.") : "(?)";
        // }
    });

    var TglinkModel = ServiceModel.extend({
        serviceName: 'tglink',
        urlRoot: "/rest_service/save_email",

        updatePrice: function(data, cb) {
            var s = this;

            $.post(
                '/rest_service/check_tglink/'+this.get('id'), 
                data , 
                function (data) {
                    
                    s.set('quantity', data.services[s.serviceName].quantity);
                    cb(data.services[s.serviceName].price_total);
                });
        },

        getAmount: function() {
            return this.get('info').services[this.serviceName].price_total + " руб.";
        }
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
                            model: new PremiumModel({
                                info: resp,
                                is_edit: options.is_edit,
                                premiumQuantity: (options.is_edit) ? options.edit_params.service.quantity : 1,
                                emailQuantity: (options.is_edit) ? options.edit_params.service.quantity : 1
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
                            model: new LiderModel({
                                info: resp,
                                is_edit: options.is_edit,
                                liderQuantity: (options.is_edit) ? options.edit_params.service.quantity : 1,
                                emailQuantity: (options.is_edit) ? options.edit_params.service.quantity : 1
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
        cities: function(id, options) {
            
            var serviceModel = new ServiceModel();
            serviceModel.urlRoot = "/rest_service/check_cities";
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            serviceModel.save({
                id: id,
                ids: options.ids
            }, {
                success: function(model) {
                    var resp = model.toJSON();

                    app.windows.vent.trigger("showWindow", "service", {
                        title: "Услуга - В несколько городов",
                        serviceView : new CitiesView({
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

                    if (options.justCheck) {
                        var group = _.findWhere(resp.groups, {id: options.group});
                       if (group && group.available) {
                           options.success();
                       } else {
                           options.error();
                       }
                    } else {
                        options.success();
                        app.windows.vent.trigger("showWindow", "service", {
                            title: resp.object.title,
                            serviceView : new KuponView({
                                model: new ServiceModel({
                                    info: resp,
                                    is_edit: options.is_edit,
                                    edit_params: options.edit_params,
                                    group: options.group
                                })
                            }),
                            code: resp.code,
                            success: options.success,
                            error: options.error,
                            is_edit: options.is_edit
                        });
                    }
                }, error: function() {
                    options.error();
                }
            });
        },
        email: function(id, options) {
            
            var serviceModel = new ServiceModel();
            serviceModel.urlRoot = "/rest_service/check_email";
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            serviceModel.save({
                id: id,
                ids: options.ids
            }, {
                success: function(model) {
                    var resp = model.toJSON();

                    app.windows.vent.trigger("showWindow", "service", {
                        title: "Услуга 'E-mail маркетинг'",
                        serviceView : new ServiceEmailView({
                            model: new EmailModel({
                                info: resp,
                                is_edit: options.is_edit,
                                quantity: (options.is_edit) ? options.edit_params.service.quantity : 1,
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
        tglink: function(id, options) {
            
            var serviceModel = new ServiceModel();
            serviceModel.urlRoot = "/rest_service/check_tglink";
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            serviceModel.save({
                id: id,
                ids: options.ids
            }, {
                success: function(model) {
                    var resp = model.toJSON();

                    app.windows.vent.trigger("showWindow", "service", {
                        title: "Услуга 'Размещение тексто-графической ссылки'",
                        serviceView : new ServiceTglinkView({
                            model: new TglinkModel({
                                id: id,
                                info: resp,
                                is_edit: options.is_edit,
                                quantity: (options.is_edit) ? options.edit_params.service.quantity : 1,
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

        // kuponGroup: function(id, groupId, options) {
            
        //     var serviceModel = new ServiceModel();
        //     serviceModel.urlRoot = "/rest_service/check_kupon";
        //     options.error = options.error || function() {};
        //     options.success = options.success || function() {};
        //     app.settings.khQuery = true;
        //     serviceModel.save({
        //         id: id
        //     }, {
        //         success: function(model) {
        //             app.settings.khQuery = false;
        //             var resp = model.toJSON(),
        //                 result = {};
                    
        //             result.info = resp;

        //             var group = _.findWhere(result.info.groups, {id: groupId});

        //             if (options.justCheck) {
        //                 if (group && group.available) {
        //                     options.success();
        //                 } else {
        //                     options.error();
        //                 }
        //             } else {

        //                 result.result = {
        //                    quantity: 1,
        //                    sum: group.service.price,
        //                    id: groupId
        //                 };
        //                 _.extend(result.info, group);


        //                 var key = $.cookie("cartKey");
        //                 if (!key) {
        //                     var session_id = $.cookie("yarmarka");
        //                     var key = (session_id + Date.now());
        //                     $.cookie("cartKey", key, { expires: 7, path: '/', domain: '.'+app.settings.mainHost})
        //                 }
        //                 console.log(result);
        //                 var addServiceModel = new ServiceModel();
        //                 addServiceModel.urlRoot = "/rest_service/save";
        //                 app.settings.khQuery = true;
        //                 addServiceModel.save({serviceData : result, key : key},{
        //                     success: function(model) {
        //                         app.settings.khQuery = false;
        //                         var resp = model.toJSON();
        //                         if (resp.code == 200) {
        //                             app.services.updateCart();
        //                             options.success();
        //                             $(window).attr("location", "/cart");
        //                         } else {
        //                             options.error();
        //                             console.log("Ошибка при сохранении услуги");
        //                         }
        //                     }, 
        //                     error: function(text) {
        //                         app.settings.khQuery = false;
        //                         s.getOption("error")("Ошибка при сохранении услуги");
        //                         console.log("Ошибка при сохранении услуги");
        //                     }
        //                 })
        //             }


        //         }, error: function() {
        //             app.settings.khQuery = false;
        //             options.error();
        //         }
        //     });
        // },
    });

});
