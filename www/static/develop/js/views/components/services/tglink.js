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

        debounceTimeout:null,
        changeText: function() {
            var s = this;
            var value = this.ui.text.val();

            this.model.set('text', value);

            this.ui.example.text( value ? value : "Пример" );

            if (this.debounceTimeout) clearTimeout(this.debounceTimeout);

            this.debounceTimeout = setTimeout(function(){

                s.saveResult();

                Array.from(s.ui.example).forEach(function(item){
                    item.style.top = (25 - (s.ui.example.height()/2 || 10) ) + 'px';
                });

            }, 500);
           

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
                    text: this.model.get('text')
               }
               
            };
            this.model.set("result", result);
            this.model.set("urlRoot", '/rest_service/save_service');
        },

        onRender: function() {
            this.changeText();
            this.saveResult();
            
        },

        resultValid: function() {
            var result = (this.model.get('quantity') > 0 && this.model.get('text') && this.model.get('text').length > 2);
            return result;
        }
    });

});