define([ 'underscore', 'backbone' ], function (_, Backbone) {

	return Backbone.Model.extend({

		defaults: {
			subject: null,
			userEmail: null,
			userName: null,
			state: null,
			isGuest: null,
			objectId: null,
			validationMessage: null
		},

		url: '/rest_complain/send',

		setInitialState: function () {
			this.set('state', 'initial');
			this.resetSubjectSilent();
		},

		setWriteState: function () {
			this.set('state', 'write');
		},

		setSelectState: function () {
			this.set('state', 'select');
			this.resetSubjectSilent();
		},

		setThanksState: function () {
			this.set('state', 'thanks');
		},

		setRemoveAllow: function () {
			this.set('remove', true);
		},

		resetSubjectSilent: function () {
			this.set({ subject: null }, { silent: true });
		}

	});

});