define([
    "marionette",
    "templates",
    "base/utils",

    "partials/index",
    "partials/search",
    "partials/detail",
], 
function (Marionette, templates, utils, IndexPage, SearchPage, DetailPage) {
    "use strict";
    
    return Marionette.Controller.extend({
        before: function() {

        },

        start_indexSection : function() {
            console.log("index start");
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