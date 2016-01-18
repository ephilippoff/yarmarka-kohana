define([ 'jquery', 'underscore', 'backbone'], function ($, _, Backbone) {

	return Backbone.View.extend({

		events: {
			'click [data-role=button]': 'onClick'
		},

		itemTemplate: _.template(
			'<div class="item">'
				+ '<p>'
					+ '<a href="<%= url %>" rel="nofollow">'
						+ '<img src="<%= image.url %>" alt="<%= image.alt %>" title="<%= image.title %>" />'
					+ '</a>'
				+ '</p>'
				+ '<span><%= price %> Р</span>'
				+ '<a href="<%= url %>" rel="nofollow"><%= shortTitle %></a>'
			+ '</div>'),

		initialize: function () {

			/* bind events */
			this.listenTo(this.model, 'change:items', this.onModelItemsChange);
			this.listenTo(this.model, 'change:page', this.onModelPageChange);

			/* save references to ui elements */
			this.$container = this.$('[data-role=container]');
			this.$buttonContainer = this.$('[data-role=button-container]');

			/* set model data */
			this.model.set('totalPages', this.$el.data('pages'));

		},

		render: function () {

		},

		/* event handlers */

		onClick: function () {
			this.model.incrementPage();
		},

		onModelItemsChange: function (model, value) {
			_.each(value, function (item, index) {
				this.$container.append(this.itemTemplate(item));
			}, this);
		},

		onModelPageChange: function (model, value) {
			if (value >= model.get('totalPages')) {
				this.$buttonContainer.hide();
			} else {
				this.$buttonContainer.show();
			}
		}

	});

});