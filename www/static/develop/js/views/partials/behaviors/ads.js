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
                    model.get("$el").attr("data-weight", weight);
                } else {
                    model.set("weight", weight);
                    model.get("$el").attr("data-weight", weight);
                }

                if (weight >= s.limit) {
                    model.get("$el").hide();
                } else {
                    model.get("$el").show();
                }

                i++;
            });
            this.sort();
        }
    });

    return Marionette.Behavior.extend({
        initialize: function() {
            var iso = new Isotope(  document.querySelector('.js-adslink-graphic'), {
                // options
                itemSelector: '.js-adslink-item',
                layoutMode: 'fitRows',
                fitRows: {
                    gutter: 5
                },
                getSortData: {
                    weight: '[data-weight] parseInt'
                },
                sortBy: 'weight'
            });
            
            var adCollection = new AdCollection();
            adCollection.init({
                limit: $(".js-adslink-graphic").width()/105
            });

            $( window ).resize(function() {
                adCollection.limit = $(".js-adslink-graphic").width()/105;
            });

            setInterval(function(){
                iso.arrange({sortBy: 'weight'});
                iso.updateSortData();
                adCollection.reorder();
            }, 2000);
        }
    });

});