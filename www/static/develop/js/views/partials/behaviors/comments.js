/*global define */
define([
    'marionette',
    'templates'
], function (Marionette, templates) {
    'use strict';

    var CommentDeleteModel = Backbone.Model.extend({
        urlRoot: "/ajax/delete_comment",
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
                    options.success(model, res);
                    Backbone.emulateJSON = false;
                    app.settings.khQuery = true;
                },
                error: function(model, res){
                  Backbone.emulateJSON = false;
                  app.settings.khQuery = true;
                  options.success(model, res);
                }
            });
        }
    });

	return Marionette.Behavior.extend({
        ui: {
            newComment: ".js-new-comment",
            answerComment: ".js-answer-comment",
            deleteComment: ".js-delete-comment"
        },

        events: {
            "click @ui.newComment": "newCommentClick",
            "click @ui.answerComment": "answerCommentClick",
            "click @ui.deleteComment": "deleteCommentClick"
        },


        newCommentClick: function(e) {
            e.preventDefault();
            app.windows.vent.trigger("showWindow", "comment", {
                object_id: $(e.currentTarget).data("objectid")
            });
        },
        answerCommentClick: function(e) {
            e.preventDefault();
            app.windows.vent.trigger("showWindow", "comment", {
                object_id: $(e.currentTarget).data("objectid"),
                comment_id: $(e.currentTarget).data("commentid")
            });
        },
        deleteCommentClick:function(e) {
            e.preventDefault();
            if (!confirm("Вы действительно хотите удалить комментарий?")){
                return;
            }
            var model = new CommentDeleteModel({comment: $(e.currentTarget).data("commentid")});
            var parentId = $(e.currentTarget).data("parentid");
            Backbone.emulateJSON = true;
            app.settings.khQuery = false;
            model.prepare({
                params: {},
                 success: function(model) {
                     var resp = model.toJSON();
                     $(".li-cont"+parentId).remove();
                     $(e.currentTarget).closest(".li-cont").remove();
                     Backbone.emulateJSON = false;
                     app.settings.khQuery = true;
                     window.location.href = window.location.href.split("#")[0] + "#comments_place"
                 }
            });
        }
    });
});