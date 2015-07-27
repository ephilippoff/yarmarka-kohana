/*global define, window */
define([
	"marionette",
	"routers/index",
	"controllers/index",
	"modules/menu",
    "modules/favourite"
], 
function (Marionette, IndexRouter, IndexController, Mainmenu, Favourite) {
    "use strict";

    var app = new Marionette.Application();

    var t0 = performance.now();
    console.log("start app");

    app.addInitializer(function(){
        console.log("app initialized");

        app.module("menu", Mainmenu);
        app.module("favourite", Favourite);

        app.indexRouter = new IndexRouter({
            controller : IndexController
        });

        var t1 = performance.now();
        console.log("dom ready " + (t1 - t0));
    });
    
    app.on("start", function(){
        if (Backbone.history){ Backbone.history.start(); }
    });

    

    return (window.app = app);
});