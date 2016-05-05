define([ 'jquery', 'underscore', 'backbone' ], function ($, _, Backbone) {

	return Backbone.View.extend({

		tagName: 'a',

		className: 'span-link',

		template: _.template('<% if (is_job_vacancy) { %><i class="fa fa-reply" aria-hidden="true"></i> Откликнуться <span class="hidden-xs">на вакансию</span><% } else { %><i class="fa fa-reply" aria-hidden="true"></i> Оставить личное сообщение<% } %>'),

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