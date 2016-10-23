define([ 'jquery', 'underscore', 'backbone' ], function($, _, Backbone) {
	return Backbone.View.extend({

		template: _.template('<a class="red" href="#" data-role="save">Подписка на обновление</a>'),

		events: {
			'click [data-role=save]': 'onSaveButtonClick'
		},

		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			return this.$el;
		},

		onSaveButtonClick: function(e) {
			e.preventDefault();
			this.model.setStateSaveConfirm();
		}

	});
});