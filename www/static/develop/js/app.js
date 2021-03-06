/*global define, window */
define([
    "marionette",
    "routers/index",
    "controllers/index",
    "modules/windows",
    "modules/menu",
    "modules/favourite",
    "modules/ocontrol",
    "modules/search",
    "modules/services",
    "modules/common"
], 
function (Marionette, IndexRouter, IndexController, Windows, Mainmenu, 
    Favourite, Ocontrol, Search, Services, Common) {
    "use strict";
    
    if (!window.performance.now) {
        window.performance.now = function(){return 0;}
    }

    var app = new Marionette.Application();

    var t0 = window.performance.now();

    app.addInitializer(function(){
        app.backLayer = $("#popup-layer");

        app.module("common", Common);
        app.module("windows", Windows);
        app.module("menu", Mainmenu);
        app.module("favourite", Favourite);
        app.module("ocontrol", Ocontrol);
        app.module("services", Services);
        app.module("search", Search);

        app.indexRouter = new IndexRouter({
            controller : IndexController
        });

        var t1 = window.performance.now();
        console.log("dom ready " + (t1 - t0));
    });
    
    app.on("start", function(){
        if (Backbone.history){ Backbone.history.start(); }
    });

    return (window.app = app);
});