define([
    "marionette",
    "templates",

    "views/partials/detail",
    "views/partials/cart",

], 
function (Marionette, templates, DetailPage, CartPage) {
    "use strict";
    
    return Marionette.Controller.extend({
        before: function() {

        },

        // start_indexSection : function() {
        //     console.log("index start");
        //      app.menu.init(["kupon", "news"]);
        //     new IndexPage({
        //         el: "body"
        //     });
        // },

        start_detailSection : function() {
            console.log("detail start", "kupon", "news");
            // app.menu.init(["main", "kupon", "news"]);
            // app.module("map", MapModule);
            new DetailPage({
                el: "body"
            });
        },

        // start_searchSection : function() {
        //     console.log("search start");

        //     app.module("filters", FiltersModule);
        //     app.module("map", MapModule);
        //     app.filters.initFilters(app.settings.category_id);

        //     app.menu.init(["main", "city", "kupon", "news"]);
        //     app.favourite.init(["list"]);
        //     new SearchPage({
        //         el: "body"
        //     });
        // },

        // start_usersearchSection : function() {
        //     console.log("usersearch start");

        //     app.menu.init(["main", "kupon", "news"]);
        //     app.favourite.init(["list"]);
        //     new UserSearchPage({
        //         el: "body"
        //     });
        // },

        start_cartSection : function() {
            console.log("cart start");
            //app.menu.init(["main", "kupon", "news"]);
            //app.favourite.init(["list"]);
            new CartPage({
                el: "body"
            });
        },

        // start_addSection : function() {
        //     console.log("Add start");
        //     app.module("map", MapModule);
        //     app.menu.init(["main", "kupon", "news"]);
        //     new AddPage({
        //         el: "body"
        //     });
        // },

        // start_userSection : function() {
        //     console.log("user start");
        //     app.menu.init(["main", "kupon", "news"]);
        // },

        // start_articleSection : function() {
        //     console.log("article start");
        //     app.menu.init(["main", "kupon", "news"]);
        //     new ArticlePage({
        //         el: "body"
        //     });
        // },
        
        notFound : function() {

        }
    });
});