define([ 'jquery', 'underscore', 'backbone', './models/state' ], function ($, _, Backbone, StateModel) {

	var Export = function () {
		this.initialize();
	};

	_.extend(Export.prototype, {

		initialize: function () {

			/* bind event */
			VK.Observer.subscribe('widgets.comments.new_comment', this.onWidgetNewComment.bind(this));

		},

		onWidgetNewComment: function (num, lastComment, date, sign) {
			/* prepare model */
			var model = new StateModel({
				sign: sign,
				last_comment: lastComment,
				date: date,
				num: num
			});

			/* save model */
			model.save(null, {
				success: function () {
					alert('Автор получит уведомление о сообщении на почту.');
				}
			});
		}

	});

	return Export;

});