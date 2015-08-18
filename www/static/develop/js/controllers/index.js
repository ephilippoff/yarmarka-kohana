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

    "modules/filters"
], 
function (Marionette, templates, utils, IndexPage, SearchPage, DetailPage, 
    UserSearchPage, CartPage, AddPage, ArticlePage, FiltersModule) {
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
            console.log("detail start");
            app.menu.init(["main"]);
            new DetailPage({
                el: "body"
            });
        },

        start_searchSection : function() {
            console.log("search start");

            app.module("filters", FiltersModule);
            app.filters.initFilters(app.settings.category_id);

            app.menu.init(["main", "city"]);
            app.favourite.init(["list"]);
            new SearchPage({
                el: "body"
            });
        },

        start_usersearchSection : function() {
            console.log("usersearch start");

            app.menu.init(["main"]);
            app.favourite.init(["list"]);
            new UserSearchPage({
                el: "body"
            });
        },

        start_cartSection : function() {
            console.log("cart start");

            app.menu.init(["main"]);
            app.favourite.init(["list"]);
            new CartPage({
                el: "body"
            });
        },

        start_addSection : function() {
            console.log("Add start");

            app.menu.init(["main"]);
            new AddPage({
                el: "body"
            });
        },

        start_userSection : function() {
            console.log("user start");

            app.menu.init(["main"]);
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