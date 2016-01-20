define(
	[ 'jquery', 'underscore', 'backbone', './subject' ], 
	function ($, _, Backbone, SubjectView) {
		return Backbone.View.extend({

			tagName: 'ul',

			initialize: function () {
				this.listenTo(this.collection, 'add', this.onCollectionAdd);
			},

			render: function () {
				return this.$el;
			},

			addItem: function (model) {
				var view = new SubjectView({
					model: model
				});
				this.$el.append(view.render());
			},

			onCollectionAdd: function (model, collection, options) {
				this.addItem(model);
			},

			show: function () {
				this.$el.show();
			},

			hide: function () {
				this.$el.hide();
			}

		});
	});