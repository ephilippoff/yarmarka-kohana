define([ 'backbone' ], function (backbone) {

	var Root = {};

	//some static data
	Root.Data = {};
	//contact types
	Root.Data.Types =[ 
		{ 'code': 'other', 'label': 'Другой' },
		{ 'code': 'mobile', 'label': 'Мобильный телефон' }, 
		{ 'code': 'phone', 'label': 'Городской телефон' }, 
		{ 'code': 'skype', 'label': 'Skype' },
		{ 'code': 'icq', 'label': 'ICQ' },
		{ 'code': 'email', 'label': 'Email' }
	];

	//declare models
	Root.Models = {};
	//contact model
	Root.Models.Contact = Backbone.Model.extend({
		defaults: {
			type: 1,
			value: '',
			index: 0
		}
	});

	//declare collections
	Root.Collections = {};
	//contacts collection
	Root.Collections.Contacts = Backbone.Collection.extend({
		model: Root.Models.Contact
	});

	//declare templates
	Root.Templates = {};
	//layout template
	Root.Templates.Layout = 
		'<div data-role="container">'
			+ ''
		+ '</div>'
		+ '<div data-role="buttons">'
			+ '<a href="#" data-role="create-contact">Добавить контакт</a>'
		+ '</div>';
	//contact view template
	Root.Templates.Contact = 
		'<div data-role="contact">'
			+ '<select data-role="types-combobox" name="additional_contacts[<%= index %>][type]">'
			+ '</select>'
			+ '<input type="text" value="<%= value %>" name="additional_contacts[<%= index %>][value]" />'
			+ '<a href="#" data-role="delete-contact">Удалить</a>'
		+ '</div>';

	//declare views
	Root.Views = {};
	//contacts full view (layout)
	Root.Views.Layout = Backbone.View.extend({

		events: {
			'click [data-role=create-contact]': 'create'
		},

		template: _.template(Root.Templates.Layout),

		initialize: function (options) {
			this.collection.on('add', this.add, this);
		},

		render: function () {
			this.undelegateEvents();
			this.$el.html(this.template());
			this.delegateEvents();

			//update references to ui elements
			this.$container = this.$el.find('[data-role=container]');
		},

		create: function (e) {
			e.preventDefault();
			this.collection.add(new Root.Models.Contact({ index: this.collection.length }));
		},

		add: function (model) {
			var view = new Root.Views.Contact({
				model: model
			});
			view.on('destroy', function () { 
				this.collection.remove(model); 
			}, this);
			this.$container.append(view.render());
		}

	});
	//contact view
	Root.Views.Contact = Backbone.View.extend({
		events: {
			'keyup [data-role=value]': 'userToModel',
			'change [data-role=types-combobox]': 'userToModel',
			'click [data-role=delete-contact]': 'destroy'
		},

		tagName: 'div',

		template: _.template(Root.Templates.Contact),

		initialize: function (options) {

		},

		render: function () {
			this.$el.html(this.template(this.model.toJSON()));

			//update references to ui elements
			this.$valueTextBox = this.$el.find('[data-role=value]');
			this.$typesComboBox = this.$el.find('[data-role=types-combobox]');

			//update types combobox values
			var me = this;
			_.each(Root.Data.Types, function (item, index) {
				var attr = {
					'value': index
				};
				if (index == me.model.get('type')) {
					attr.selected = 'selected';
				}
				me.$typesComboBox.append($('<option />')
					.attr(attr)
					.html(item.label));
			});

			return this.$el;
		},

		userToModel: function () {
			this.model.set('value', this.$valueTextBox.val());
			this.model.set('type', this.$typesComboBox.val());
		},

		destroy: function (e) {
			e.preventDefault();
			this.undelegateEvents();
			this.$el.remove();
			this.trigger('destroy');
		}
	});

	//controller default options
	Root.ControllerDefaults = {
		el: '#additional_contacts'
	};
	//controller
	Root.Controller = function (options) {
		this.options = _.extend(Root.ControllerDefaults, options);
		this.initialize();
	};
	//controller prototype
	_.extend(Root.Controller.prototype, {

		initialize: function () {
			this.collection = new Root.Collections.Contacts();
			this.layout = new Root.Views.Layout({
				el: this.options.el,
				collection: this.collection
			});
			this.layout.render();

			//append initial contacts	
			var initialData = this.layout.$el.data('contacts');
			if (initialData) {
				//update type
				_.each(initialData, function (item, index) {
					initialData[index].type = _.findIndex(Root.Data.Types, function (x) { 
						return x.code == item.type 
					});
				});
				this.collection.add(initialData);
			}
		}

	});

	return Root;
});