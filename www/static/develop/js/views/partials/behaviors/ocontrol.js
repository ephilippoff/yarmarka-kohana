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
            contactsShow: ".js-contacts-show",
            contactList: ".js-contact-list"
        },

        events: {
            "click @ui.publishControl": "publishControlClick",
            "click @ui.editControl": "editControlClick",
            "click @ui.contactsShow": "showContacts"
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
                        $(".js-object-state-"+id).removeClass("red").text("Объявление опубликовано");
                        $(".js-object-title-"+id).removeClass("red").removeClass("strike");
                        $(".js-object-services-"+id).removeClass("hidden");
                        $(".js-object-contacts-"+id).text("Контактные данные доступны, обновите страницу");
                        $(e.currentTarget).find("span").text("Снять с публикации");
                    } else {
                        $(".js-object-state-"+id).addClass("red").text("Объявление снято");
                        $(".js-object-title-"+id).addClass("red").addClass("strike");
                        $(".js-object-services-"+id).addClass("hidden");
                        $(".js-object-contacts-"+id).text("Объявление снято с публикации, контактные данные не доступны.");
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
        },

        showContacts: function(e) {
            var s = this;
            e.preventDefault();
            var id = $(e.currentTarget).data("id");
            app.ocontrol.contacts(id, {
                code: this.ui.contactList.find("input").val(),
                success: function(result) {
                    s.ui.contactList.html(result);
                },
                captcha: function(result) {
                    s.ui.contactList.html(result);
                },
                error: function(result) {
                    alert(result);
                }
            });
        }
    });
});