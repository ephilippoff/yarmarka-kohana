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
				object_id: this.$el.data('id'),
				email: this.$el.data('email'),
				is_job_vacancy: this.$el.data('is-job-vacancy') == 1,
				message: this.$el.data('text'),
				state: this.$el.data('state') || 'initial'
			});

			/* bind model events */
			this.listenTo(this.appState, 'change:state', this.onAppStateChanged);

			/* force call render */
		},

		render: function () {
			/* destroy old view */
			console.log(this.appState.get('state'));
			if (this.currentView && this.appState.get('state') == 'initial') {
				this.currentView.remove();
			}

			/* create new one */
			this.currentView = this.getCurrentView();
			if (this.appState.get('doNotShowAgain') == true && this.appState.get('state') == 'initial') {
				return;
			}else this.$el.append(this.currentView.render());

			this.appState.set('doNotShowAgain', true);

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