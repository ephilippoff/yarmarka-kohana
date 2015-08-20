define([
    "marionette",
    "templates",
    "base/utils",

    "views/partials/index",
    "views/partials/search",
    "views/partials/detail",
    "views/partials/userSearch",
    "views/partials/cart",
    "views/partials/add",
    "views/partials/article",

    "modules/filters",
    "modules/map"
], 
function (Marionette, templates, utils, IndexPage, SearchPage, DetailPage, 
    UserSearchPage, CartPage, AddPage, ArticlePage, FiltersModule, MapModule) {
    "use strict";
    
    return Marionette.Controller.extend({
        before: function() {

        },

        start_indexSection : function() {
            console.log("index start");
            new IndexPage({
                el: "body"
            });
        },

        start_detailSection : function() {
            console.log("detail start", "kupon", "news");
            app.menu.init(["main", "kupon", "news"]);
            app.module("map", MapModule);
            new DetailPage({
                el: "body"
            });
        },

        start_searchSection : function() {
            console.log("search start");

            app.module("filters", FiltersModule);
            app.module("map", MapModule);
            app.filters.initFilters(app.settings.category_id);

            app.menu.init(["main", "city", "kupon", "news"]);
            app.favourite.init(["list"]);
            new SearchPage({
                el: "body"
            });
        },

        start_usersearchSection : function() {
            console.log("usersearch start");

            app.menu.init(["main", "kupon", "news"]);
            app.favourite.init(["list"]);
            new UserSearchPage({
                el: "body"
            });
        },

        start_cartSection : function() {
            console.log("cart start");

            app.menu.init(["main", "kupon", "news"]);
            app.favourite.init(["list"]);
            new CartPage({
                el: "body"
            });
        },

        start_addSection : function() {
            console.log("Add start");
            app.module("map", MapModule);
            app.menu.init(["main", "kupon", "news"]);
            new AddPage({
                el: "body"
            });
        },

        start_userSection : function() {
            console.log("user start");

            app.menu.init(["main", "kupon", "news"]);
        },

        start_articleSection : function() {
            console.log("article start");

            new ArticlePage({
                el: "body"
            });
        },
        
        notFound : function() {

        }
    });
});