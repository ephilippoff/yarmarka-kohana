/*global define */
define([
    "marionette",
    "backbone",
    "templates"
], function (Marionette, Backbone, templates) {
    'use strict';

    return Marionette.ItemView.extend({
        template: templates.components.services.up,
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
                        if (!info.available) { 
                            return info.service.price + " руб.";
                        } else {
                            return info.service.discount_reason;
                        }
                    } else if (info.services){

                        return (info.services.length > info.count) ? "(?)" : "(бесплатно)";
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
                        return "Услуга 'Подъем' для объявления '" + info.object.title + "'";
                    } else if (info.objects) {
                        return "Услуга 'Подъем' для "+info.objects.length+" объявлений(ия)";
                    }
               },
               getNextFreeUp: function(){
                    var next_freeup = s.model.get("info").service.next_freeup;
                    if (next_freeup) {
                        return "( следующий <b>" + next_freeup + "</b> )";
                    } else {
                        return "";
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
                var discount_reason = this.model.get("info").service.discount_reason;
                var available = this.model.get("info").available;
                
                result.sum = price * quantity;

                if (quantity > 1) {
                    this.ui.price.text(price * quantity + " руб.");
                } else {
                    if (available) {
                        this.ui.price.text(discount_reason);
                    } else {
                        this.ui.price.text(price + " руб.");
                    }
                }
            } else if (this.model.get("info").services) {
                console.log(this.model.get("info"))
                if (quantity > 1) {
                    this.ui.price.text("(?)");
                } else {
                    this.ui.price.text((this.model.get("info").services.length > this.model.get("info").count) ? "(?)" : "(бесплатно)");
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