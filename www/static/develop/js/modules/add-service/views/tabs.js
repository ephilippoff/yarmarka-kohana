define([ 'jquery', 'underscore', 'backbone' ], function ($, _, Backbone) {

	return Backbone.View.extend({

		tagName: 'div',
		className: "tabs-wrap clearfix mt15",

		template: _.template(
			'<h2 style="font-size: 18px;" class="mb20">Выберите начальную услугу</h2>'+
			'<div class="tab-item bold" data-service="premium">Премиум</div>'+
			'<div class="tab-item bold" data-service="lider">Лидер</div>'+
			'<div class="tab-item bold" data-service="up">Подъем</div>'+
			'<div class="tab-item" data-service="free">Бесплатно</div>'
		),

		item: $('.tab-item'),

		events: {

			// 'click': 'onTabClicked'
		   
		},

		initialize: function () {
			// this.listenTo(this.model, 'change:active', this.changeActiveItem);
			this.render();
		},

		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			$('').after(this.$el);
		}


	});

});