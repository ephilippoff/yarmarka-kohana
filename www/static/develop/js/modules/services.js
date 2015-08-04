/*global define */
define([
    "marionette",
    "backbone"
], function (Marionette, Backbone) {
    'use strict';

    var ServiceModel = Backbone.Model.extend({

    });

    return Marionette.Module.extend({
        up: function(id, options) {
            var serviceModel = new ServiceModel();
            serviceModel.urlRoot = "/ajax/service_up/"+id;
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            serviceModel.save({}, {
                success: function(model) {
                    var resp = model.toJSON();
                    if (resp.code == 200) {
                        options.success(resp);
                    } else {
                        options.error(resp);
                    }
                    
                }
            });
        }
    });

});
