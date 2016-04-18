define([ 'jquery', 'underscore', 'backbone' ], function ($, _, Backbone) {

	return Backbone.View.extend({

		className: 'save-confirm',

		template: _.template(
				'<div>Я хочу получать информацию о новых объявлениях: <b><%= info.title %></b></div>'
				+ '<ul>'
					+ '<% _.each(info.attributes, function (item) { %>'
						+ '<li>'
							+ '<b><%= item.title %>: </b>'
							+ '<% if (item.type == \'list\') { %>'
								+ '<% if (Array.isArray(item.value)) { %>'
									+ '<%= _.map(item.value, function (x) { return x.title; }).join(\', \') %>'
								+ '<% } else { %>'
									+ '<%= item.value.title %>'
								+ '<% } %>'
							+ '<% } else if (item.type == \'integer\') { %>'
								+ '<% if (item.value.min) { %> от <%= item.value.min %><% } %>'
								+ '<% if (item.value.max) { %> до <%= item.value.max %><% } %>'
							+ '<% } %>'
						+ '</li>'
					+ '<% }); %>'
					+ '<% if (info.search_text) { %><li><b>Строка поиска: </b><%= info.search_text %></li><% } %>'
					+ '<% if (info.with_photo) { %><li><b>Есть фото: </b>Да</li><% } %>'
					+ '<% if (info.only_private) { %><li><b>Только частные: </b>Да</li><% } %>'
				+ '</ul>'
				+ '<div>Электронная почта: <a href="mailto:<%= info.email %>"><%= info.email %></a></div>'
				+ '<div>'
					+ '<button data-role="save" class="button bg-color-blue more-button">Сохранить</button>'
					+ '<button data-role="cancel" class="button bg-color-blue more-button">Отменить</button>'
				+ '</div>'
			),

		events: {
			'click [data-role=save]': 'onSaveButtonClick',
			'click [data-role=cancel]': 'onCancelButtonClick'
		},

		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			this.$el.fadeIn(300);
			return this.$el;
		},

		onSaveButtonClick: function (e) {
			e.preventDefault();
			this.model.setStateSave();
		},

		onCancelButtonClick: function (e) {
			e.preventDefault();
			this.model.set('renderAgain', false);
			this.model.set('state', 'initial');
		}

	});

});