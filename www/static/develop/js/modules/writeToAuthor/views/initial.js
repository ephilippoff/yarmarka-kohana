define([ 'jquery', 'underscore', 'backbone' ], function ($, _, Backbone) {

	return Backbone.View.extend({

		tagName: 'button',

		className: '',

		template: _.template('Написать автору'),

		events: {
			'click': 'onUserClick'
		},

		initialize: function (options) {

		},

		render: function () {
			this.$el.html(this.template());
			return this.$el;
		},

		/* event handlers */
		onUserClick: function () {

			this.model.set('state', 'compose');

		}

	});

});