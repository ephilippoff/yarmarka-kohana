define([ 'backbone' ], function (backbone) {

	var Root = {};

	//some static data
	Root.Data = {};
	//contact types
	Root.Data.Types = [ 
		{ 'code': 'other', 'label': 'Другой' },
		{ 'code': 'mobile', 'label': 'Моб. телефон' }, 
		{ 'code': 'phone', 'label': 'Гор. телефон' }, 
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
			state: 'initial',
			checkCode: ''
		},

		saveTimer: null,

		saveTimerDuration: 500,

		initialize: function () {
			this.listenTo(this, 'change:value', this.onValueChanged);
			this.listenTo(this, 'change:checkCode', this.onCheckCodeChanged);
		},

		onValueChanged: function (model, newValue) {
			this.resetStep();
			this.save();
		},

		onCheckCodeChanged: function (model, newCheckCode) {
			if (this.get('step') == 3) {
				this.saveNow();
			}
		},

		saveNow: function () {
			this.set('state', 'loading');
			Backbone.Model.prototype.save.apply(this, arguments);
		},

		save: function (attributes, options) {
			var me = this;
			var myArguments = arguments;
			if (this.saveTimer) {
				clearTimeout(this.saveTimer);
			}
			this.set('state', 'loading');
			this.saveTimer = setTimeout(function () {
				Backbone.Model.prototype.save.apply(me, myArguments);
			}, this.saveTimerDuration);
		},

		parse: function (response, options) {
			var currentStep = this.get('step');
			var currentType = this.get('type');
			switch(response.code) {
				case 400:
					this.set('state', 'error');
					break;
				case 300:
					if (currentStep == 1) {
						this.set('state', 'next');
						//if (currentType == 'mobile' || currentType == 'email' || currentType == 'phone') {
						//	this.nextStep();	
						//	this.set('text', '');	
						//} else {
							this.set('step', 4);
							this.set('state', 'success');
						//}
					} else if (currentStep == 2) {
						this.set('step', 4);
						this.set('state', 'success');
					}
					this.set('text', '');
					break;
				case 200:
					if (currentStep == 1 || currentStep == 3) {
						this.set('state', 'success');
						this.set('step', 4);
					} else if (currentStep == 2) {
						this.set('state', 'wait');
						this.nextStep();
					}
					this.set('text', '');
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
				case 2:
					return '/rest_user/sent_code';
				default:
					return '/rest_user/check_code';
			}
		},

		nextStep: function () {
			var currentStep = this.get('step');
			if (currentStep > 2) {
				throw new Error('No next step');
			}
			this.set('step', currentStep + 1);
		},

		resetStep: function () {
			this.set('step', 1);
		}
	});

	//declare collections
	Root.Collections = {};
	//contacts collection
	Root.Collections.Contacts = Backbone.Collection.extend({
		model: Root.Models.Contact
	});

	//declare views
	Root.Views = {};
	//contacts full view (layout)
	Root.Views.Layout = Backbone.View.extend({

		events: {
			'click [data-role=create-contact]': 'create'
		},

		template: _.template(
				'<div data-role="container">'
				+ '</div>'
				+ '<div data-role="buttons">'
					+ '<a href="#" data-role="create-contact">Добавить контакт</a>'
				+ '</div>'
			),

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
	//input icon view
	Root.Views.ValueIcon = Backbone.View.extend({

		template: _.template('<i class="fa"></i>'),

		tagName: 'span',

		className: 'input-group-addon bg-color-whitesmoke brl3',

		iconsMap: [
			null,
			'mobile-phone',
			'phone',
			'skype',
			null,
			'envelope'
		],

		initialize: function (options) {
			//bind events
			this.listenTo(this.model, 'change:type', this.onTypeChanged);
		},

		render: function () {
			//append to dom
			this.$el.html(this.template());

			//set references to ui elements
			this.$icon = this.$el.find('i');

			//initialy call onTypeChanged
			this.onTypeChanged(this.model, this.model.get('type'));

			return this.$el;
		},

		onTypeChanged: function (model, newType) {
			var newType = this.model.get('type');
			if (typeof(this.iconsMap[newType]) == 'undefined') {
				throw new Error('Cannot find icon for type == ' + newType);
			}

			this.$icon.removeClass();
			if (this.iconsMap[newType]) {
				this.$icon.addClass('fa fa-' + this.iconsMap[newType]);
			}
		}
	});
	//contact value view
	Root.Views.Value = Backbone.View.extend({

		tagName: 'input',

		className: 'form-control js-contact-value',

		events: {
			'keyup': 'onUserKeyUp'
		},

		initialize: function () {
			//bind events
			this.listenTo(this.model, 'change:type', this.onTypeChanged);
			this.listenTo(this.model, 'change:value', this.onValueChanged);
			this.listenTo(this.model, 'change:index', this.onIndexChanged);
		},

		render: function () {
			this.$el.attr('type', 'text');
			//set initial value
			this.$el.val(this.model.get('value'));
			//initially call on type changed event
			this.onTypeChanged(this.model, this.model.get('type'));
			//initialy call on index changed event handler
			this.onIndexChanged(this.model, this.model.get('index'));
			//return dom element
			return this.$el;
		},

		onIndexChanged: function (model, newIndex) {
			this.$el.attr('name', 'additional_contacts[' + newIndex + '][value]');
		},

		onTypeChanged: function (model, newType) {
			var currentMask = Root.Data.Masks[newType];

			if (currentMask === null) {
				//destroy mask widget
				this.$el.unmask();
			} else {
				//enable mask widget
				var maskOptions = {

				};
				this.$el.mask(currentMask, maskOptions);
			}
		},

		onValueChnaged: function (model, newValue) {
			this.undelegateEvents();
			this.$el.val(newValue);
			this.delegateEvents();
		},

		onUserKeyUp: function () {
			this.model.set('value', this.$el.val());
		}
	});
	//validation icon view
	Root.Views.ValidationIcon = Backbone.View.extend({

		tagName: 'span',

		className: 'input-group-addon button white pl5 pr5 brr3 js-contact-ok',

		elClasses: { 
			success: 'bg-color-lightgreen', 
			error: 'bg-color-crimson',
			next: 'bg-color-crimson',
			loading: 'bg-color-gray',
			initial: 'bg-color-gray',
			wait: 'bg-color-gray'
		},

		iconClasses: {
			success: 'fa-check',
			error: 'fa-remove',
			loading: 'fa-spinner fa-spin',
			initial: 'fa-question',
			next: 'fa-check',
			wait: 'fa-spinner fa-spin'
		},

		template: _.template('<i></i><span></span>'),

		events: {
			'click': 'onUserClick'
		},

		countDownSec: 10,

		countDownTimer: null,

		initialize: function () {
			//bind events
			this.listenTo(this.model, 'change:state', this.onStateChanged);
			this.listenTo(this.model, 'change:step', this.onStepChanged);
			this.listenTo(this, 'countdown:change', this.onCountDownChanged);
		},

		render: function () {
			this.$el.html(this.template());
			//set references to ui elements
			this.$icon = this.$el.find('i');
			this.$label = this.$el.find('span');
			//initially call on state changed event handler
			this.onStateChanged(this.model, this.model.get('state'));
			//intiialy run on step changed event
			this.onStepChanged(this.model, this.model.get('step'));
			//return dom element
			return this.$el;
		},

		onCountDownChanged: function (sec) {
			this.$label.html(' До повторной отправки осталось (' + sec + ')');
		},

		onStateChanged: function (model, newState) {
			//update el classes
			//remove all classes
			this.$el.removeClass(_.values(this.elClasses).join(' '));
			//get current class
			var currentClass = this.elClasses[newState];
			if (!currentClass) {
				throw new Error('No el class for state == ' + newState);
			}
			this.$el.addClass(currentClass);

			//update icon classes
			this.$icon.removeClass('fa');
			this.$icon.removeClass(_.values(this.iconClasses).join(' '));
			currentClass = this.iconClasses[newState];
			if (!currentClass) {
				throw new Error('No icon class for state == ' + newState);
			}
			this.$icon.addClass('fa ' + currentClass);
		},

		onStepChanged: function (model, newStep) {
			this.stopCountDown();
			switch(newStep) {
				case 1:
					this.$icon.show();
					this.$label.hide();
					break;
				case 2:
					this.$label.show();
					this.$icon.hide();
					this.$label.html('Нажмите чтобы подтвердить');
					break;
				case 3:
					this.$icon.show();
					this.$label.show();
					this.startCountDown();
				case 4:
					this.$icon.show();
					this.$label.hide();
				default:
					break;
			}
		},

		stopCountDown: function () {
			if (this.countDownTimer) {
				clearTimeout(this.countDownTimer);
			}
		},

		startCountDown: function (sec) {
			if (arguments.length == 0) {
				sec = this.countDownSec;
			}
			if (sec == 0) {
				this.trigger('countdown:done');
				return;
			}
			this.trigger('countdown:change', sec);
			me = this;
			this.countDownTimer = setTimeout(function () {
				me.startCountDown(sec - 1);
			}, 1000);
		},

		onUserClick: function () {
			var currentStep = this.model.get('step');

			if (currentStep == 2) {
				this.model.saveNow();
			}
		}
	});
	//type selector view
	Root.Views.TypeSelector = Backbone.View.extend({

		tagName: 'select',

		events: {
			'change': 'onUserSelect'
		},

		initialize: function () {
			//bind events
			this.listenTo(this.model, 'change:index', this.onIndexChanged);
		},

		render: function () {
			var me = this;
			this.$el.empty();
			_.each(Root.Data.Types, function (item, index) {
				var attrs = {
					value: index
				};
				if (index == me.model.get('type')) {
					attrs.selected = 'selected';
				}
				me.$el.append($('<option />').attr(attrs).html(item.label));
			});

			//initialy run on index changed event handler
			this.onIndexChanged(this.model, this.model.get('index'));

			return this.$el;
		},

		onUserSelect: function () {
			var currentValue = this.$el.val();
			this.model.set('type', currentValue);
		},

		onIndexChanged: function (model, newIndex) {
			this.$el.attr('name', 'additional_contacts[' + newIndex + '][type]');
		}
	});
	//validation message view
	Root.Views.ValidationMessage = Backbone.View.extend({

		tagName: 'div',

		className: 'input-group w100p js-contact-description',

		initialize: function () {
			this.listenTo(this.model, 'change:text', this.onTextChanged);
		},

		render: function () {
			//initialy run on text changed event handler
			this.onTextChanged(this.model, this.model.get('text'));
			return this.$el;
		},

		onTextChanged: function (mode, newText) {
			this.$el.html(newText);
		}
	});
	//validation code view
	Root.Views.Code = Backbone.View.extend({

		tagName: 'div',

		className: 'input-group w100p js-contact-code',

		template: _.template(
			'<input class="form-control w100 js-contact-code-value" type="text" placeholder="Введите код">'
			+ '<span class="input-group-addon button bg-color-crimson white pl5 pr5 brr3 js-contact-code-ok">ок</span>'),

		initialize: function () {
			this.listenTo(this.model, 'change:step', this.onStepChanged);
		},

		render: function () {
			this.$el.html(this.template());

			this.$input = this.$el.find('input');

			//initialy call on step changed event handler
			this.onStepChanged(this.model, this.model.get('step'));

			return this.$el;
		},

		onUserClick: function () {
			this.model.set('checkCode', this.$input.val());
		},

		onStepChanged: function (model, newStep) {
			if (newStep == 3) {
				this.$el.show();
			}
			if (newStep == 1 || newStep == 4) {
				this.$el.hide();
			}
		}
	});
	//delete button 
	Root.Views.Remove = Backbone.View.extend({

		tagName: 'span',

		className: 'input-group-addon button bg-color-crimson white pl5 pr5 brr3 js-contact-ok',

		template: _.template('<i class="fa fa-remove"></i>'),

		events: {
			'click': 'onUserClick'
		},

		onUserClick: function () {
			this.trigger('remove');
		},

		initialize: function () {

		},

		render: function () {
			this.$el.html(this.template());
			return this.$el;
		}
	});
	//contact layout view
	Root.Views.Contact = Backbone.View.extend({

		template: _.template(
				'<div class="col-md-3 labelcont" data-role="type-container">'
				+ '</div>'
				+ '<div class="col-md-9">'
					+ '<div class="row js-contact">'
						+ '<div class="col-md-8 inp-cont">'
							+ '<div class="input-group w100p" data-role="value-container">'
							+ '</div>'
						+ '</div>'
						+ '<div class="col-md-4 inp-cont error" data-role="error-container">'
						+ '</div>'
					+ '</div>'
				+ '</div>'
			),

		tagName: 'div',

		className: 'row mb20',

		remove: function () {
			_.each(this.childViews, function (item) {
				if (typeof(item.remove) == 'function') {
					item.remove();
				}
			});
			Backbone.View.prototype.remove.apply(this, arguments);
		},

		initialize: function () {
			this.childViews = {};
			//initialize models
			this.validationModel = new Root.Models.Validation({
				type: this.model.get('type'),
				value: this.model.get('value')
			});

			//bind events
			this.listenTo(this.model, 'change:type', this.onTypeChanged);
			this.listenTo(this.model, 'change:value', this.onValueChanged);

			//initialize child views

			//contact icon view
			this.childViews.icon = new Root.Views.ValueIcon({
				model: this.model
			});

			//contact value view
			this.childViews.value = new Root.Views.Value({
				model: this.model
			});

			//validation icon view
			this.childViews.validationIcon = new Root.Views.ValidationIcon({
				model: this.validationModel
			});

			//type selector
			this.childViews.typeSelect = new Root.Views.TypeSelector({
				model: this.model
			});

			//validation message view
			this.childViews.validationMessage = new Root.Views.ValidationMessage({
				model: this.validationModel
			});

			//code form
			this.childViews.code = new Root.Views.Code({
				model: this.validationModel
			});

			//remove button
			this.childViews.remove = new Root.Views.Remove();

			//bind child view events
			this.listenTo(this.childViews.validationIcon, 'countdown:done', this.onCountDownDone);
			this.listenTo(this.childViews.remove, 'remove', this.remove);
		},

		render: function () {
			this.$el.append(this.template(this.model.toJSON()));

			//set references to ui elements
			this.$typeContainer = this.$el.find('[data-role=type-container]');
			this.$valueContainer = this.$el.find('[data-role=value-container]');
			this.$errorContainer = this.$el.find('[data-role=error-container]');

			//render child views
			this.$valueContainer.append(this.childViews.icon.render());
			this.$valueContainer.append(this.childViews.value.render());
			//this.$valueContainer.append(this.childViews.validationIcon.render());
			this.$valueContainer.append(this.childViews.remove.render());

			this.$typeContainer.append(this.childViews.typeSelect.render());

			this.$errorContainer.append(this.childViews.validationMessage.render());
			this.$errorContainer.append(this.childViews.code.render());

			//initialy run on type changed event
			this.onTypeChanged(this.model, this.model.get('type'));

			return this.$el;
		},

		// event handlers
		onTypeChanged: function (model, newType) {
			//from contact model to validation model
			this.validationModel.set('type', model.get('typeCode'));
		},

		onValueChanged: function (model, newValue) {
			//from contact model to validation model
			this.validationModel.set('value', newValue);
		},

		onCountDownDone: function () {
			this.validationModel.set('state', 'next');
			this.validationModel.set('step', 2);
		}
		// event handlers done
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