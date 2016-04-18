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
            contactList: ".js-contact-list",
            moderateControl: ".js-moderate-action"
        },

        events: {
            "click @ui.publishControl": "publishControlClick",
            "click @ui.editControl": "editControlClick",
            "click @ui.contactsShow": "showContacts",
            "click @ui.moderateControl": "moderateControlClick",
        },

        publishControlClick: function(e) {
            var s = this;
            e.preventDefault();
            var id = $(e.currentTarget).data("id");
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

                        app.windows.vent.trigger("showWindow","object_callback",{
                                id: id
                        });
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

        moderateControlClick: function(e) {
            var s = this;
            e.preventDefault();
            var id = $(e.currentTarget).data("id");
            var user = $(e.currentTarget).data("user");
            app.ocontrol.moderateAction(id, {type:"block", user: user});
        },

        showContacts: function(e) {
            var s = this;
            e.preventDefault();
            var $target = $(e.currentTarget);
            var id = $target.data("id");
            app.ocontrol.contacts(id, {
                code: this.ui.contactList.find("input").val(),
                success: function(result) {
                    s.ui.contactList.html(result);
                    $target.remove();
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