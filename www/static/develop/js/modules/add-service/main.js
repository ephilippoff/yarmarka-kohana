define(
	[
		'jquery',
		'underscore',
		'backbone',
		'./models/state',
		'./views/tabs',
		'templates',
		'./views/service'
	],
	function (
			$,
			_,
			Backbone,
			StateModel,
			TabsView,
			templates,
			ServiceView

		) {

		return Backbone.View.extend({

			className: "document-row",

			container: $('.service-wrap'),

			events: {
				'click .tab-item': 'onTabClicked',
			},

			initialize: function () {

				this.model = new StateModel();

				this.$tabs = new TabsView({model: this.model});
				this.$el.prepend(this.$tabs.$el);

				this.render();
			},

			render: function () {

				this.setActiveTab();
				this.editButtonText();

				new ServiceView({
					model: this.model,
					el: this.container
				});

			},

			onTabClicked: function (e) {
				newView = $(e.currentTarget).data('service');
				this.model.setState(newView);
				this.render();
			},

			setActiveTab: function(){
				s = this;
				this.$el.find('.tab-item').each(function(){
					var data = $(this).data('service');
					if (data == s.model.get('active')) {
						$(this).addClass('active');
					}else $(this).removeClass('active');
				});
			},

			editButtonText: function(){
				var service = $('.tab-item[data-service='+this.model.get('state')+']').html();
				if (this.model.get('state') == 'free') {
					$('.js-button-submit').text('Разместить объявление бесплатно');
				}else $('.js-button-submit').text('Разместить объявление с услугой "'+service+'"');
			},

		});

	});