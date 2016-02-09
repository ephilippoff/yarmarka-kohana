define([ 'underscore', 'backbone' ], function (_, Backbone) {
	return Backbone.Model.extend({
		defaults: {
			fields: null,
			state: null,
			method: null,
			error: null
		},

		url: function () {
			return '/rest_subscription/' + this.get('method');
		},

		setStateSaveConfirm: function () {
			this.setState('save_confirm', 'saveConfirm');
		},

		setStateSave: function () {
			this.setState('save', 'save');
		},

		setState: function (method, state) {
			this.save({ method: method }, {
				success: function (model, response, options) {
					if (!model.get('error')) {
						model.set('state', state);
					} 
				}
			});
		}
	});
});