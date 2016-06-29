define([ 'jquery', 'underscore', 'backbone', 'templates' ], function ($, _, Backbone, templates) {

	return Backbone.View.extend({

		tagName: 'div',
		// className: "tabs-wrap ml15",

		events: {
		   
		},

		initialize: function () {
			this.render();
		},

		render: function () {
			this.$el.html(this.model.getTemplate(this.model.toJSON()));
		}

	});

});