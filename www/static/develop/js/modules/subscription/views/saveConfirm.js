define([ 'jquery', 'underscore', 'backbone' ], function ($, _, Backbone) {

	return Backbone.View.extend({

		template: _.template(
				'<div>Я хочу получать информацию о новых объявлениях:</div>'
				+ '<div><%= info.title %></div>'
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
				+ '<div>На электронную почту <a href="mailto:<%= info.email %>"><%= info.email %></a></div>'
				+ '<div>'
					+ '<button data-role="save">Сохранить</button>'
					+ '<button data-role="cancel">Отменить</button>'
				+ '</div>'
			),

		events: {
			'click [data-role=save]': 'onSaveButtonClick',
			'click [data-role=cancel]': 'onCancelButtonClick'
		},

		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			return this.$el;
		},

		onSaveButtonClick: function (e) {
			e.preventDefault();
			this.model.setStateSave();
		},

		onCancelButtonClick: function (e) {
			e.preventDefault();
			this.model.set('state', 'initial');
		}

	});

});