/*global define */
define([
    'marionette',
    'templates',
], function (Marionette, templates) {
    'use strict';

	return Marionette.Behavior.extend({
        ui: {
            upService: ".js-service-up",
            kuponService: ".js-service-kupon",
            premiumService: ".js-service-premium",
            liderService: ".js-service-lider",
        },

        events: {
            "click @ui.upService": "upServiceClick",
            "click @ui.kuponService": "kuponServiceClick",
            "click @ui.premiumService": "premiumServiceClick",
            "click @ui.liderService": "liderServiceClick"
        },

        upServiceClick: function(e) {
            var s = this;
            e.preventDefault();
            var id = $(e.currentTarget).data("id");
            app.services.up(id, {
                success: function(result) {
                    console.log(result);
                },
                error: function(result) {
                   console.log(result);
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

        kuponServiceClick: function(e) {
            var s = this;
            e.preventDefault();
            var id = $(e.currentTarget).data("id");
            app.services.kupon(id, {
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