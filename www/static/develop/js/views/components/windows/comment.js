/*global define */
define([
    'marionette',
    'templates',
    'base/validation',
    'models/comment'
], function (Marionette, templates, validation, CommentModel) {
    'use strict';

    return Marionette.ItemView.extend({
        template: templates.components.windows.comment,
        className: "popup-wrp z400",
        errors: {},
        ui: {
            ok: ".js-ok",
            close: ".js-close",
            form: ".js-form"
        },

        events: {
            'click @ui.ok': 'ok',
            "click @ui.close": "close"
        },

        close: function() {
            app.windows.vent.trigger("closeWindow","comment");
        },

        templateHelpers: function() {
            var s = this;
            return {
               getError: function(name){
                    return (_.has(s.errors, name) && s.errors[name]) ? s.errors[name] : false;
                }
            }
        },

        initialize: function(options) {
            this.bindUIElements();
            this.model = new CommentModel({
                objectId: options.object_id,
                commentId: options.comment_id
            });
        },

        ok: function (e) {
           e.preventDefault();
          var s = this, f = {};
          this.ui.form.serializeArray().map(function(x){f[x.name] = x.value;});
          console.log(f);
          this.model.set(f);
          if ( !this.model.isValid() ) {
              s.errors = {};
              _.each(this.model.validationError, function(item, key){
                  s.errors[item.name] = item.text;
              });
              this.render();
          } else {
             this.model.prepare({
                  params: this.model.toJSON(),
                  success: function(model) {
                      var resp = model.toJSON();
                      if (resp.code == 200) {
                          console.log(true);
                          s.close();
                           window.location.href = window.location.href.split("#")[0] + "#comments_place";
                          location.reload(true);
                         
                      } else {
                          s.errors["common"] = "Ошибка при сохранении";
                          s.render();
                      }
                  },
                  error: function(model) {
                      s.errors["common"] = "Ошибка при сохранении";
                      s.render();
                  }
             });
          }
        }
    });

});