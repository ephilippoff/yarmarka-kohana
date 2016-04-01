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
    "modules/map",

    'modules/afisha',
    'modules/reklama_click',

    'modules/main_page_news',
    'modules/subscription/main'
], 
function (Marionette, templates, utils, IndexPage, SearchPage, DetailPage, 
    UserSearchPage, CartPage, AddPage, ArticlePage, FiltersModule, MapModule, Afisha,
    ReklamaClickInitializer, MainPageNewsView, SubscriptionModule) {
    "use strict";

    return Marionette.Controller.extend({
        before: function() {

        },

        initialize: function () {
            ReklamaClickInitializer();

            if($('div').is('#same_cat_news')) {
                var view = new MainPageNewsView({
                    el: $('#button-next')
                });
            }
        },

        start_indexSection : function() {
            console.log("index start");
            app.menu.init(["main", "city", "kupon", "news"]);
            new IndexPage({
                el: "body"
            });
        },

        start_detailSection : function() {
            console.log("detail start", "kupon", "news");
            app.menu.init(["main", "city", "kupon", "news"]);
            app.module("map", MapModule);
            new DetailPage({
                el: "body"
            });
        },

        start_searchSection : function() {

            app.module("filters", FiltersModule);
            app.module("map", MapModule);
            app.filters.initFilters(app.settings.category_id);

            app.menu.init(["main", "city", "kupon", "news"]);
            app.favourite.init(["list"]);



             /* initialize subscriptions module */
            var $temp = $('[data-role=subscription-module]');
            if ($temp.length) {
                new SubscriptionModule({
                    el: $temp
                });
            }



            $('[data-action="showMap"]').click(function(){       
                new SearchPage({
                      el: "body"
                });
                $(this).hide();
                $('#map-wrap').show();
            });
        },

        start_usersearchSection : function() {
            console.log("usersearch start");

            app.menu.init(["main", "city", "kupon", "news"]);
            app.favourite.init(["list"]);
            new UserSearchPage({
                el: "body"
            });
        },

        start_cartSection : function() {
            console.log("cart start");
            app.menu.init(["main", "city", "kupon", "news"]);
            app.favourite.init(["list"]);
            new CartPage({
                el: "body"
            });
        },

        start_addSection : function() {
            console.log("Add start");
            app.module("map", MapModule);
            app.menu.init(["main", "city", "kupon", "news"]);
            new AddPage({
                el: "body"
            });
        },

        start_userSection : function() {
            console.log("user start");
            app.menu.init(["main", "city", "kupon", "news"]);
        },

        start_articleSection : function() {
            console.log("article start");
            app.menu.init(["main", "city", "kupon", "news"]);
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