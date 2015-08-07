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
        },

        events: {
            "click @ui.upService": "upServiceClick",
            "click @ui.buyObjectService": "buyObjectServiceClick"
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