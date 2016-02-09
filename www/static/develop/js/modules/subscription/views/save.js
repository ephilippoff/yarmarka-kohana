define([ 'jquery', 'underscore', 'backbone' ], function ($, _, Backbone) {

	return Backbone.View.extend({

		template: _.template(
				'<div>Ваша подписка успешно оформлена!</div>'
				+ '<div>Управление подписками доступно в <a href="/user/subscriptions">личном кабинете</a></div>'
			),

		initialize: function () {},

		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			return this.$el;
		}

	});

});