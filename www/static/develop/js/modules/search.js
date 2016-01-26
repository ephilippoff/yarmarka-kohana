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

        do: function(text, options) {
            var s = this;
            window.clearTimeout(this.searchTimer);
            this.searchTimer = setTimeout(function(){
               s.searchAdverts(text, options)
            }, 400);
        },

        searchAdverts: function(text, options) {
            
            var search = new SearchModel();
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            search.fetch({
                data: {text: text, city_id: options.city_id},
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
