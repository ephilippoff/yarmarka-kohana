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
            removeControl: ".js-remove-object",
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
            "click @ui.removeControl": "removeControlClick"
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
                        $(".js-object-remove-"+id).slideUp();
                        s.ui.publishControl.find('i').addClass("fa-times").removeClass('fa-check');
                        s.ui.publishControlCaption.text('Снять с публикации');
                    } else {
                        $(".js-object-state-"+id).addClass("red").removeClass("green").text("Объявление снято");
                        $(".js-object-title-"+id).addClass("red").addClass("strike");
                        $(".js-object-services-"+id).slideUp();
                        $(".js-object-remove-"+id).slideDown();
                        $(".js-object-contacts-"+id).text("Объявление снято с публикации, контактные данные не доступны.");
                        s.ui.publishControl.find('i').removeClass("fa-times").addClass('fa-check');
                        s.ui.publishControlCaption.text('Опубликовать');

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

        removeControlClick: function(e) {
            var s = this;
            e.preventDefault();
            var id = $(e.currentTarget).data("id");
            var target = e.target;
            app.ocontrol.remove(id, {
                success: function(result) {
                    $(".js-object-container-"+id).slideUp();
                },
                error: function(result) {
                    alert(result.errors);
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

                    //$target.remove();

                    s.ui.contactList.find('p').each(function(){
                        var contact = $(this).html();
                        var isEmail = /(.*)@(.*)\.(.*)/.test(contact);

                        if (isEmail) {

                             $(this).text(contact);

                        } else {
                            var phone = contact;
                            
                            if (phone.indexOf('79') !== -1) {
                                phone = '+'+phone.charAt(0)+' ('+phone.charAt(1)+phone.charAt(2)+phone.charAt(3)+') '+phone.charAt(4)+phone.charAt(5)+phone.charAt(6)+'-'+phone.charAt(7)+phone.charAt(8)+'-'+phone.charAt(9)+phone.charAt(10);
                            }else {
                                phone = '+'+phone.charAt(0)+' ('+phone.charAt(1)+phone.charAt(2)+phone.charAt(3)+phone.charAt(4)+') '+phone.charAt(5)+phone.charAt(6)+'-'+phone.charAt(7)+phone.charAt(8)+'-'+phone.charAt(9)+phone.charAt(10);
                            }

                            $(this).text(phone);
                        }
                       

                       
                    });

                    s.ui.contactsShow.remove();

                },
                captcha: function(result) {
                    s.ui.contactsShow.css('position', 'static');
                    s.ui.contactList.html(result);
                },
                error: function(result) {
                    alert(result);
                }
            });
        }
    });
});