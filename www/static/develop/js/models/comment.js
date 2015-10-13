define([
    "marionette",
    'base/validation'
], 
function (Marionette, validation) {
    "use strict";
    
    return Backbone.Model.extend({
        urlRoot: "/ajax/add_comment",
        settings: {
            username : ["require"],
            email : ["require"],
            text : ["require"]
        },
        defaults: {
            username : null,
            email : null,
            text : null
        },

        validate: function (attrs, options) {
            var s = this, result, errors = [];
            _.each(_.keys(attrs), function(item){
                result = validation.isValid(s.settings[item], item, attrs[item]);
                if (result) {
                    errors.push(result);
                }
            });
            return (errors.length) ? errors : false;
        },
        save: function (attributes, options) {
            options       = options || {};
            attributes    = attributes || {};
            options.data  = this.toJSON();
            return Backbone.Model.prototype.save.call(this, attributes, options);
        },
        prepare: function(options) {
            var s =this;
            options.success = options.success || function(){};
            options.error = options.error || function(){};
            options.params = options.params || {};
            this.on("error", function(model, res){
                var errorMsg = (res.responseJSON) ? res.responseJSON.error.message : res.error.message;
                options.error(errorMsg);
            });
            Backbone.emulateJSON = true;
            app.settings.khQuery = false;

            this.save(options.params,{
                success: function(model, res) {
                    if (model.get("error")) {
                        s.trigger("error", model, res);
                    } else {
                        options.success(model, res);
                    }
                    Backbone.emulateJSON = false;
                    app.settings.khQuery = true;
                },
                error: function(){
                  Backbone.emulateJSON = false;
                  app.settings.khQuery = true;
                }
            });
        }
    });
});