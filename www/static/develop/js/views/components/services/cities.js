/*global define */
define([
    "marionette",
    "backbone",
    "templates"
], function (Marionette, Backbone, templates) {
    'use strict';

    return Marionette.ItemView.extend({
        template: templates.components.services.cities,
        ui: {
            quantity: ".js-quantity",
            city: ".js-city",
            price: ".js-price",
            form: ".js-form",
            description: ".js-service-description"
        },
        events: {
            "change @ui.quantity": "changeQuantity",
            "change @ui.city": "changeCity"
        },
        initialize: function(options) {
            this.bindUIElements();
        },
        templateHelpers: function() {
            var s = this;
            return {
               getCities: function() {
                    return s.model.get("info").cities_info.cities;
               },
               existsCurrentCity: function(id) {
                    return _.contains(s.model.get("info").cities_info.exists_cities, ''+id);
               },
               checkedCity: function(id) {
                    return _.contains(s.model.get("edit_params").service.cities, +id); 
               },
               getPrice: function() {
                    return s.getPrice() + " руб.";
                
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
                        return "Размещение в нескольких городах объявления '" + info.object.title + "'";
                    } else if (info.objects) {
                        return "Размещение в нескольких городах "+info.objects.length+" объявлений(ия)";
                    }
               }
            }
        },
        getPrice: function() {
            return this.model.get("info").cities_info.price
        },
        changeCity: function() {
            this.ui.city.not(":checked").closest("tr").removeClass("success");
            this.ui.city.filter(":checked").closest("tr").addClass("success");
            this.saveResult();
        },
        onRender:function() {
            this.saveResult();
        },
        saveResult: function() {
            if (!this.ui.form.length) return;
            var result = {};
            var cities = [];
            _.each(this.ui.city.filter(":checked"), function(item){
                var city = $(item).val();
                cities.push(city);
            });
            result.cities = cities;
            this.model.set("result", result);
        },
        resultValid: function() {
            var result = this.model.get("result");
            return (result.cities && result.cities.length > 0) ;
        }
    });
});