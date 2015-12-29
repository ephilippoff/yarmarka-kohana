define([ 'underscore', 'backbone' ], function (_, Backbone) {

	return Backbone.View.extend({

		tagName: 'div',

		className: 'container-fluid',

		template: _.template(
				'<div class="row mt10">'
					+ '<div class="col-md-12">'
						+ '<textarea data-role="message"></textarea>'
					+ '</div>'
					+ '<button data-role="send" class="button dib bg-color-blue white p10 br2 mr10">Отправить</button>'
					+ '<button data-role="reset" class="button dib bg-color-blue white p10 br2">Отменить</button>'
				+ '</div>'
			),

		events: {

			'click [data-role=send]': 'onSendClick',
			'click [data-role=reset]': 'onResetClick'

		},

		initialize: function () {

		},

		render: function () {

			/* render template */
			this.$el.html(this.template());

			/* save references to ui elements */
			this.$message = this.$('[data-role=message]');

			return this.$el;

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

		onResetClick: function () {

			this.model.set('state', 'initial');

		}


	});

});