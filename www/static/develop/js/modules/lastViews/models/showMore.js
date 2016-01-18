define([ 'jquery', 'underscore', 'backbone'], function ($, _, Backbone) {

	return Backbone.Model.extend({

		defaults: {
			page: 1,
			totalPages: 1,
			perPage: 5,
			mode: 'json',
			items: []
		},

		url: '/block_twig/last_views',

		parse: function (response, options) {
			this.set('page', response.result.pagination.page);
			this.set('totalPages', Math.ceil(response.result.pagination.total / response.result.pagination.perPage));
			this.set('items', response.result.items);
		},

		incrementPage: function () {
			var currentPage = this.get('page');
			if (currentPage >= this.get('totalPages')) {
				return;
			}
			this.set('page', currentPage + 1);
			this.save();
		}

	});

});