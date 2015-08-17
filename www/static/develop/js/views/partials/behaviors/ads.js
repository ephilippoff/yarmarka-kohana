/*global define */
define([
    'marionette',
    'templates',
    "isotope"
], function (Marionette, templates, Isotope) {
    'use strict';

    var AdModel = Backbone.Model.extend({});
    var AdCollection = Backbone.Collection.extend({
        model: AdModel,
        comparator: function(model){
            return model.get("weight")
        },
        init: function(options) {
            this.limit = options.limit || 11;
            var s = this;
            _.each($('.js-adslink-graphic .js-adslink-item'), function(item){
                s.add({
                    $el: $(item),
                    weight: $(item).data("weight")
                }, {sort: false});
            });
            this.reorder();
        },
        reorder: function() {
            var s = this, i = 0;
            this.each(function(model){
                var weight = i;
                if (i == 0) {
                    weight = s.models.length;
                    model.set("weight", weight);
                } else {
                    model.set("weight", weight);
                }
                i++;
            });
            this.sort();
        }
    });
    
    var AdItemView = Marionette.ItemView.extend({
        tagName: "a",
        className: "item js-adslink-graphic",
        template: "<img src='<%=src%>'><% if (title) {%> <span><%=title%></span><%}%>",
        events: {
            "mouseover": "itemMouseOver",
            "mouseout": "itemMouseOut"
        },
        initialize: function(options) {
            this.$el.attr("style","display:none;");
            this.$el.attr("target","_blank");
            this.$el.attr("rel","nofollow");
            this.$el.attr("href",this.model.get("link"));
            if (this.model.get("empty")) {
                this.$el.addClass("noactive");
            }
        },
        itemMouseOver: function() {
            this.triggerMethod('item:mouseover');
        },
        itemMouseOut: function() {
            this.triggerMethod('item:mouseout');
        }
    });

    var AdViewCollection = Marionette.CollectionView.extend({
        maxCount: 10,
        childView: AdItemView,
        events: {
            "mouseleave": "viewMouseLeave"
        },
        fadeIn: function() {
            var i = 0, k = 0, s = this;
            _.each(this.children._views, function(item) {
                setTimeout(function(){
                    if (k < s.maxCount) {
                        item.$el.fadeIn();
                    }
                    k = k + 1;
                }, i);
                i = i + 50;
            });
        },
        fadeOut: function() {
           var s = this, i = 0;
           var ads = _.keys(this.children._views).reverse();
            _.each(ads, function(key) {
                var item = s.children._views[key];
                setTimeout(function(){
                    item.$el.fadeOut();
                }, i);
                i = i + 50;
            });
        },
        childEvents: {
            'item:mouseover': function (view) {
                var s = this;
                _.each(this.children._views, function(item) {
                    if (view.cid != item.cid) {
                        item.$el.addClass("noactive");
                    }
                });
                view.$el.removeClass("noactive");
                s.isActive = true;
            },
            'item:mouseout': function (view) {
                var s = this;
                if (!view.model.get("empty")) {
                    view.$el.removeClass("noactive");
                } else {
                    view.$el.addClass("noactive");
                }
                s.isActive = false;
            }
        },
        viewMouseLeave: function(e) {
           
            var s = this;

            if (this.viewMouseOutTimer) clearTimeout(this.viewMouseOutTimer);
            this.viewMouseOutTimer = setTimeout(function(){
                _.each(s.children._views, function(item) {
                    if (!item.model.get("empty")) {
                        item.$el.removeClass("noactive");
                    } else {
                        item.$el.addClass("noactive");
                    }
                });
            }, 600);
        }
    });

    return Marionette.Behavior.extend({
        ui: {
            'storageItems': $(".js-ads-storage img"),
            'container': $(".js-adslink-graphic")
        },
        initialize: function() {
            var s = this;
            this.initAds();
            if (this.adsView) {
                this.startAnimationAds();
            }

        },
        initAds: function() {

            var s = this, i = 300;
            var adCollection = new AdCollection();

            _.each(this.ui.storageItems, function(item){
                adCollection.add({
                    src: $(item).attr("src"),
                    link: $(item).data("link"),
                    title: $(item).data("title"),
                    weight: 0,
                    empty: $(item).data("empty")
                }, {sort: false});
            });

            adCollection.models = _.shuffle(adCollection.models);

            var view = new AdViewCollection({
                el: this.ui.container,
                collection: adCollection
            });
            view.maxCount = Math.floor((this.ui.container.width() - 18)/107);
            view.render();
            view.fadeIn();

             $( window ).resize(function() {
                clearInterval(s.adsAnimationInterval);
                view.fadeOut();
                view.$el.hide();

                if (s.resizeTimer) clearTimeout(s.resizeTimer);
                s.resizeTimer = setTimeout(function(){
                    view.$el.show();
                    view.maxCount = Math.floor((s.ui.container.width() - 18)/107);
                    view.fadeIn();
                }, 550);
            });

            if (adCollection.where({empty:true}).length > 0){
                return;
            };

            this.adsView = view;

            $(this.ui.container).on("mouseover", function(){
                clearInterval(s.adsAnimationInterval);
            });
            $(this.ui.container).on("mouseout", function(){
                 s.startAnimationAds();
            });
           
        },
        startAnimationAds: function() {
            var view = this.adsView;
            this.adsAnimationInterval =  setInterval(function(){
                view.fadeOut();
                setTimeout(function(){
                    view.collection.reorder();
                    view.render();
                    view.fadeIn();
                }, 550);
            }, 5000);
        }

        // initAnimation: function() {
        //     var iso = new Isotope(  document.querySelector('.js-adslink-graphic'), {
        //         // options
        //         itemSelector: '.js-adslink-item',
        //         layoutMode: 'fitRows',
        //         fitRows: {
        //             gutter: 3
        //         },
        //         getSortData: {
        //             weight: '[data-weight] parseInt'
        //         },
        //         sortBy: 'weight'
        //     });
            
        //     var adCollection = new AdCollection();
        //     adCollection.init({
        //         limit: ($(".js-adslink-graphic").width())/105
        //     });

        //     $( window ).resize(function() {
        //         adCollection.limit = ($(".js-adslink-graphic").width())/105;
        //     });

        //     iso.arrange({sortBy: 'weight'});
        //     iso.updateSortData();
        //     adCollection.reorder();
        //     this.iso = iso;
        //     this.adCollection = adCollection;
        // },
        // startAnimation: function() {
        //     var s = this;
        //     setInterval(function(){
        //         s.iso.arrange({sortBy: 'weight'});
        //         s.iso.updateSortData();
        //         s.adCollection.reorder();
        //     }, 3000);
        // }
    });

});