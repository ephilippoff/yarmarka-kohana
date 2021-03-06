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

        publishUnpublishGroup: function(ids, to_publish, all, options) {
            var controlModel = new ControlModel();
            controlModel.urlRoot = "/rest_object/group_publishun";
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            
            controlModel.save({ids: ids, to_publish: to_publish, all: all}, {
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
        },

        remove: function(id, options) {
            var controlModel = new ControlModel();
            controlModel.urlRoot = "/rest_object/remove/"+id;
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

        contacts: function(id, options) {
            var controlModel = new ControlModel();
            controlModel.urlRoot = "/rest_object/show_contacts";
            options.error = options.error || function() {};
            options.success = options.success || function() {};
            options.captcha = options.captcha || function() {};
            controlModel.save({id: id, captcha: options.code}, {
                success: function(model) {
                    var resp = model.toJSON();
                    if (resp.code == 200) {
                        options.success(resp.result);
                    } else if (resp.code == 300) {
                        options.captcha(resp.result);
                    } else {
                        options.error(resp.error);
                    }
                    
                }
            });
        },

        moderateAction: function(id, params) {
            params = params || {};

            var controlModel = new ControlModel();
            controlModel.urlRoot = "/rest_object/moderate_reasons";
            params.error = params.error || function() {};
            params.success = params.success || function() {};
            console.log(id, params)
            controlModel.save({id: id, params: params}, {
                success: function(model) {
                    var resp = model.toJSON();
                    if (resp.code == 200) {
                        app.windows.vent.trigger("showWindow","moderate", {
                            id: id,
                            reasons: resp.result,
                            params: params
                        });
                    } else {
                        params.error(resp.error);
                    }
                }
            });
            
            
        },
    });

});
