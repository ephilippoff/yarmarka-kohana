define([ 'underscore', 'backbone' ], function (_, Backbone) {
	return Backbone.Model.extend({
		defaults: {
			id: -1,
			title: '(EMPTY)'
		}
	});
});