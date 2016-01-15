define([ 'underscore', 'backbone' ], function (_, Backbone) {
	return Backbone.Model.extend({
		defaults: {
			fields: null,
			state: null,
			method: null
		},

		url: function () {
			return '/rest_subscription/' + this.get('method');
		},

		setStateSaveConfirm: function () {
			this.save({ method: 'save_confirm' }, {
				success: function (model, response, options) {
					model.set('state', 'saveConfirm');
				}
			});
		}
	});
});