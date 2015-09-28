/*global define */
define([
    'marionette',
    'templates',
], function (Marionette, templates) {
    'use strict';

	return Marionette.Behavior.extend({
        ui: {
            selectObject:".js-select-object",
            selectObjectChecked:".js-select-object:checked",
            hideRegion: ".js-hide-region",
            groupOperations: "#group_operations",

            publish: ".js-groupcontrol-publish",
            unpublish: ".js-groupcontrol-unpublish",
            unpublishall: ".js-groupcontrol-unpublish-all",

            up: ".js-service-group-up",
            premium: ".js-service-group-premium",
            lider: ".js-service-group-lider",
            newspaper: ".js-service-group-newspaper"
        },

        events: {
            "change @ui.selectObject": "selectObject",
            "click @ui.publish": "publishObjects",
            "click @ui.unpublish": "unpublishObjects",
            "click @ui.unpublishall": "unpublishAllObjects",

            "click @ui.up": "upObjects",
            "click @ui.premium": "premiumObjects",
            "click @ui.lider": "liderObjects",
            "click @ui.newspaper": "newspaperObjects"
        },

        selectObject: function(e) {

            var value = $(e.currentTarget).prop("checked");
            var object_id = $(e.currentTarget).data("id");
            var count = $(".js-select-object:checked").length;

            if (value) {
                $(".js-hide-region-"+object_id).hide();
            } else {
                $(".js-hide-region-"+object_id).show();
            }

            if (count > 0) {
                this.ui.groupOperations.show();
                this.ui.hideRegion.hide();
            } else {
                this.ui.groupOperations.hide();
                this.ui.hideRegion.show();
            }
        },

        publishObjects: function(e) {
            e.preventDefault();
            var ids = this.get_objects_ids(true);
            app.windows.vent.trigger("showWindow", "message", {
                "title": "<i class='fa fa-spinner fa-spin fa-lg mr3'></i> Публикация объявлений",
                "text": "Ожидайте окончания операции.",
                "disableOk" : true
            });
            this.publishUnpublish(ids, true);
        },

        unpublishObjects: function(e) {
            e.preventDefault();
            var ids = this.get_objects_ids(true);
            
            app.windows.vent.trigger("showWindow", "message", {
                "title": "<i class='fa fa-spinner fa-spin fa-lg mr3'></i> Снятие объявлений с публикации",
                "text": "Ожидайте окончания операции..",
                "disableOk" : true
            });
            this.publishUnpublish(ids, false);
            app.windows.vent.trigger("showWindow","object_callback",{ids: ids});
        },

        publishAllObjects: function(e) {
            e.preventDefault();
            var ids = this.get_objects_ids(true);
            app.windows.vent.trigger("showWindow", "message", {
               "title": "<i class='fa fa-spinner fa-spin fa-lg mr3'></i> Публикация объявлений",
                "text": "Ожидайте окончания операции..",
                "disableOk" : true
            });
            this.publishUnpublish(ids, true, true);
        },

        unpublishAllObjects: function(e) {
            e.preventDefault();
            if (!confirm("Вы уверены что хотите снять с публикации все объявления?")) {
                    return;
            }
            var ids = this.get_objects_ids(true);
            app.windows.vent.trigger("showWindow", "message", {
                "title": "<i class='fa fa-spinner fa-spin fa-lg mr3'></i> Снятие объявлений с публикации",
                "text": "Ожидайте окончания операции..",
                "disableOk" : true
            });
            this.publishUnpublish(ids, false, true);
        },

        upObjects: function(e) {
             e.preventDefault();
             var ids = this.get_objects_ids(true);
             app.services.up(null, {
                 ids: ids,
                 success: function(result) {
                     console.log(result);
                 },
                 error: function(result) {
                    console.log(result);
                 }
             });
         },

         premiumObjects: function(e) {
             e.preventDefault();
             var ids = this.get_objects_ids(true);
             app.services.premium(null, {
                 ids: ids,
                 success: function(result) {
                     console.log(result);
                 },
                 error: function(result) {
                    console.log(result);
                 }
             });
         },

         liderObjects: function(e) {
             e.preventDefault();
             var ids = this.get_objects_ids(true);
             app.services.lider(null, {
                 ids: ids,
                 success: function(result) {
                     console.log(result);
                 },
                 error: function(result) {
                    console.log(result);
                 }
             });
         },

         newspaperObjects: function(e) {
             e.preventDefault();
             var ids = this.get_objects_ids(true);
             app.services.newspaper(null, {
                 ids: ids,
                 success: function(result) {
                     console.log(result);
                 },
                 error: function(result) {
                    console.log(result);
                 }
             });
         },

        get_objects_ids: function(checked) {
            var result = [];
            var objects = (checked) ? $(".js-select-object:checked") : $(".js-select-object");
            _.each(objects, function(item){
                result.push($(item).data("id"));
            });
            return result;
        },

        publishUnpublish: function(ids, to_publish, all) {
            var s = this;
            var errors = 0;
            app.ocontrol.publishUnpublishGroup(ids, to_publish, all, {
                success: function(result) {
                    if (result.affected) {
                        _.each(result.affected, function(id){
                            if (to_publish) {
                                $(".js-object-state-"+id).removeClass("red").text("Объявление опубликовано");
                                $(".js-object-title-"+id).removeClass("red").removeClass("strike");
                                $(".js-object-services-"+id).removeClass("hidden");
                                $(".js-object-contacts-"+id).text("Контактные данные доступны, обновите страницу");
                            } else {
                                $(".js-object-state-"+id).addClass("red").text("Объявление снято");
                                $(".js-object-title-"+id).addClass("red").addClass("strike");
                                $(".js-object-services-"+id).addClass("hidden");
                                $(".js-object-contacts-"+id).text("Объявление снято с публикации, контактные данные не доступны.");
                            }
                        });
                    }
                    
                    if (to_publish && result.code == 400) {
                        alert("Несколько объявлений, не удалось опубликовать. Скорее всего не все поля заполнены");
                    }
                    app.windows.vent.trigger("closeWindow", "message");
                },
                error: function(result) {
                    errors ++;
                    alert("Несколько объявлений, не удалось опубликовать. Скорее всего не все поля заполнены");
                    app.windows.vent.trigger("closeWindow", "message");
                }
            });

            return errors;
        }
    });
});