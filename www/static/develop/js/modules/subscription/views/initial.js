define([ 'jquery', 'underscore', 'backbone' ], function($, _, Backbone) {
	return Backbone.View.extend({

		template: _.template('<a href="#" data-role="save">Сохранить поиск</a>'),

		events: {
			'click [data-role=save]': 'onSaveButtonClick'
		},

		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			return this.$el;
		},

		onSaveButtonClick: function() {
			this.model.setStateSaveConfirm();
		}

	});
});