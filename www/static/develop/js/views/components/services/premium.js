/*global define */
define([
    "marionette",
    "backbone",
    "templates"
], function (Marionette, Backbone, templates) {
    'use strict';

    return Marionette.ItemView.extend({
        template: templates.components.services.premium,
        ui: {
            quantity: ".js-quantity",
            price: ".js-price",
            emQuantity: ".js-em-quantity",
            emPrice: ".js-em-price",
            
        },
        events: {
            "change @ui.quantity": "changePremiumQuantity",
            "change @ui.emQuantity": "changeEmailQuantity"
        },

        modelEvents: {
            "change": "saveResult"
        },

        initialize: function() {
            this.saveResult();
        },

        changePremiumQuantity: function() {
            this.model.set('premiumQuantity', +this.ui.quantity.val());
            this.ui.price.text( this.model.getAmount('premium') );
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
               'premium' : {
                    quantity: this.model.get('premiumQuantity'),
               }
               
            };
            if (!this.model.get('is_edit')) {
                result['email'] = { quantity: this.model.get('emailQuantity')}
            }
            this.model.set("result", result);
            this.model.set("urlRoot", '/rest_service/save_service');
        },

        resultValid: function() {
            return this.model.get('premiumQuantity') > 0;
        }
    });

});