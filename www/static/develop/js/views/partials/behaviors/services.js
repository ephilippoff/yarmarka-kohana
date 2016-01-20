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
            newspaperService: ".js-service-newspaper",
            citiesService: ".js-service-cities"
        },

        events: {
            "click @ui.upService": "upServiceClick",
            "click @ui.kuponService": "kuponServiceClick",
            "click @ui.premiumService": "premiumServiceClick",
            "click @ui.liderService": "liderServiceClick",
            "click @ui.newspaperService": "newspaperServiceClick",
            "click @ui.citiesService": "citiesServiceClick",
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

        newspaperServiceClick: function(e) {
            var s = this;
            e.preventDefault();
            var id = $(e.currentTarget).data("id");
            var city_id = $(e.currentTarget).data("city");
            app.services.newspaper(id, {
                city_id: city_id,
                success: function(result) {
                    console.log(result);
                },
                error: function(result) {
                    console.log(result);
                }
            });
        },

        citiesServiceClick: function(e) {
            var s = this;
            e.preventDefault();
            var id = $(e.currentTarget).data("id");
            var city_id = $(e.currentTarget).data("city");
            app.services.cities(id, {
                city_id: city_id,
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