define([ 'jquery', 'underscore', 'backbone' ], function ($, _, Backbone) {

	return Backbone.View.extend({

		className: 'save-complete ta-c',

		template: _.template(
				'Ваша подписка успешно оформлена!'
				+ '<a class="button bg-color-blue more-button" href="/user/subscriptions">Ваши подписки</a>'
			),

		initialize: function () {},

		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			this.$el.fadeIn(300);
			me = this;
			setTimeout(function(){
				me.model.set('renderAgain', false);
				me.model.set('state', 'initial');
			}, 2000);
			return this.$el;
		}

	});

});