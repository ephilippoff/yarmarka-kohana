define([ 'jquery', 'underscore', 'backbone', './models/state', './views/initial', './views/saveConfirm', './views/save' ], function ($, _, Backbone, StateModel, InitialView, SaveConfirmView, SaveView) {

	var state2viewCtor = {
		'initial': InitialView,
		'saveConfirm': SaveConfirmView,
		'save': SaveView
	};

	return Backbone.View.extend({

		childView: null,
		templated: null,

		template: _.template('<div data-role="error" style="color:red;"><b><%= error %></b></div>'),

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
			this.$el.find('span').remove();

			var state = this.model.get('state');
			this.releaseChildView();
			this.setChildView(this.childViewFactory(state));
		},

		releaseChildView: function () {
			if ( (this.childView) && (this.model.get('state') != 'saveConfirm')) {
				this.childView.$el.fadeOut(300);
				me = this;
				setTimeout(function(){
					if (this.childView) this.childView.remove();
					this.childView = null;
				}, 300);
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
			if (this.model.get('renderAgain') == false) {
				this.$el.find('a[data-role="save"]').remove();
				this.model.set('renderAgain', true);
			}
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
			this.model.set('renderAgain', false);
			this.render();
			$('div[data-role="error"]').fadeIn().delay(1000).fadeOut();
		}

	});
});