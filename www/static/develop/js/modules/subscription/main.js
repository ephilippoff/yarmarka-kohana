define([ 'jquery', 'underscore', 'backbone', './models/state', './views/initial' ], function ($, _, Backbone, StateModel, InitialView) {

	var state2viewCtor = {
		'initial': InitialView
	};

	return Backbone.View.extend({

		childView: null,

		initialize: function () {
			this.model = new StateModel();

			this.listenTo(this.model, 'change:state', this.onModelStateChanged);

			this.model.set('state', 'initial');
		},

		render: function () {
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
		}

	});
});