define([ 'backbone' ], function (Backbone) {
	var Afisha = {};

	//define templates
	Afisha.Templates = {
		CitySelectViewTemplate: _.template(
			'<div class="form-group">' +
				'<label>Выберите город:</label>' + 
				'<select class="form-control"></select>' +
			'</div>')
	};

	//define models
	Afisha.Models = {};
	Afisha.Models.BaseAfishaModel = Backbone.Model.extend({
		parse: function (data) {
			if (_.isObject(data.data)) {
				return data.data;
			} else {
				return data;
			}
		}
	});
	Afisha.Models.AppState = Afisha.Models.BaseAfishaModel.extend({ });
	Afisha.Models.Film = Afisha.Models.BaseAfishaModel.extend({ });
	Afisha.Models.City = Afisha.Models.BaseAfishaModel.extend({ });

	//define collections
	Afisha.Collections = {};
	Afisha.Collections.FilmCollection = Backbone.Collection.extend({ model: Afisha.Models.Film });
	Afisha.Collections.CitiesCollection = Backbone.Collection.extend({ model: Afisha.Models.City, url: '/afisha/cities' });

	//define views
	Afisha.Views = {};
	Afisha.Views.FilmsView = Backbone.View.extend({

		el: '<div class="afisha-films" />',

		initialize: function () {
			this.collection.on('add', this.add, this);
		},

		render: function () {
			var me = this;
			this.collection.forEach(function (item) { me.add(item); });
			return this.$el;
		},

		add: function (item) {
			var itemView = new Afisha.Views.FilmView({ model: item });
			itemView.render();
			this.$el.append(itemView.$el);
		}
	});
	Afisha.Views.CitySelectView = Backbone.View.extend({

		el: '<div class="afisha-city-select" />',

		events: {
			'change select': 'select'
		},

		initialize: function () {
			this.listenTo(this.collection, 'add', this.add);
		},

		render: function () {
			this.undelegateEvents();
			this.$el.append(Afisha.Templates.CitySelectViewTemplate());
			this.delegateEvents();

			this.$comboBox = this.$el.find('select');

			return this.$el;
		},

		add: function (item) {
			console.log(arguments);
			this.$comboBox.append('<option value="' + item.get('CityID')  + '">' + item.get('Name') + '</option>');
			this.select(this.model);
		},

		select: function () {
			var selected = this.$comboBox.find('option:selected').val();
			if (selected != this.model.get('cityId')) {
				this.model.set('cityId', selected);
			}
		}
	});

	//define router
	Afisha.Router = Backbone.Router.extend({
		routes: {
			'films': 'films',
		}
	});

	//define controller
	Afisha.Controller = function () {

		this.views = {};
		this.collections = {};
		this.appState = new Afisha.Models.AppState({});
		this.$container = $('.fn-afisha-container');

		this.initRouter();
		this.initCitySelect();
	};

	Afisha.Controller.prototype.initCitySelect = function () {
		this.collections.citiesCollection = new Afisha.Collections.CitiesCollection();
		this.views.citySelectView = new Afisha.Views.CitySelectView({
			model: this.appState,
			collection: this.collections.citiesCollection
		});
		this.collections.citiesCollection.fetch();
		this.$container.append(this.views.citySelectView.render());
	};

	Afisha.Controller.prototype.initRouter = function () {
		this.router = new Afisha.Router();

		this.router.navigate('afishaReset', { trigger: true });
		this.router.navigate('films', { trigger: true });
	};

	//define factory method
	Afisha.factory = function () {
		//console.log('Afisha.factory() call');
		return new Afisha.Controller();
	};

	return Afisha;
});