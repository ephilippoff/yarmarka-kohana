define([
    "marionette",
    "templates",
    "base/utils",

    "partials/index",
    "partials/search",
    "partials/detail",

    "modules/filters"
], 
function (Marionette, templates, utils, IndexPage, SearchPage, DetailPage, FiltersModule) {
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
            app.menu.init(["main", "city"]);
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
        
        notFound : function() {

        }
    });
});