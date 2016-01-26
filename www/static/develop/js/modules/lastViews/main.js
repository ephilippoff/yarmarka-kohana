define([ 
		'jquery', 
		'underscore', 
		'backbone',

		//others
		'./models/showMore',
		'./views/showMore'

	], function ($, _, Backbone, ShowMoreModel, ShowMoreView) {

		return Backbone.View.extend({

			initialize: function () {
				if (this.$el.data('showmore')) {
					this.initializeShowMore();
				}
			},

			initializeShowMore: function () {
				this.showMoreModel = new ShowMoreModel();
				this.showMoreView = new ShowMoreView({
					el: this.$el,
					model: this.showMoreModel
				});
			}

		});

});