/*global define */
define([
    "marionette",
    "backbone",
    "templates"
], function (Marionette, Backbone, templates) {
    'use strict';

    return Marionette.ItemView.extend({
        template: templates.components.services.tglink,
        ui: {
            image: ".js-tg-image",
            block: ".js-tg-block",
            text: ".js-tg-text",
            quantity: ".js-quantity",
            price: ".js-price",
            form: ".tg-form",
            example: ".js-example"
        },
        events: {
            //"change @ui.image": "changed",
            "keyup @ui.text": "changeText",
            "change @ui.block": "changed",
            "change @ui.quantity": "changed",
            //"change @ui.quantity": "changeQuantity"
        },

        modelEvents: {
            "change": "saveResult"
        },

        initialize: function() {
            this.saveResult();
            //this.changed();
        },

        formSerialize: function() {
            var f = {};
            $(this.ui.form).serializeArray().map(function(x){f[x.name] = x.value;});
            return f;
        },

        changed: function() {
            var s = this;
            this.model.updatePrice(this.formSerialize(), function(price){
                s.ui.price.text( price  + 'руб.' );
            });
        },

        changeText: function() {
            var s = this;
            this.ui.example.text( this.ui.text.val() ? this.ui.text.val() : "Пример" );
            Array.from(this.ui.example).forEach(function(item){
                console.log()
                item.style.top = (25 - (s.ui.example.height()/2 || 10) ) + 'px';
            })
            
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
               'tglink' : {
                    quantity: this.model.get('quantity'),
                    category: this.model.getCategory(),
                    image: this.formSerialize()['image'],
                    text: $(this.ui.text).val()
               }
               
            };
            this.model.set("result", result);
            this.model.set("urlRoot", '/rest_service/save_service');
        },

        onRender: function() {
            this.changeText();
        },

        resultValid: function() {
            return this.model.get('quantity') > 0;
        }
    });

});