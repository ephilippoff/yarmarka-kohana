/*global define */
define([
    'marionette',
    'templates',
], function (Marionette, templates) {
    'use strict';

	return Marionette.Behavior.extend({
        ui: {
            publishControl: ".js-ocontrol-publish",
            publishControlCaption: ".js-ocontrol-publish > span",
            editControl: ".js-ocontrol-edit",
        },

        events: {
            "click @ui.publishControl": "publishControlClick",
            "click @ui.editControl": "editControlClick"
        },

        publishControlClick: function(e) {
            var s = this;
            e.preventDefault();
            var id = $(e.currentTarget).data("id");
            // var $ico = $(e.currentTarget);
            // var $icoCaption = $(e.currentTarget).find(s.ui.favouriteCaption);
            // if (!$(e.currentTarget).hasClass("favorit-ico")) {
            //     $ico = $ico.find(".favorit-ico");
            // }
            app.ocontrol.publishUnpublish(id, {
                success: function(result) {
                    if (result.is_published) {
                        $(e.currentTarget).find("span").text("Снять с публикации");
                    } else {
                        $(e.currentTarget).find("span").text("Опубликовать");
                    }
                    console.log(result);
                },
                error: function(result) {
                    alert(result.errors);
                    app.ocontrol.edit(id);
                }
            });
        },

        editControlClick: function(e) {
            var s = this;
            e.preventDefault();
            var id = $(e.currentTarget).data("id");
            app.ocontrol.edit(id);
        }
    });
});