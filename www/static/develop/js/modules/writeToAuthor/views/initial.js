define([ 'jquery', 'underscore', 'backbone' ], function ($, _, Backbone) {

	return Backbone.View.extend({

		tagName: 'button',

		className: 'button dib bg-color-blue white p10 br2 mt10',

		template: _.template('<% if (is_job_vacancy) { %>Откликнуться на вакансию<% } else { %>Написать автору<% } %>'),

		events: {
			'click': 'onUserClick'
		},

		initialize: function (options) {

		},

		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			return this.$el;
		},

		/* event handlers */
		onUserClick: function () {

			this.model.set('state', 'compose');

		}

	});

});