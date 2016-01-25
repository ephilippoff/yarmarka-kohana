define([ 'jquery', 'underscore', 'backbone', './models/state', './views/initial', './views/saveConfirm', './views/save' ], function ($, _, Backbone, StateModel, InitialView, SaveConfirmView, SaveView) {

	var state2viewCtor = {
		'initial': InitialView,
		'saveConfirm': SaveConfirmView,
		'save': SaveView
	};

	return Backbone.View.extend({

		childView: null,
		templated: null,

		template: _.template('<div data-role="error" style="color:red;"><%= error %></div>'),

		initialize: function () {
			this.model = new StateModel();

			this.listenTo(this.model, 'change:state', this.onModelStateChanged);
			this.listenTo(this.model, 'change:error', this.onModelErrorChanged);

			this.model.set('state', 'initial');
		},

		render: function () {
			this.releaseTemplated();
			this.$templated = $(this.template(this.model.toJSON()));
			this.$el.append(this.$templated);

			var state = this.model.get('state');
			this.releaseChildView();
			this.setChildView(this.childViewFactory(state));
		},

		releaseChildView: function () {
			if (this.childView) {
				this.childView.remove();
				this.childView = null;
			}
		},

		releaseTemplated: function () {
			if (this.$templated) {
				this.$templated.remove();
				this.$templated = null;
			}
		},

		childViewFactory: function (name) {
			var viewCtor = state2viewCtor[name];
			if (!viewCtor) {
				throw new Error('viewCtor is null');
			}
			return new viewCtor({
				model: this.model
			});
		},

		setChildView: function (view) {
			this.childView = view;
			this.$el.append(this.childView.render());
		},

		remove: function () {
			this.releaseChildView();
			Backbone.View.prototype.remove.apply(this, arguments);
		},

		onModelStateChanged: function (model, value) {
			this.render();
		},

		onModelErrorChanged: function (model, value) {
			this.render();
		}

	});
});