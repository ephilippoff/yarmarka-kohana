/*global define */
define([
    "marionette",
    "backbone",
    "templates"
], function (Marionette, Backbone, templates) {
    'use strict';

    return Marionette.ItemView.extend({
        template: templates.components.services.email,
        ui: {
            quantity: ".js-quantity",
            price: ".js-price",
        },
        events: {
            "change @ui.quantity": "changeQuantity"
        },

        modelEvents: {
            "change": "saveResult"
        },

        initialize: function() {
            this.saveResult();
        },

        changeQuantity: function() {
            this.model.set('quantity', +this.ui.quantity.val());
            this.ui.price.text( this.model.getAmount() );
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
               'email' : {
                    quantity: this.model.get('quantity'),
               }
               
            };
            this.model.set("result", result);
            this.model.set("urlRoot", '/rest_service/save_service');
        },

        resultValid: function() {
            return this.model.get('quantity') > 0;
        }
    });

});