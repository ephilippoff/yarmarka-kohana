define([
    "marionette",
    "templates",
    "base/utils",

    "views/partials/index",
    "views/partials/search",
    "views/partials/detail",
    "views/partials/userSearch",
    "views/partials/cart",
    "views/partials/user",
    
    "views/partials/article",

    "modules/filters",
    "modules/map",

    'modules/afisha',
    'modules/reklama_click',
    
    'modules/subscription/main'
], 
function (Marionette, templates, utils, IndexPage, SearchPage, DetailPage, 
    UserSearchPage, CartPage, UserPage, 
    //AddPage, serviceApp, 
    ArticlePage, FiltersModule, MapModule, Afisha,
    ReklamaClickInitializer, SubscriptionModule) {
    "use strict";

    return Marionette.Controller.extend({
        before: function() {

        },

        initialize: function () {
            ReklamaClickInitializer();
        },

        start_indexSection : function() {
            console.log("index start");
            app.menu.init(["main", "city", "news"]);
            new IndexPage({
                el: "body"
            });
        },

        start_detailSection : function() {
            console.log("detail start", "news");
            app.menu.init(["main", "city", "news"]);
            app.module("map", MapModule);
            new DetailPage({
                el: "body"
            });
        },

        start_searchSection : function() {

            app.module("filters", FiltersModule);
            app.module("map", MapModule);
            app.filters.initFilters(app.settings.category_id);

            app.menu.init(["main", "city", "news"]);
            app.favourite.init(["list"]);



             /* initialize subscriptions module */
            var $temp = $('[data-role=subscription-module]');
            if ($temp.length) {
                new SubscriptionModule({
                    el: $temp
                });
            }

            new SearchPage({
                  el: "body"
            });
        },

        start_usersearchSection : function() {
            console.log("usersearch start");

            app.menu.init(["main", "city", "news"]);
            app.favourite.init(["list"]);
            new UserSearchPage({
                el: "body"
            });
        },

        start_cartSection : function() {
            console.log("cart start");
            app.menu.init(["main", "city", "news"]);
            app.favourite.init(["list"]);
            new CartPage({
                el: "body"
            });
        },

        start_addSection : function() {
            console.log("Add start");
            app.module("map", MapModule);
            app.menu.init(["main", "city", "news"]);

            if (app.settings.debug) {
                require(['views/partials/add'], function(AddPage)  {

                    new AddPage({
                        el: "body"
                    });
                    
                });
                
            } else {
                require(["../static/develop/production/js/add.build.js"], function() {

                    require(['../js/views/partials/add'], function(AddPage)  {

                        new AddPage({
                            el: "body"
                        });
                        
                    });
                  
                });
            }
            

            

        },

        start_userSection : function() {
            console.log("user start");
            app.menu.init(["main", "city", "news"]);
            new UserPage({
                el: "body"
            });
        },

        start_articleSection : function() {
            console.log("article start");
            app.menu.init(["main", "city", "news"]);
            app.module("map", MapModule);
            new ArticlePage({
                el: "body"
            });
        },
        
        notFound : function() {
            
        },

        start_afishaSection: function (action) {
            if (action == 'afishaReset') {
                return;
            }
            Afisha.factory();
        }
    });
});