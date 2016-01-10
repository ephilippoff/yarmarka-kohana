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
        menuClass: ".citymenu1",
        menuTemplate: "#template-citymenu"
    }

    var cityMenuSettingsRight = {
        controlClass: ".js-citymenu-dropdown-right",
        menuClass: ".citymenu2",
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
            "click" : "showMenu",
            "mouseover" : "showMenu",
            "mouseleave" : "closeMenu"
        },
        initialize: function(options) {
            this.visible = 0;
            this.$el.append($(options.templateClass).html());
            if (!options.doNotRemove) {
                $(options.templateClass).remove();
            }
            //Разделение меню городов по Class;
            var cityClass = 1;
            $('.js-citymenu').each(function(){
                $(this).addClass('citymenu'+cityClass);
                cityClass++;
            });
        },

        showMenu: function() {
            if (this.visible && this.$('[data-link]').length) {
                window.location = this.$('[data-link]').data('link');
            }
            var s = this;
            if (this.activateTimer) clearTimeout(this.activateTimer);
            this.activateTimer = setTimeout(function(){
                s.visible = 1;
                $(s.getOption("menuClass")).fadeIn(70);
                $("#popup-layer").fadeIn(70);
                $(s.getOption("controlClass")).addClass("z301");
            }, 250)
            
        },

        closeMenu: function() {
            if (this.activateTimer) clearTimeout(this.activateTimer);
            $(this.getOption("menuClass")).fadeOut(70);
             $("#popup-layer").fadeOut(70);
             $(this.getOption("controlClass")).removeClass("z301");
             this.visible = 0;
             console.log('hide');
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

              $submenu.fadeIn(70);
            }, 200)
           
        },
        deactivateSubmenu: function(row) {
            if (this.submenuActivateTimer) clearTimeout(this.submenuActivateTimer);
            var $row = $(row), 
                submenuId = $row.data("submenu-id"), 
                $submenu = $("#" + submenuId);

            $submenu.fadeOut(70);
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
                    doNotRemove: true
                });
            }

            if (_.contains(menusToload, "city")) {
                this.city1 = new MenuView({
                    el: cityMenuSettingsRight.controlClass,
                    templateClass: cityMenuSettingsRight.menuTemplate,
                    menuClass: cityMenuSettingsRight.menuClass,
                    controlClass: cityMenuSettingsRight.controlClass,
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