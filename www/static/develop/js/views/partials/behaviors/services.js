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
            kuponServiceGroup: ".js-Service-Kupon-Group",
            premiumService: ".js-service-premium",
            liderService: ".js-service-lider",
            newspaperService: ".js-service-newspaper",
            citiesService: ".js-service-cities",
            tglinkService: ".js-service-tglink"
        },

        events: {
            "click @ui.upService": "upServiceClick",
            "click @ui.kuponService": "kuponServiceClick",
            "click @ui.kuponServiceGroup": "kuponServiceGroupClick",
            "click @ui.premiumService": "premiumServiceClick",
            "click @ui.liderService": "liderServiceClick",
            "click @ui.newspaperService": "newspaperServiceClick",
            "click @ui.citiesService": "citiesServiceClick",
            "click @ui.tglinkService": "tglinkServiceClick"
        },

        initialize: function() {
            $(this.ui.kuponServiceGroup).each(function(index, item){
                app.services.kupon($(item).data('id'), {
                    justCheck: true,
                    group: $(item).data('group'),
                    success: function(result) {
                       
                    },
                    error: function(result) {
                        var $item = $(item);
                        $('<span style="text-decoration:line-through;">'+$item.text()+'</span>').insertAfter(item);
                        $item.remove()
                    }
                });
            });
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

        tglinkServiceClick: function(e) {
            var s = this;
            e.preventDefault();
            var id = $(e.currentTarget).data("id");
            app.services.tglink(id, {
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
            app.services.kupon(id, {});
        },

        kuponServiceGroupClick: function(e) {
            var s = this;
            e.preventDefault();
            var id = $(e.currentTarget).data("id");
            var group = $(e.currentTarget).data("group");
            app.services.kupon(id, {
                group: group
            });
        }
    });
});