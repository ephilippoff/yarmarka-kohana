/*global define */
define([
    "marionette",
    "backbone",
    "templates"
], function (Marionette, Backbone, templates) {
    'use strict';

    return Marionette.ItemView.extend({
        template: templates.components.services.lider,
        ui: {
            quantity: ".js-quantity",
            price: ".js-price",
            emQuantity: ".js-em-quantity",
            emPrice: ".js-em-price",
        },
        events: {
            "change @ui.quantity": "changeLiderQuantity",
            "change @ui.emQuantity": "changeEmailQuantity"
        },

        modelEvents: {
            "change": "saveResult"
        },

        initialize: function() {
            this.saveResult();
        },

        changeLiderQuantity: function() {
            this.model.set('liderQuantity', +this.ui.quantity.val());
            this.ui.price.text( this.model.getAmount('lider') );
        },

        changeEmailQuantity: function() {
            this.model.set('emailQuantity', +this.ui.emQuantity.val());
            this.ui.emPrice.text( this.model.getAmount('email') );
        },

        templateHelpers: function() {
            var s = this;
            return {
                model: function() {
                    return s.model;
                }
            };
        },

        saveResult: function() {
            var result = {
               'lider' : {
                    quantity: this.model.get('liderQuantity'),
               }
               
            };
            if (!this.model.get('is_edit')) {
                result['email'] = { quantity: this.model.get('emailQuantity')}
            }
            this.model.set("result", result);
            this.model.set("urlRoot", '/rest_service/save_service');
        },

        resultValid: function() {
            return this.model.get('liderQuantity') > 0;
        }

        // templateHelpers: function() {
        //     var s = this;
        //     return {
        //        getPrice: function() {
        //             var info = s.model.get("info");
        //             if (info.service) {
        //                 return info.service.price + " руб.";
                       
        //             } else if (info.services){
        //                 return "(?)";
        //             }
                
        //        },
        //        getCount: function(){
        //          var info = s.model.get("info");
        //          if (info.object) {
        //              return 1;
        //          } else if (info.objects) {
        //              return info.objects.length;
        //          }
        //        },
        //        getTitle: function(){
        //             var info = s.model.get("info");
        //             if (info.object) {
        //                 return "Услуга 'Лидер' для объявления '" + info.object.title + "'";
        //             } else if (info.objects) {
        //                 return "Услуга 'Лидер' для "+info.objects.length+" объявлений(ия)";
        //             }
        //        }
        //     }
        // },
        // changeQuantity: function() {
        //     var quantity = this.ui.quantity.val();
        //     var result = {
        //         quantity: quantity
        //     }
            
        //     if (this.model.get("info").service) {

        //         var price = this.model.get("info").service.price;
        //         var quantity = this.ui.quantity.val();
        //         var result = {
        //             quantity: quantity,
        //             sum: price * quantity
        //         }

        //         result.sum = price * quantity;
               
        //         if (quantity > 1) {
        //             this.ui.price.text(price * quantity + " руб.");
        //         } else {
        //             this.ui.price.text(price + " руб.");
        //         }
        //     }

        //     this.model.set("result", result);
        // },
        // onRender:function() {
        //     this.bindUIElements();
        //     this.changeQuantity();
        // },
        // check: function() {
        //     return false;
        // }
    });
});