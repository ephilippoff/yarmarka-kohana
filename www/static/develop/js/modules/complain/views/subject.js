define([ 'jquery', 'underscore', 'backbone' ], function ($, _, Backbone) {
	return Backbone.View.extend({

		tagName: 'li',

		template: _.template('<a href="#" data-role="select"><%= title %></a>'),

		events: {
			'click [data-role=select]': 'onSelectButtonClick',
			
		},

		initialize: function () {

		},

		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			return this.$el;
		},

		onSelectButtonClick: function (e) {
			e.preventDefault();
			this.model.trigger('select', this.model);
		}

	});
});