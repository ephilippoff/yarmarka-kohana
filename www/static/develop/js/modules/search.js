/*global define */
define([
    "marionette",
    "backbone"
], function (Marionette, Backbone) {
    'use strict';

    var SearchModel = Backbone.Model.extend({
        urlRoot: "/ajax_object/global_search"
    });

    return Marionette.Module.extend({
        initialize: function() {
           
        },

        init: function(toLoad) {

        },

        do: function(text, options, category) {
            var s = this;
            window.clearTimeout(this.searchTimer);
            this.searchTimer = setTimeout(function(){
               s.searchAdverts(text, options, category)
            }, 400);
        },

        searchAdverts: function(text, options, category) {
            
            var search = new SearchModel();
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            var data = {text: text, city_id: options.city_id};
            if (category !== null && category !== undefined) {
                data.category_id = category;
            }
            search.fetch({
                data: data,
                success: function(model) {
                    var resp = model.toJSON();
                    if (resp.code == 200) {
                        options.success(resp.objects, resp.pricerows, parseInt(resp.objects_found), parseInt(resp.pricerows_found) );
                    } else {
                        options.error(resp);
                    }
                }
            })
        }
    });

});
