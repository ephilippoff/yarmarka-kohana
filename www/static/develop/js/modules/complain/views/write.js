define([ 'jquery', 'underscore', 'backbone', './user' ], function ($, _, Backbone, UserView) {

	return Backbone.View.extend({

		tagName: 'div',

		template: _.template(
				'<div data-role="validation-container"></div>'
				+ '<div data-role="user-view-container"></div>'
				+ '<div class="clearfix">'
					+ 'Причина для жалобы: <label data-role="label"></label>'
					+ '<textarea data-role="message" placeholder="Дополнительная информация (необязательно)"></textarea>'
					+ '<button data-role="submit">Отправить</button>'
					+ '<button data-role="cancel">Отменить</button>'
				+ '</div>'
			),

		events: {
			'click [data-role=submit]': 'onSubmitButtonClick',
			'click [data-role=cancel]': 'onCancelButtonClick'
		},

		initialize: function () {
			this.listenTo(this.model, 'change:subject', this.onModelSubjectChanged);
			this.listenTo(this.model, 'change:validationMessage', this.onModelValidationMessageChanged);
		},

		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			this.$userViewContainer = this.$('[data-role=user-view-container]');
			if (!this.userView) {
				this.userView = new UserView({ model: this.model });
			}
			this.$userViewContainer.append(this.userView.render());

			this.$validationContainer = this.$('[data-role=validation-container]');
			this.$message = this.$('[data-role=message]');
			this.$label = this.$('[data-role=label]');

			return this.$el;
		},

		show: function () {
			this.$el.slideDown();
			$('#complain').addClass('active');
			if (this.model.get('isGuest')) {
				this.userView.show();
			} else {
				this.userView.hide();
			}
		},

		hide: function () {
			this.$el.slideUp();
			$('#complain').removeClass('active');
		},

		onSubmitButtonClick: function (e) {
			e.preventDefault();

			this.model.set('message', this.$message.val());
			if (this.userView) {
				this.userView.updateModel();
			}
			this.model.set('validationMessage', null);
			this.model.save(null, {
				success: function(model, response, options) {
					if (!model.get('validationMessage')) {
						model.setThanksState();
					}
				}
			});
		},

		onCancelButtonClick: function (e) {
			e.preventDefault();
			this.model.setInitialState();
		},

		onModelSubjectChanged: function (model, value) {
			this.$label.html(value && typeof(value.get) === 'function'
				? value.get('title')
				: '');
		},

		onModelValidationMessageChanged: function (model, value) {
			this.$validationContainer.html(value);
		}

	});

});