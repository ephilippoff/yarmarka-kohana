define(
	[
		'jquery',
		'underscore',
		'backbone',

		'./models/state',
		'./views/write',
		'./collections/subject',
		'./views/subjects',
		'./views/thanks'
	],
	function (
			$,
			_,
			Backbone,

			StateModel,
			WriteView,
			SubjectCollection,
			SubjectsView,
			ThanksView
		) {

		return Backbone.View.extend({

			events: {
				'click [data-role=start]': 'onStartButtonClick',
			},

			initialize: function () {

				this.model = new StateModel();

				this.subjects = new SubjectCollection();
				this.subjectsView = new SubjectsView({ collection: this.subjects });
				this.$el.append(this.subjectsView.render());
				this.writeView = new WriteView({ model: this.model });
				$('.dialog_window').append(this.writeView.render());
				this.thanksView = new ThanksView({ model: this.model });
				$('.dialog_window').append(this.thanksView.render());
				
				this.listenTo(this.subjects, 'add', this.onCollectionAdd);
				this.listenTo(this.model, 'change:state', this.onModelStateChanged);
				this.listenTo(this.model, 'change:subject', this.onModelSubjectChanged);

				this.subjects.fetch();
				this.model.setInitialState();
				this.model.set('isGuest', this.$el.data('is-guest'));
				this.model.set('objectId', this.$el.data('object-id'));
			},

			render: function () {

			},

			onModelStateChanged: function (model, value) {
				if (value == 'initial') {
					this.subjectsView.hide();
					this.writeView.hide();
				}

				if (value == 'select') {
					this.subjectsView.show();
				}

				if (value == 'write') {
					this.subjectsView.hide();
					this.thanksView.hide();
					this.writeView.show();
				}

				if (value == 'thanks') {
					this.subjectsView.hide();
					this.writeView.hide();
					this.thanksView.show();	
					this.thanksView.render();
				}
			},

			onModelSubjectChanged: function (model, value) {
				this.model.setWriteState();
			},

			onCollectionAdd: function (model, collection, options) {
				this.listenTo(model, 'select', this.onSubjectSelected);
			},

			onSubjectSelected: function (model) {
				this.model.set('subject', model);
			},

			onStartButtonClick: function (e) {
				e.preventDefault();
				this.model.setSelectState();
			}

		});

	});