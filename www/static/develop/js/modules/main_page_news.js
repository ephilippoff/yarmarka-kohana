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

		nextButtonTemplate: _.template('<button data-role="next_page" class="more-button module button bg-color-blue">Еще новости</button>'),

		itemTemplate: _.template(
			'<div class="col-md-6 col-sm-6 masonry">'
				+ '<div class="news_wrap">'
					+ '<div class="date light fs12">'
					+ '<% var d = new Date(date*1000), fragmentsTime = [d.getHours(), d.getMinutes()]; fragments = [d.getDate(), d.getMonth() + 1, d.getFullYear() - 2000 ]; %>'
						+ '<% print(fragmentsTime.join(":"))+"&nbsp;" %>'
						+ ' <span><% print(fragments.join(".")); %></span>'
					+ '</div>'
					+ '<a href="<%= url %>" class="fs16"><%= title %></a>'
				+ '</div>'
			+ '</div>'),

		initialize: function () {
			// this.$nextButton = $(this.nextButtonTemplate());
			// this.$el.append(this.$nextButton);
			this.model = new Model();
			if (this.$el.data('category')) {
				this.model.set('category', this.$el.data('category'));
			}
			this.model.set('pages', this.$el.data('pages'));
			this.$appendContainer = $('#same_cat_news .row');
			this.render();

			this.listenTo(this.model, 'change:page', this.onModelPageChanged);
		},

		render: function () {
			if (this.$nextButton) {
				this.$nextButton.remove();
			}
			this.$nextButton = $(this.nextButtonTemplate());

			this.undelegateEvents();
			if (this.model.get('page') <= this.model.get('pages')) {
				this.$el.append(this.$nextButton);
				this.$nextButton.fadeIn(200);
			}else console.log(this.model.get('page'), this.model.get('pages'));
			this.delegateEvents();

		},

		addItem: function (item) {

			this.$appendContainer.append(this.itemTemplate(item));
			this.masonryItems();

		},

		masonryItems: function() {
			if ($('html').hasClass('desktop') || $('html').hasClass('tablet')) {
	            var $container = $('#same_cat_news');
	            $container.imagesLoaded( function() {
	                $('.news_wrap').each(function(){
	                    $(this).fadeIn(500);
	                });
	                $container.masonry('reloadItems');   
      				$container.masonry('layout'); 
	            });
	    	}else{
	    		$('.other_cat_news .news_wrap').show();
	    	}
		},

		// events handlers

		onNextButtonClick: function (e) {
			var currentPage = this.model.get('page');
			console.log(currentPage);
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