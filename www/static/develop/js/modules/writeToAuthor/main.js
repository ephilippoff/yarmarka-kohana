define([ 'underscore', 'backbone', './models/state', './views/initial', './views/compose' ], function (_, Backbone, AppStateModel, InitialView, ComposeView) {

	return Backbone.View.extend({

		viewsMap: {
			'initial': InitialView,
			'compose': ComposeView
		},

		currentView: null,

		initialize: function (options) {

			/* initialize models */
			this.appState = new AppStateModel({
				object_id: this.$el.data('id')
			});

			/* bind model events */
			this.listenTo(this.appState, 'change:state', this.onAppStateChanged);

			/* force call render */
		},

		render: function () {

			/* destroy old view */
			if (this.currentView) {
				this.currentView.remove();
			}

			/* create new one */
			this.currentView = this.getCurrentView();
			this.$el.empty().append(this.currentView.render());

			return this.$el;
		},

		getCurrentView: function () {

			var currentState = this.appState.get('state');
			if (!this.viewsMap[currentState]) {
				throw new Error('Cannot find view for ' + currentState);
			}
			return new this.viewsMap[currentState]({
				model: this.appState
			});

		},

		/* events handlers */
		onAppStateChanged: function (value) {

			this.render();

		}

	});

});