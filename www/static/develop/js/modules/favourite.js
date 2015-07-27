/*global define */
define([
    "marionette",
    "backbone"
], function (Marionette, Backbone) {
    'use strict';

    var FavouriteModel = Backbone.Model.extend({
        urlRoot: "/ajax_object/favourite"
    });

    return Marionette.Module.extend({
        initialize: function() {
           
        },

        init: function(toLoad) {

        },

        addRemove: function(id, options) {
            var favourite = new FavouriteModel({id: id});
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            favourite.save({}, {
                success: function(model) {
                    var resp = model.toJSON();
                    if (resp.code == 200) {
                        options.success(resp.result, resp.favourites);
                    } else {
                        options.error(resp.result);
                    }
                    
                }
            });
        },

        list: function(options) {
            var favourite = new FavouriteModel();
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            favourite.fetch({
                success: function(model) {
                    var resp = model.toJSON();
                    if (resp.code == 200) {
                        options.success(resp.result);
                    } else {
                        options.error(resp.result);
                    }
                }
            });
        }
    });

});
