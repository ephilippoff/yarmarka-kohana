define([ 'backbone' ], function (backbone) {

	var Root = {};

	//some static data
	Root.Data = {};
	//contact types
	Root.Data.Types = [ 
		{ 'code': 'other', 'label': 'Другой' },
		{ 'code': 'mobile', 'label': 'Мобильный телефон' }, 
		{ 'code': 'phone', 'label': 'Городской телефон' }, 
		{ 'code': 'skype', 'label': 'Skype' },
		{ 'code': 'icq', 'label': 'ICQ' },
		{ 'code': 'email', 'label': 'Email' }
	];
	//type input mask
	Root.Data.Masks = [
		null,
		'+7(999)999-99-99',
		'+7(9999)99-99-99',
		null,
		null,
		null
	];

	//validators
	Root.Validators = [
		null,
		{
			message: 'Не верный формат номера, введите номер мобильного телефона +7(9xx)xxx-xx-xx',
			regex: /^\+7\(9\d{2}\)\d{3}-\d{2}-\d{2}$/
		},
		{
			message: 'Не верный формат городского номера',
			regex: /^\+7\(\d{4}\)\d{2}-\d{2}-\d{2}$/
		},
		null,
		null,
		{
			message: 'Не верный формат Email адреса',
			regex: /^\w+@\w+\.\w{2,}$/
		}
	];

	//declare models
	Root.Models = {};
	//contact model
	Root.Models.Contact = Backbone.Model.extend({
		defaults: {
			type: '1',
			value: '',
			index: 0,
			typeCode: 'mobile',
			typeLabel: 'Мобильный телефон'
		},

		initialize: function () {
			this.listenTo(this, 'change:type', this.onTypeChanged);
		},

		onTypeChanged: function () {
			var typeObject = Root.Data.Types[this.get('type')];
			if (!typeObject) {
				throw new Error('undefined type id');
			}
			this.set('typeCode', typeObject.code);
			this.set('typeLabel', typeObject.label);
		}
	});
	//validation model
	Root.Models.Validation = Backbone.Model.extend({
		defaults: {
			type: '1',
			value: '',
			code: 300,
			text: '',
			step: 1,
			state: 'initial'
		},

		save: function (attributes, options) {
			var me = this;
			this.set('state', 'loading');
			Backbone.Model.prototype.save.apply(this, arguments);
		},

		parse: function (response, options) {
			switch(response.code) {
				case 400:
					this.set('state', 'error');
					break;
				case 300:
					this.set('state', 'success');
					break;
				default:
					this.set('state', 'initial');
					break;
			}

			return Backbone.Model.prototype.parse.apply(this, arguments);
		},

		urlRoot: function () {
			switch (this.get('step')) {
				case 1:
					return '/rest_user/check_contact';
				default:
					throw new Error('No any url for step == ' + step);
			}
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
			+ '<input type="text" value="<%= value %>" name="additional_contacts[<%= index %>][value]" data-role="value" />'
			+ '<a href="#" data-role="delete-contact">Удалить</a>'
			+ '<span data-role="validation-icon"></span>'
			+ '<span data-role="validation-message"></span>'
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

		validationIconClasses: { 
			success: 'contact-validation-ok', 
			error: 'contact-validation-error',
			loading: 'contact-validation-loading',
			initial: 'contact-validation-initial'
		},

		initialize: function (options) {
			this.rendered = false;

			//define other models
			this.validationModel = new Root.Models.Validation();

			//bind model events
			this.listenTo(this.model, 'change:type', this.initInputMask);
			this.listenTo(this.validationModel, 'change:state change:text', this.onValidationChanged);
		},

		onValidationAfterSave: function () {
			
		},

		onValidationChanged: function () {
			this.updateValidationIconState();
			this.updateValidationMessage();
		},

		updateValidationIconState: function () {
			var code = this.validationModel.get('state');
			if (!this.validationIconClasses[code]) {
				throw new Error('No ui class for code == ' + code);
			}
			var currentClassCode = this.validationIconClasses[code];

			//remove all classes
			var allClasses = _.values(this.validationIconClasses).join(' ');
			this.$validationIcon.removeClass(allClasses);

			//append current class
			this.$validationIcon.addClass(currentClassCode);
		},

		updateValidationMessage: function () {
			if (this.validationModel.get('code') == 300) {
				this.$validationMessage.empty();
			} else {
				this.$validationMessage.html(this.validationModel.get('text'));
			}
		},

		syncValidationModel: function () {
			this.validationModel.set({
				value: this.model.get('value'),
				type: this.model.get('typeCode')
			});
		},

		render: function () {
			this.$el.html(this.template(this.model.toJSON()));

			//update references to ui elements
			this.$valueTextBox = this.$el.find('[data-role=value]');
			this.$typesComboBox = this.$el.find('[data-role=types-combobox]');
			this.$validationMessage = this.$el.find('[data-role=validation-message]');
			this.$validationIcon = this.$el.find('[data-role=validation-icon]');

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

			//set rendered flag
			this.rendered = true;

			//init widgets
			this.initInputMask();

			return this.$el;
		},

		initInputMask: function () {
			//only if we render view at least once
			if (!this.rendered) {
				return;
			}

			var currentType = this.$typesComboBox.val();
			var currentMask = Root.Data.Masks[currentType];

			if (currentMask === null) {
				//destroy mask widget
				console.log('unmask');
				this.$valueTextBox.unmask();
			} else {
				//enable mask widget
				console.log('mask');
				var maskOptions = {

				};
				this.$valueTextBox.mask(currentMask, maskOptions);
			}
		},

		submitAfterWait:function () {
			if (this.waitSubmitTimer) {
				clearTimeout(this.waitSubmitTimer);
			}
			this.waitSubmitTimer = setTimeout(this.submit.bind(this), 500);
		},

		submit: function () {
			this.syncValidationModel();
			this.validationModel.save();
		},

		userToModel: function () {
			this.model.set('value', this.$valueTextBox.val());
			this.model.set('type', this.$typesComboBox.val());

			//run submit timer
			this.submitAfterWait();
		},

		destroy: function (e) {
			e.preventDefault();
			this.undelegateEvents();
			this.stopListening();
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