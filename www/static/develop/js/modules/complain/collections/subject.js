define(
	[ 'underscore', 'backbone', '../models/subject' ], 
	function (_, Backbone, Subject) {
		return Backbone.Collection.extend({
			model: Subject,
			url: '/rest_complain/subject'
		});
});