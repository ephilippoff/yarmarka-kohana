define([ 'underscore', 'backbone' ], function (_, Backbone) {

	return Backbone.Model.extend({

		defaults: {
			num: null,
			last_comment: null,
			date: null,
			sign: null
		},

		url: '/rest_VkMessagesNotifier/submit'

	});

});