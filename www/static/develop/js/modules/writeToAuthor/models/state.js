define([ 'underscore', 'backbone' ], function (_, Backbone) {

	/* 
		States definition:
			- initial: show only one button "Send message"
			- compose: show text area with submit button
			- validate: show validation message
	*/


	return Backbone.Model.extend({

		states: [ 'initial', 'compose', 'validate' ],

		defaults: {
			state: 'initial',
			message: ''
		},

		url: '/rest_object/write_to_author',

		validate: function (attributes, options) {
			if (_.indexOf(this.states, attributes.state) === -1) {
				return 'Bad state ' + attributes.state;
			}
		}

	});

});