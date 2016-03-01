define([ 'jquery', 'underscore', 'backbone' ], function () {

	var Model = Backbone.Model.extend({
		defaults: {
			page: 1,
			perPage: 6,
			error: null,
			result: null
		},
		url: '/rest_object/main_page_news'
	});

	return Backbone.View.extend({

		events: {
			'click [data-role=next_page]': 'onNextButtonClick'
		},

		nextButtonTemplate: _.template('<button data-role="next_page">Next</button>'),

		itemTemplate: _.template(
			'<div class="col-md-12 col-sm-6 other_cat_news">'
				+ '<div class="date light fs12">'
					+ '<%= new Date(date * 1000) %>'
				+ '</div>'
				+ '<a href="<%= url %>"><%= title %></a>'
			+ '</div>'),

		initialize: function () {
			this.model = new Model();

			if (this.$el.data('category')) {
				this.model.set('category', this.$el.data('category'));
			}
			this.model.set('pages', this.$el.data('pages'));
			this.$appendContainer = this.$el.find('[data-role=append_container]');
			this.render();

			this.listenTo(this.model, 'change:page', this.onModelPageChanged);
		},

		render: function () {

			if (this.$nextButton) {
				this.$nextButton.remove();
			}
			this.$nextButton = $(this.nextButtonTemplate());

			this.undelegateEvents();
			if (this.model.get('page') < this.model.get('pages')) {
				this.$el.append(this.$nextButton);
			}
			this.delegateEvents();

		},

		addItem: function (item) {

			this.$appendContainer.append(this.itemTemplate(item));

		},

		// events handlers

		onNextButtonClick: function (e) {
			var currentPage = this.model.get('page');
			this.model.set('page', currentPage + 1);
		},

		onModelPageChanged: function (model, page) {
			var me = this;
			model.save(null, {
				success: function () {
					me.onModelSyncDone();
				}
			});
		},

		onModelSyncDone: function (model) {
			var res = this.model.get('result');
			_.each(res, function (item) {
				this.addItem(item);
			}, this);
			this.render();
		}

	});

});