/*global define */
define([
    'marionette',
    'templates',
], function (Marionette, templates) {
    'use strict';

	return Marionette.Behavior.extend({
        ui: {
            upService: ".js-service-up",
            buyObjectService: ".js-service-buy-object",
            premiumService: ".js-service-premium",
            liderService: ".js-service-lider",
        },

        events: {
            "click @ui.upService": "upServiceClick",
            "click @ui.buyObjectService": "buyObjectServiceClick",
            "click @ui.premiumService": "premiumServiceClick",
            "click @ui.liderService": "liderServiceClick"
        },

        upServiceClick: function(e) {
            var s = this;
            e.preventDefault();
            var id = $(e.currentTarget).data("id");
            app.services.up(id, {
                success: function(result) {
                    console.log(result, result.date_service_up_available);
                    $(e.currentTarget).find("span").text("Поднято");
                },
                error: function(result) {
                    alert("В следующий раз Вы можете поднять объявление не ранее " + result.date_service_up_available);
                }
            });
        },

        premiumServiceClick: function(e) {
            var s = this;
            e.preventDefault();
            var id = $(e.currentTarget).data("id");
            app.services.premium(id, {
                success: function(result) {
                    console.log(result);
                },
                error: function(result) {
                    console.log(result);
                }
            });
        },

        liderServiceClick: function(e) {
            var s = this;
            e.preventDefault();
            var id = $(e.currentTarget).data("id");
            app.services.lider(id, {
                success: function(result) {
                    console.log(result);
                },
                error: function(result) {
                    console.log(result);
                }
            });
        },

        buyObjectServiceClick: function(e) {
            var s = this;
            e.preventDefault();
            var id = $(e.currentTarget).data("id");
            app.services.buyObject(id, {
                success: function(result) {
                    console.log(result);
                },
                error: function(result) {
                    console.log(result);
                }
            });
        }
    });
});