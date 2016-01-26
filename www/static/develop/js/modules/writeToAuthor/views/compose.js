define([ 'underscore', 'backbone' ], function (_, Backbone) {

	return Backbone.View.extend({

		tagName: 'div',

		className: 'container-fluid',

		template: _.template(
				'<div class="mt10">Напишите автору объявления</div>'
				+ '<div class="row">'
					+ '<div class="col-md-12">'
						+ '<textarea data-role="message"><%= message %></textarea>'
					+ '</div>'
				+ '</div>'
				+ '<% if (is_job_vacancy) { %>'
					+ '<div class="is-job-vacancy">'
						+ 'Прикрепите свое <a href="#" data-role="select-cv">резюме</a> или <a href="#" data-role="create-cv">создайте</a> его на Ярмарке'
					+ '</div>'
				+ '<% } %>'
				+ '<div>'
					+ '<span style="white-space:nowrap;">Указанный при регистрации электронный адрес:</span><br />'
					+ '<a href="#" data-role="email"></a><br />'
					+ '<span>будет отправлен автору для связи с Вами</span>'
				+ '</div>'
				+ '<div class="mt10" style="text-align:center;">'
					+ '<button data-role="send" class="button dib bg-color-blue white p10 br2 mr10">Отправить</button>'
					+ '<button data-role="reset" class="button dib bg-color-blue white p10 br2">Отменить</button>'
				+ '</div>'
			),

		events: {

			'click [data-role=send]': 'onSendClick',
			'click [data-role=reset]': 'onResetClick'

		},

		initialize: function () {

			/* bind model events */
			this.listenTo(this.model, 'change:email', this.onEmailChanged);

		},

		render: function () {

			/* render template */
			this.$el.html(this.template(this.model.toJSON()));

			/* save references to ui elements */
			this.$message = this.$('[data-role=message]');
			this.$emailLink = this.$('[data-role=email]');

			this.$('[data-role=create-cv]').attr('href', this.prepareCvUrl('create'));
			this.$('[data-role=select-cv]').attr('href', this.prepareCvUrl('select'));

			this.setUiEmail(this.model.get('email'));

			return this.$el;

		},

		prepareCvUrl: function (mode) {
			return '/detail/' + mode + '_cv?object_id=' + this.model.get('object_id'); 
		},

		/* api */
		setUiEmail: function (value) {
			this.$emailLink.html(value);
			this.$emailLink.attr('href', 'mailto:' + value);
		},

		/* events handlers */
		onSendClick: function () {

			var me = this;
			this.model.set('message', this.$message.val());
			this.model.save(null, {
				success: function () {
					alert('Сообщение успешно отправлено');
					me.onResetClick();
				}
			});

		},

		onEmailChanged: function (model, value) {

			this.setUiEmail(value);

		},

		onResetClick: function () {

			this.model.set('state', 'initial');

		}


	});

});