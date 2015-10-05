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
            price: ".js-price"
        },
        events: {
            "change @ui.quantity": "changeQuantity"
        },
        templateHelpers: function() {
            var s = this;
            return {
               getPrice: function() {
                    var info = s.model.get("info");
                    if (info.service) {
                        return info.service.price + " руб.";
                       
                    } else if (info.services){
                        return "(?)";
                    }
                
               },
               getCount: function(){
                 var info = s.model.get("info");
                 if (info.object) {
                     return 1;
                 } else if (info.objects) {
                     return info.objects.length;
                 }
               },
               getTitle: function(){
                    var info = s.model.get("info");
                    if (info.object) {
                        return "Услуга 'Лидер' для объявления '" + info.object.title + "'";
                    } else if (info.objects) {
                        return "Услуга 'Лидер' для "+info.objects.length+" объявлений(ия)";
                    }
               }
            }
        },
        changeQuantity: function() {
            var quantity = this.ui.quantity.val();
            var result = {
                quantity: quantity
            }
            
            if (this.model.get("info").service) {

                var price = this.model.get("info").service.price;
                var quantity = this.ui.quantity.val();
                var result = {
                    quantity: quantity,
                    sum: price * quantity
                }

                result.sum = price * quantity;
               
                if (quantity > 1) {
                    this.ui.price.text(price * quantity + " руб.");
                } else {
                    this.ui.price.text(price + " руб.");
                }
            }

            this.model.set("result", result);
        },
        onRender:function() {
            this.bindUIElements();
            this.changeQuantity();
        },
        check: function() {
            return false;
        }
    });
});