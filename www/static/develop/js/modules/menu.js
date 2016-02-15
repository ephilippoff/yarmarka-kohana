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
                $('#menu').accordion({
                    heightStyle: "content",
                    collapsible: true,
                });

                $('#user_menu, .change_city_cont').accordion({
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
                $("#popup-layer").fadeIn(200);
                $('#search-form1').hide();
                this.$menu.show().animate({left: '0'}, 500);
            },

            closeMenu: function(){
                this.$el.find('.bars, .preview_menu_block').removeClass('active');
                $("#popup-layer").fadeOut(200);
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
            this.visible = 0;

            if (!options.doNotUseTemplate) {
                this.$el.append($(options.templateClass).html());
                if (!options.doNotRemove) {
                    $(options.templateClass).remove();
                }
            }
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

        closeMenu: function() {
            if (this.getOption('alwaysVisibleMenu')) {
                return;
            }
            if (this.activateTimer) clearTimeout(this.activateTimer);
            $(this.getOption("menuClass")).fadeOut(70);
            $("#popup-layer").fadeOut(70);
            $(this.getOption("controlClass")).removeClass("z301");
            this.visible = 0;
        }
    });

<<<<<<< HEAD
var MainmenuView = MenuView.extend({
    initialize: function(options) {
        MenuView.prototype.initialize.call(this, options);
        var s = this;
        var $menu = this.$el.find(this.getOption("menuClass")+ " ul.top");
=======
    var MainmenuView = MenuView.extend({
        initialize: function(options) {
            MenuView.prototype.initialize.call(this, options);
            var s = this;
            console.log('init');
            var $menu = this.$el.find(this.getOption("menuClass")+ " ul.top");
console.log(this.getOption("menuClass")+ " ul.top");
            $('.left_menu').find('.section').each(function(){
                var id = $(this).attr('id');
                $(this).attr('id', id+'-l');
            });
>>>>>>> 0588f8d4ffc996ded4bb8b3ba0ee285991be3759

        $('.left_menu').find('.section').each(function(){
            var id = $(this).attr('id');
            $(this).attr('id', id+'-l');
        });

<<<<<<< HEAD
        $('.left_menu').find('ul.top li').each(function(){
            var id = $(this).attr('data-submenu-id');
            $(this).attr('data-submenu-id', id+'-l');
        });
=======
            if ($menu.length > 0){
                try {
                    
                    $menu.menuAim({
                        activate: this.activateSubmenu.bind(this), 
                        deactivate: this.deactivateSubmenu.bind(this),
                        rowSelector: this.$(".js-submenu-item")
                    });
                } catch(e) {}
            }
        },
        onRender: function() {
>>>>>>> 0588f8d4ffc996ded4bb8b3ba0ee285991be3759

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

        if (_.contains(menusToload, "main")) {
            this.main = new MenuView({
                el: mainMenuSettings.controlClass,
                templateClass: mainMenuSettings.menuTemplate,
                menuClass: mainMenuSettings.menuClass,
                controlClass: mainMenuSettings.controlClass,
                doNotRemove: true
            });
        }

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
                    menuOptions.el = $('.left_menu');
                    menuOptions.menuClass = '.top_level_menu:visible';
                    menuOptions.doNotUseTemplate = true;

                    var me = this;
                    var allowDeactivate = true;
                    $('body').on('mousemove', function (e) {
                        var r = me.main.$el.find('.right');
                        var l = me.main.$el.find('.left');
                        var rd = { w: r.width(), h: r.height() };
                        var ld = { w: l.width(), h: l.height() };

                        var td = { w: rd.w + ld.w, h: rd.h + ld.h };
                        var to = l.offset();

                        var w = to.left <= e.pageX && e.pageX <= (to.left + td.w);
                        var h = to.top <= e.pageY && e.pageY <= (to.top + td.h);

                        if (w && h) {
                            return;
                        }

                        me.main.deactivateSubmenu();
                    });
                    /*
                    $(menuOptions.el).find('.submenu-ul').on('mousemove', function (e) {
                        var x = this;
                        var offset = $(x).offset();
                        var mouseOffset = { top: e.pageY, left: e.pageX };

                        console.log([ $(x).width(), $(x).height() ], offset, mouseOffset);

                        if (
                            offset.top <= mouseOffset.top && offset.top + $(x).height() >= mouseOffset.top
                            && offset.left <= mouseOffset.left && offset.left + $(x).width() >= mouseOffset.left) {

                            e.stopPropagation();
                            allowDeactivate = false;
                        } else {
                            allowDeactivate = true;
                            console.log('close');
                        }
                    });
*/
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