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
            var target = e.target;
            app.ocontrol.publishUnpublish(id, {
                success: function(result) {
                    if (result.is_published) {
                        $(".js-object-state-"+id).removeClass("red").addClass("green").text("Объявление опубликовано");
                        $(".js-object-title-"+id).removeClass("red").removeClass("strike");
                        $(".js-object-services-"+id).slideDown();
                        $(".js-object-contacts-"+id).text("Контактные данные доступны, обновите страницу");
                        $(target).addClass("fa-times").removeClass('fa-plus');
                        console.log(e.currentTarget);
                    } else {
                        $(".js-object-state-"+id).addClass("red").removeClass("green").text("Объявление снято");
                        $(".js-object-title-"+id).addClass("red").addClass("strike");
                        $(".js-object-services-"+id).slideUp();
                        $(".js-object-contacts-"+id).text("Объявление снято с публикации, контактные данные не доступны.");
                        $(target).removeClass("fa-times").addClass('fa-plus');

                        app.windows.vent.trigger("showWindow","object_callback",{
                                id: id
                        });
                    }
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
            var id = $(e.currentTarget).data("id");
            app.ocontrol.contacts(id, {
                code: this.ui.contactList.find("input").val(),
                success: function(result) {
                    s.ui.contactList.html(result);
                    $(e.currentTarget).remove();
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