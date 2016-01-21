define([ 'jquery', 'underscore', 'backbone' ], function ($, _, Backbone) {

	return Backbone.View.extend({

		tagName: 'div',

		template: _.template(
				'Спасибо, <%= userName %>, Ваше обращение отправлено.<br />' + 
				'Благодарим вас за то, что Вы помогаете сделать "Ярмарку" лучше.<br />' + 
				'Наш специалист сообщит Вам о рассмотрении Вашего обращения в ближайшее время.'
			),

		initialize: function () {

		},

		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			return this.$el;
		},

		show: function () { this.$el.slideDown(); }, 

		hide: function () { this.$el.slideUp(); }

	});

});