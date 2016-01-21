define([ 'jquery', 'underscore', 'backbone' ], function ($, _, Backbone) {
	return Backbone.View.extend({

		tagName: 'div',

		template: _.template(
				'<div class="clearfix module contact_form">'
					+'<label class="mr5">Имя</label>'
					+ '<input type="text" data-role="name" />'
				+ '</div>'
				+ '<div class="clearfix module contact_form">'
					+ '<label class="mr5">Email</label>'
					+ '<input type="text" data-role="email" />'
				+ '</div>'
			),

		initialize: function () {

		},

		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			this.$name = this.$('[data-role=name]');
			this.$email = this.$('[data-role=email]');
			return this.$el;
		},

		show: function () { this.$el.show(); },

		hide: function () { this.$el.hide(); },

		updateModel: function () {
			this.model.set('userName', this.$name.val());
			this.model.set('userEmail', this.$email.val());
		}

	});
});