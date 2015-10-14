/*global define */
define([
    'marionette',
    'templates'
], function (Marionette, templates) {
    'use strict';

    var CommentModel = Backbone.Model.extend({
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
            deleteComment: ".js-delete-comment",
            moderateComment: ".js-moderate-comment"
        },

        events: {
            "click @ui.newComment": "newCommentClick",
            "click @ui.answerComment": "answerCommentClick",
            "click @ui.deleteComment": "deleteCommentClick",
            "click @ui.moderateComment": "moderateCommentClick"
        },


        newCommentClick: function(e) {
            e.preventDefault();
            app.windows.vent.trigger("showWindow", "comment", {
                object_id: $(e.currentTarget).data("objectid"),
                email: $(e.currentTarget).data("email")
            });
        },
        answerCommentClick: function(e) {
            e.preventDefault();
            app.windows.vent.trigger("showWindow", "comment", {
                object_id: $(e.currentTarget).data("objectid"),
                comment_id: $(e.currentTarget).data("commentid"),
                email: $(e.currentTarget).data("email")
            });
        },
        deleteCommentClick:function(e) {
            e.preventDefault();
            if (!confirm("Вы действительно хотите удалить комментарий?")){
                return;
            }
            var parentId = $(e.currentTarget).data("parentid");
            var commentId = $(e.currentTarget).data("commentid");

            var model = new CommentModel({comment: commentId});
            model.urlRoot = "/ajax/delete_comment";
            
            Backbone.emulateJSON = true;
            app.settings.khQuery = false;
            model.prepare({
                params: {},
                 success: function(model) {
                     var resp = model.toJSON();
                     if (!parentId) { 
                        $(".li-cont"+commentId).remove();
                     }
                     $(e.currentTarget).closest(".li-cont").remove();
                     Backbone.emulateJSON = false;
                     app.settings.khQuery = true;
                 }
            });
        },
        moderateCommentClick:function(e) {
            e.preventDefault();
            var model = new CommentModel({comment: $(e.currentTarget).data("commentid"), show: true});
            model.urlRoot = "/ajax/moderate_comment";
            var parentId = $(e.currentTarget).data("parentid");
            Backbone.emulateJSON = true;
            app.settings.khQuery = false;
            model.prepare({
                params: {},
                 success: function(model) {
                     var resp = model.toJSON();
                     $(".li-cont"+parentId).removeClass("byellow");
                     $(e.currentTarget).closest(".li-cont").removeClass("byellow");
                     Backbone.emulateJSON = false;
                     app.settings.khQuery = true;
                 }
            });
        }
    });
});