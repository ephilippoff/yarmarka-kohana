/*global define */
define([
    "marionette",
    "backbone"
], function (Marionette, Backbone) {
    'use strict';

    var ControlModel = Backbone.Model.extend({

    });

    return Marionette.Module.extend({
        publishUnpublish: function(id, options) {
            var controlModel = new ControlModel();
            controlModel.urlRoot = "/ajax/pub_toggle/"+id;
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            controlModel.save({}, {
                success: function(model) {
                    var resp = model.toJSON();
                    if (resp.code == 200) {
                        options.success(resp);
                    } else {
                        options.error(resp);
                    }
                    
                }
            });
        },

        edit: function(id) {
            $(window).attr('location','/edit/'+id);
        }
    });

});
