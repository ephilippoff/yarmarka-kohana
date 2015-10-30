/*global define */
define([
    "marionette",
    "backbone",
    "templates"
], function (Marionette, Backbone, templates) {
    'use strict';

    return Marionette.ItemView.extend({
        template: templates.components.services.newspaper,
        ui: {
            quantity: ".js-quantity",
            city: ".js-city",
            type: ".js-type",
            price: ".js-price",
            form: ".js-form",
            description: ".js-service-description"
        },
        events: {
            "change @ui.quantity": "changeQuantity",
            "change @ui.city": "changeCityCheck",
            "change @ui.type": "changeType",
            "click @ui.description": "showDescription"
        },
        initialize: function(options) {
            this.bindUIElements();
            if (!this.model.get("is_edit")){
                if (this.model.get("city_id")){
                    var cities = this.getNewspaperCities();
                    this.changeCity(cities[this.model.get("city_id")][1]);
                }
            }
        },
        showDescription: function() {
            var s = this;
            var $parent = $(this._parent.options.parentEl());
            $parent.hide();
            app.windows.vent.trigger("showWindow","message", {
                "title": "Описание типов объявлений в газете",
                "text": templates.components.descriptions.newspaper,
                success: function(){
                    $parent.show();
                }
            });
        },
        getNewspaperCities: function() {
                return {1948: ["Ярмарка-Нижневартовск","nizhnevartovsk"], 1919:["Ярмарка-Тюмень","tyumen"],1979:["Ярмарка-Сургут","surgut"] };
        },
        templateHelpers: function() {
            var s = this;
            return {
               getNewspaperCities: function() {
                    return s.getNewspaperCities();
               },
               getStaticPath: function() {
                    return app.settings.staticPath;
               },
               getTypes: function() {
                    return s.model.get("info").type_info.types;
               },
               getPrice: function(type, city, quantity) {
                    var info = s.model.get("info");
                    if (info.service) {
                        return s.getPrice(type, city, quantity) + " руб.";
                       
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
                        return "Размещение в газете объявления '" + info.object.title + "'";
                    } else if (info.objects) {
                        return "Размещение в газете "+info.objects.length+" объявлений(ия)";
                    }
               }
            }
        },
        getPrice: function(type, city, quantity) {
            quantity = quantity || 2;
            var prices = this.model.get("info").type_info.prices;
            return (city && type) ? prices[city][type] * quantity : "(?)";
        },
        changeCityCheck: function(e) {
            var city = $(e.currentTarget).val();
            this.changeCity(city);
        },
        changeCity: function(city) {
            this.city = city;
            this.saveResult();
        },
        changeType: function() {
            this.ui.type.not(":checked").closest("tr").removeClass("success");
            this.ui.type.filter(":checked").closest("tr").addClass("success");
            this.saveResult();
        },
        changeQuantity: function(e) {
            if (!this.city) return;
            var quantity = $(e.currentTarget).val();
            var type = $(e.currentTarget).data("type");

            if (this.model.get("info").service) {
               $(".js-price_" + type).text( this.getPrice(type, this.city, quantity ) + " руб." ) ;
            }
            this.saveResult();
        },
        updatePrices: function() {
            var s = this;
            if (this.model.get("info").service) {
                _.each(this.model.get("info").type_info.types, function(type, type_key) {
                    var quantity = s.$el.find(".js-quantity_" + type_key).val();
                    s.$el.find(".js-price_" + type_key).text( s.getPrice(type_key, s.city, quantity ) + " руб." ) ;
                });
            }
        },
        saveResult: function() {
            if (!this.ui.form.length) return;
            var result = {};
            var types = [];
            _.each(this.ui.type.filter(":checked"), function(item){
                var type = $(item).val();
                types.push(type);
                result["quantity_"+type] = parseInt($(item).closest("tr").find(".js-quantity").val());
            });
            result.types = types;
            result.city = this.city;
            this.model.set("result", result);
            this.updatePrices();
        },
        onRender:function() {
            if (!this.model.get("is_edit")){
                this.ui.type.filter("[value='free']").prop("checked", true).trigger("change");
            } else {
                this.changeType();
                this.changeCity(this.ui.city.filter(":checked").val());
            }
        },
        resultValid: function() {
            var result = this.model.get("result");
            return (result.types && result.types.length > 0 && this.city) ;
        }
    });
});