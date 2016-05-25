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
        };

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

        var MobileMenu = Marionette.View.extend({
            el: ".mobile_menu",
            events: {
                "click .bars" : "onButtonClick"
            },

            initialize: function(options){
                this.$menu = this.$el.find('.menu_content');
                if (!$('#menu').accordion) return;
                $('#menu').accordion({
                    heightStyle: "content",
                    collapsible: true,
                });

                $('#user_menu, .change_city_cont').accordion({
                    collapsible: true,
                    active: false,
                    heightStyle: "content",
                });

                $('#user_menu, .change_city_cont, #subscribe-cont').accordion({
                    collapsible: true,
                    active: false,
                    heightStyle: "content",
                });
            },

            onButtonClick: function(){
                if (!$('.menu_content').is(':visible')) {
                    this.openMenu();
                }else{
                    this.closeMenu();
                }
            },

            openMenu: function(){
                this.$el.find('.bars, .preview_menu_block').addClass('active');
                $(".popup-layer").fadeIn(200);
                $('#wrap-page').addClass('fixed z200');
                $('#search-form1').hide();
                this.$menu.show().animate({left: '0'}, 500);
            },

            closeMenu: function(){
                this.$el.find('.bars, .preview_menu_block').removeClass('active');
                $(".popup-layer").fadeOut(200);
                $('#wrap-page').removeClass('fixed z200');
                $('#search-form1').show();
                this.$menu.animate({left: '-100%'}, 500).fadeOut();
            },

        });

    var MenuView = Marionette.ItemView.extend({
        events: {
            "click" : "showMenu",
            "mouseover" : "showMenu",
            "mouseleave" : "closeMenu"
        },
        initialize: function(options) {
            var $this = this;
            this.visible = 0;

            if (!options.doNotUseTemplate) {
                this.$el.append($(options.templateClass).html());
                if (!options.doNotRemove) {
                    $(options.templateClass).remove();
                }
            }

            // this.$el.find(.on('mouseleave')
            //Разделение меню городов по Class;
            var cityClass = 1;
            $('.js-citymenu').each(function(){
                $(this).addClass('citymenu'+cityClass);
                cityClass++;
            });

        },

        showMenu: function() {
            if (this.getOption('alwaysVisibleMenu')) {
                return;
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

        closeMenu: function(e) {
            if (this.getOption('alwaysVisibleMenu')) {
                return;
            }
            if (this.activateTimer) clearTimeout(this.activateTimer);
            $(this.getOption("menuClass")).fadeOut(100);
            $("#popup-layer").fadeOut(100);
            $(this.getOption("controlClass")).removeClass("z301");
            this.visible = 0;
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
                    activate: this.activateSubmenu.bind(this), 
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
        this.activeRow = row;
        this.submenuActivateTimer = setTimeout(function(){
            var $row = $(row), 
            submenuId = $row.data("submenu-id"), 
            submenu = "#" + submenuId;
            $(submenu).show();
        }, 200);

    },
    deactivateSubmenu: function(row) {
        if (this.submenuActivateTimer) clearTimeout(this.submenuActivateTimer);
        if (!row) {
            row = this.activeRow;
        }
        var $row = $(row), 
        submenuId = $row.data("submenu-id"), 
        submenu = "#" + submenuId;
        $(submenu).fadeOut(70);

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

        this.mobilemenu = new MobileMenu();
    },
    init: function (menusToload) {
        menusToload = menusToload || [];

        // if (_.contains(menusToload, "main")) {
        //     this.main = new MenuView({
        //         el: mainMenuSettings.controlClass,
        //         templateClass: mainMenuSettings.menuTemplate,
        //         menuClass: mainMenuSettings.menuClass,
        //         controlClass: mainMenuSettings.controlClass,
        //         doNotRemove: true
        //     });
        // }

        if (_.contains(menusToload, "main")) {
            var menuOptions = {
                el: mainMenuSettings.controlClass,
                templateClass: mainMenuSettings.menuTemplate,
                menuClass: mainMenuSettings.menuClass,
                controlClass: mainMenuSettings.controlClass
            };

            if (_globalSettings.page == 'index') {
                    //override some settings
                    menuOptions.alwaysVisibleMenu = true;
                    menuOptions.doNotRemove = true;
                    menuOptions.el = '.left_menu';
                    menuOptions.menuClass = '.top_level_menu';
                    menuOptions.doNotUseTemplate = true;

                    var me = this;
                    $(menuOptions.el).on('mouseleave', function (e) {
                        me.main.deactivateSubmenu();
                    });
                }

                this.main = new MainmenuView(menuOptions);
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