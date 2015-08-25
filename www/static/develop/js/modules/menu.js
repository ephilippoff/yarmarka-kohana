/*global define */
define([
    'marionette',
    'menuAim'
], function (Marionette) {
    'use strict';

    var mainMenuSettings = {
        controlClass: ".js-mainmenu-dropdown",
        menuClass: ".js-mainmenu",
        menuTemplate: "#template-mainmenu"
    }

    var userMenuSettings = {
        controlClass: ".js-usermenu-dropdown",
        menuClass: ".js-usermenu",
        menuTemplate: "#template-usermenu"
    }

    var cityMenuSettings = {
        controlClass: ".js-citymenu-dropdown",
        menuClass: ".js-citymenu",
        menuTemplate: "#template-citymenu"
    }

    var newsMenuSettings = {
        controlClass: ".js-newsmenu-dropdown",
        menuClass: ".js-newsmenu",
        menuTemplate: "#template-newsmenu"
    }

    var kuponMenuSettings = {
        controlClass: ".js-kuponmenu-dropdown",
        menuClass: ".js-kuponmenu",
        menuTemplate: "#template-kuponmenu"
    }

    var MenuView = Marionette.ItemView.extend({
        events: {
            "mouseover" : "showMenu",
            "mouseout" : "closeMenu"
        },
        initialize: function(options) {
            this.$el.append($(options.templateClass).html());
            $(options.templateClass).remove();
        },

        showMenu: function() {
            $(this.getOption("menuClass")).show();
        },

        closeMenu: function() {
            $(this.getOption("menuClass")).hide();
        }
    });

    var MainmenuView = MenuView.extend({
        initialize: function(options) {
            MenuView.prototype.initialize.call(this, options);
            var s = this;
            var $menu = this.$el.find(".js-mainmenu ul.top");
            if ($menu.size() > 0){
                $menu.menuAim({
                    activate: this.activateSubmenu, 
                    deactivate: this.deactivateSubmenu,
                    rowSelector: ".js-submenu-item"
                });
            }
        },
        onRender: function() {

        },
        activateSubmenu: function(row) {
            var $row = $(row), 
                submenuId = $row.data("submenu-id"), 
                $submenu = $("#" + submenuId);

            $submenu.css("display", "block");
        },
        deactivateSubmenu: function(row) {
            var $row = $(row), 
                submenuId = $row.data("submenu-id"), 
                $submenu = $("#" + submenuId);

            $submenu.css("display", "none");
        }
    });



    return Marionette.Module.extend({
        initialize: function() {
            this.user = new MenuView({
                el: userMenuSettings.controlClass,
                templateClass: userMenuSettings.menuTemplate,
                menuClass: userMenuSettings.menuClass,
            });
        },
        init: function (menusToload) {
            menusToload = menusToload || [];

            if (_.contains(menusToload, "main")) {
                this.main = new MainmenuView({
                    el: mainMenuSettings.controlClass,
                    templateClass: mainMenuSettings.menuTemplate,
                    menuClass: mainMenuSettings.menuClass,
                });
            }

            if (_.contains(menusToload, "city")) {
                this.city = new MenuView({
                    el: cityMenuSettings.controlClass,
                    templateClass: cityMenuSettings.menuTemplate,
                    menuClass: cityMenuSettings.menuClass,
                });
            }

            if (_.contains(menusToload, "news")) {
                this.news = new MenuView({
                    el: newsMenuSettings.controlClass,
                    templateClass: newsMenuSettings.menuTemplate,
                    menuClass: newsMenuSettings.menuClass,
                });
            }

            if (_.contains(menusToload, "kupon")) {
                this.kupon = new MenuView({
                    el: kuponMenuSettings.controlClass,
                    templateClass: kuponMenuSettings.menuTemplate,
                    menuClass: kuponMenuSettings.menuClass,
                });
            }
        },
    });

});