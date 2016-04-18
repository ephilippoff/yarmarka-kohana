define([ 'backbone' ], function (Backbone) {
	var Afisha = { };

	//define helpers
	Afisha.Helpers = {
		Parser: function (data) {
			if (_.isObject(data.data)) {
				return data.data;
			} else {
				return data;
			}
		},
		Endpoint: function (x, params) {
			var res = '/afisha/' + x;
			if (arguments.length > 1) {
				res += '?' + $.param(params);
			}
			return res;
		},
		RebootChosen: function ($el, opts) {
			if ($el.data().chosen) {
				$el.chosen('destroy');
			}
			$el.chosen(opts || {});
		},
	};

	//define templates
	Afisha.Templates = {
		AppStateEditorListTemplate: _.template(
			'<div class="afisha-select container">' +
				'<div class="row">' +
					'<div class="col-md-2 text-right">' +
						'<label><%= label %></label>' + 
					'</div>' +
					'<div class="col-md-10">' +
						'<select class="select" style="width: 350px;"></select>' +
					'</div>' +
				'</div>' +
			'</div>'),
		ObjectsContainerTemplate: _.template(
			'<div class="afisha-objects-container container">' +
				'<div class="row afisha-objects-list">' +
				'</div>' +
			'</div>'),
		_objectTemplateWrap: function (x) {
			return '<div class="col-md-2 afisha-object">' + x + '</div>';
		},
		_objectTemplates: function (x) {
			if (!this._objectTemplatesCache) {
				this._objectTemplatesCache = {
					Movie: _.template(this._objectTemplateWrap(
						'<div class="afisha-object-image">' + 
							'<img src="<%= Thumbnail %>" class="img-thumbnail" />' +
						'</div>' +
						'<div class="afisha-object-name">' + 
							'<p class="text-center lead"><%= Name %> (<%= Rating %>)</p>' + 
						'</div>')),
					NotImplemented: _.template(this._objectTemplateWrap('<p>Not implemented</p>'))
				};
			}

			if (!this._objectTemplatesCache[x]) {
				return this._objectTemplatesCache.NotImplemented;
			}
			return this._objectTemplatesCache[x];
		},
		ObjectTemplate: function (model) {
			return this._objectTemplates(model.ClassType)(model);
		},
		PlaceSelectTemplate: _.template(
			'<div class="afisha-place-select container">' +
				'<div class="row">' + 
					'<div class="col-md-2 text-right">' + 
						'<label>Выберите кинотеатр</label>' +
					'</div>' +
					'<div class="col-md-8">' +
						'<select class="form-control"></select>' +
					'</div>' +
					'<div class="col-md-2 text-center">' + 
						'<button class="btn btn-primary">' +
							'<span class="glyphicon glyphicon-ok"></span>' +
							' Выбрать' +
						'</button>' +
					'</div>' +
				'</div>' +
			'</div>')
	};

	//define models
	Afisha.Models = {};
	Afisha.Models.Base = Backbone.Model.extend({ parse: Afisha.Helpers.Parser });
	Afisha.Models.AppState = Afisha.Models.Base.extend({ });
	Afisha.Models.Object = Afisha.Models.Base.extend({
		getId: function () { return this.get('ObjectID'); },
		getName: function () { return this.get('Name'); }
	});
	Afisha.Models.City = Afisha.Models.Base.extend({
		getId: function () { return this.get('CityID'); },
		getTitle: function () { return this.get('Name'); }
	});
	Afisha.Models.ObjectType = Afisha.Models.Base.extend({
		getId: function () { return this.get('Name'); },
		getTitle: function () { return this.get('Name'); }
	});

	//define collections
	Afisha.Collections = {};
	Afisha.Collections.Base = Backbone.Collection.extend({ parse: Afisha.Helpers.Parser });
	Afisha.Collections.Object = Afisha.Collections.Base.extend({ 
		model: Afisha.Models.Object,

		initialize: function (options) {
			this.options = options;
		},

		url: function () {
			return Afisha.Helpers.Endpoint('object', this.options);
		},

		comparator: function (item1, item2) {
			var byField = 'ViewCountDaily';
			var asc = false;

			var rating1 = +item1.get(byField);
			var rating2 = +item2.get(byField);
			if ((asc && rating1 < rating2) || (!asc && rating1 > rating2)) {
				return -1;
			} else if ((asc && rating1 > rating2) || (!asc && rating1 < rating2)) {
				return 1;
			}
			return 0;
		}
	});
	Afisha.Collections.City = Afisha.Collections.Base.extend({ 
		model: Afisha.Models.City, 
		url: Afisha.Helpers.Endpoint('city')
	});
	Afisha.Collections.ObjectType = Afisha.Collections.Base.extend({
		model: Afisha.Models.ObjectType,
		url: Afisha.Helpers.Endpoint('object_type')
	});

	//define views
	Afisha.Views = {};
	Afisha.Views.Base = Backbone.View.extend({
		views: [],

		addChildView: function (view) {
			this.views.push(view);
		},

		clearChildViews: function () {
			_.each(this.views, function (item) { item.destroy(); });
			this.views = [];
		},

		destroy: function () {
			this.clearChildViews();
			this.undelegateEvents();
			this.$el.remove();
		}
	});
	Afisha.Views.AppStateEditorList = Afisha.Views.Base.extend({

		events: {
			'change select': 'updateModel'
		},

		initialize: function (options) {
			this.label = options.label;
			this.field = options.field;
			this.initialValue = this.model.get(this.field);
			this.setElement(Afisha.Templates.AppStateEditorListTemplate({ label: this.label }));
			this.$comboBox = this.$el.find('select');
			this.collection.on('sync', this.render, this);
		},

		render: function () {
			var me = this;
			this.$comboBox.empty();
			this.collection.forEach(this.add, this);
			Afisha.Helpers.RebootChosen(this.$comboBox);

			if (this.initialValue) {
				this.set(this.initialValue);
			} else {
				this.updateModel();
			}

			this.$el.addClass('afisha-select-' + this.field);

			return this.$el;
		},

		add: function (item) {
			this.$comboBox.append('<option value="' + item.getId()  + '">' + item.getTitle() + '</option>');
		},

		updateModel: function () {
			var selected = this.$comboBox.find('option:selected').val();
			if (selected != this.model.get(this.field)) {
				this.model.set(this.field, selected);
			}
		},

		set: function (value) {
			this.$comboBox.val(value);
			this.updateModel();
		}
	});
	Afisha.Views.ObjectsContainer = Afisha.Views.Base.extend({

		initialize: function () {
			this.setElement(Afisha.Templates.ObjectsContainerTemplate());
			this.$container = this.$el.find('.afisha-objects-list');
			this.collection.on('sync', this.render, this);
		},

		render: function () {
			this.clearChildViews();
			this.collection.forEach(this.add, this);
		},

		add: function (item) {
			var view = new Afisha.Views.Object({
				model: item
			});
			this.$container.append(view.render());
			this.addChildView(view);
		}
	});
	Afisha.Views.Object = Afisha.Views.Base.extend({

		initialize: function () {

		},

		render: function () {
			this.setElement(Afisha.Templates.ObjectTemplate(this.model.toJSON()));
			return this.$el;
		}
	});
	Afisha.Views.PlaceSelect = Afisha.Views.Base.extend({

		events: {
			'click button': 'select'
		},

		initialize: function () {
			this.collection.on('sync', this.render, this);
			this.setElement(Afisha.Templates.PlaceSelectTemplate());
			this.$comboBox = this.$el.find('select');
			this.value = null;
		},

		render: function () {
			this.$comboBox.empty();
			this.collection.forEach(this.add, this);
			Afisha.Helpers.RebootChosen(this.$comboBox);
		},

		add: function (item) {
			this.$comboBox.append(
				$('<option />')
					.attr('value', item.getId())
					.append(item.getName()));
		},

		select: function (event) {
			event.preventDefault();
			this.model.set('placeId', this.$comboBox.val());
		}
	});

	//define router
	Afisha.Router = Backbone.Router.extend({
		routes: {
			'objects/:objectTypeId/:cityId': 'objects',
			'place/:objectId': 'place'
		},

		objects: function (objectTypeId, cityId) {
			console.log('Show objects for ', arguments);
		},

		place: function (placeId) {
			console.log('Show place ', arguments);
		}
	});

	//define controllers
	Afisha.Controllers = {};
	//cinema select controller
	Afisha.Controllers.PlaceSelect = function (options) {
		this.appState = options.appState;
		this.router = options.router;

		this.bindEvents();
		this.initialize();
	};

	Afisha.Controllers.PlaceSelect.prototype = _.extend(Afisha.Controllers.PlaceSelect.prototype, {
		//events
		cityChanged: function () {
			this.initialize();
			if (this.appState.get('cityId')) {
				this.collection.fetch();
			}
		},
		//events

		bindEvents: function () {
			this.appState.on('change:cityId', this.cityChanged, this);
		},

		initialize: function () {
			this.initializeCollection();
			this.initializeView();
		},

		initializeCollection: function () {
			if (this.collection) {
				this.collection.options.cityId = this.appState.get('cityId');
				return;
			}

			this.collection = new Afisha.Collections.Object({
				objectTypeId: 'Place',
				cityId: this.appState.get('cityId')
			});
		},

		initializeView: function () {
			if (this.view) {
				return;
			}

			this.view = new Afisha.Views.PlaceSelect({
				model: this.appState,
				collection: this.collection,
				el: this.$el
			});
		}
	});
	//main controller
	Afisha.Controllers.Main = function () {

		this.rootView = new Afisha.Views.Base();
		this.appState = new Afisha.Models.AppState({
			objectTypeId: 'Movie'
		});
		this.$container = $('.fn-afisha-container');

		this.initRouter();
		this.bindEvents();
		this.initAppStateEditor();
		this.initCinemaSelect();
	};

	Afisha.Controllers.Main.prototype = _.extend(Afisha.Controllers.Main.prototype, {
		//events 
		appStateChanged: function (model) {
			if (model.changed.placeId) {
				this.navigate([ 'place', model.get('placeId') ])
				return;
			}
			this.navigate([ 'objects', model.get('objectTypeId'), model.get('cityId') ]);
		},

		showObjects: function (objectTypeId, cityId) {
			var collection = new Afisha.Collections.Object({
				objectTypeId: objectTypeId,
				cityId: cityId
			});
			this.showView(new Afisha.Views.ObjectsContainer({
				collection: collection
			}));
			collection.fetch();
		},
		//events done

		bindEvents: function () {
			//app state
			this.appState.on('change', this.appStateChanged, this);
			//router
			this.router.on('route:objects', this.showObjects, this);
		},

		initAppStateEditor: function () {
			var schema = [
				{
					label: 'Выберите город',
					field: 'cityId',
					collection: new Afisha.Collections.City()
				},
				{
					label: 'Выберите тип объектов',
					field: 'objectTypeId',
					collection: new Afisha.Collections.ObjectType()
				}
			];

			_.each(schema, function (item) {
				var view = new Afisha.Views.AppStateEditorList(_.extend(item, {
					model: this.appState
				}));
				view.collection.fetch();
				this.$container.append(view.$el);
			}, this);
		},

		initCinemaSelect: function () {
			this.placeSelect = new Afisha.Controllers.PlaceSelect({ appState: this.appState });
			this.$container.append(this.placeSelect.view.$el);
		},

		initRouter: function () {
			this.router = new Afisha.Router();
			this.navigate([ 'afishaReset' ]);
		},

		showView: function (view) {
			this.rootView.clearChildViews();
			this.rootView.addChildView(view);
			this.$container.append(view.$el);
		},

		navigate: function (tokens) {
			this.router.navigate(tokens.join('/'), { trigger: true });
		}
	});

	//define factory method
	Afisha.factory = function () {
		//console.log('Afisha.factory() call');
		return new Afisha.Controllers.Main();
	};

	return Afisha;
});