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
            "mouseleave" : "closeMenu"
        },
        initialize: function(options) {
            this.$el.append($(options.templateClass).html());
            $(options.templateClass).remove();
        },

        showMenu: function() {
            var s = this;
            if (this.activateTimer) clearTimeout(this.activateTimer);
            this.activateTimer = setTimeout(function(){
                $(s.getOption("menuClass")).fadeIn();
                $("#popup-layer").fadeIn();
                $(s.getOption("controlClass")).addClass("z301");
            }, 200)
            
        },

        closeMenu: function() {
            if (this.activateTimer) clearTimeout(this.activateTimer);
            $(this.getOption("menuClass")).fadeOut();
             $("#popup-layer").fadeOut();
             $(this.getOption("controlClass")).removeClass("z301");
        }
    });

    var MainmenuView = MenuView.extend({
        initialize: function(options) {
            MenuView.prototype.initialize.call(this, options);
            var s = this;

            var $menu = this.$el.find(this.getOption("menuClass")+ " ul.top");

            if ($menu.length > 0){
                try {
                    $menu.menuAim({
                        activate: this.activateSubmenu, 
                        deactivate: this.deactivateSubmenu,
                        rowSelector: ".js-submenu-item"
                    });
                } catch(e) {}
            }
        },
        onRender: function() {

        },
        activateSubmenu: function(row) {
            var s = this;
            if (this.submenuActivateTimer) clearTimeout(this.submenuActivateTimer);

            this.submenuActivateTimer = setTimeout(function(){
               var $row = $(row), 
                  submenuId = $row.data("submenu-id"), 
                  $submenu = $("#" + submenuId);

              $submenu.fadeIn();
            }, 200)
           
        },
        deactivateSubmenu: function(row) {
            if (this.submenuActivateTimer) clearTimeout(this.submenuActivateTimer);
            var $row = $(row), 
                submenuId = $row.data("submenu-id"), 
                $submenu = $("#" + submenuId);

            $submenu.fadeOut();
        }
    });



    return Marionette.Module.extend({
        initialize: function() {
            this.user = new MenuView({
                el: userMenuSettings.controlClass,
                templateClass: userMenuSettings.menuTemplate,
                menuClass: userMenuSettings.menuClass,
                controlClass: userMenuSettings.controlClass,
            });
        },
        init: function (menusToload) {
            menusToload = menusToload || [];

            if (_.contains(menusToload, "main")) {
                this.main = new MainmenuView({
                    el: mainMenuSettings.controlClass,
                    templateClass: mainMenuSettings.menuTemplate,
                    menuClass: mainMenuSettings.menuClass,
                    controlClass: mainMenuSettings.controlClass,
                });
            }

            if (_.contains(menusToload, "city")) {
                this.city = new MenuView({
                    el: cityMenuSettings.controlClass,
                    templateClass: cityMenuSettings.menuTemplate,
                    menuClass: cityMenuSettings.menuClass,
                    controlClass: cityMenuSettings.controlClass,
                });
            }

            if (_.contains(menusToload, "news")) {
                this.news = new MainmenuView({
                    el: newsMenuSettings.controlClass,
                    templateClass: newsMenuSettings.menuTemplate,
                    menuClass: newsMenuSettings.menuClass,
                    controlClass: newsMenuSettings.controlClass,
                });
            }

            if (_.contains(menusToload, "kupon")) {
                this.kupon = new MainmenuView({
                    el: kuponMenuSettings.controlClass,
                    templateClass: kuponMenuSettings.menuTemplate,
                    menuClass: kuponMenuSettings.menuClass,
                    controlClass: kuponMenuSettings.controlClass,
                });
            }
        },
    });

});