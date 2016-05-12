define([ 'underscore', 'backbone', 'templates' ], function (_, Backbone, templates) {

	return Backbone.Model.extend({

		defaults: {
			state: 'premium',
			active: 'premium',
			template: templates.addService.premium
		},

		url: '/rest_complain/send',

		setState: function (state) {
			this.set('state', state);
			this.set('active', state)
			this.setTemplate(state);
		},

		setTemplate: function (state) {
			switch(state) {
			  	case 'premium':
			  	  	this.set('template', templates.addService.premium)
			  	  	break;
	
			  	case 'lider':
			  	  	this.set('template', templates.addService.lider)
			  	  	break;

			  	case 'up':
			  	  	this.set('template', templates.addService.up)
			  	  	break;
	
			 	default:
			    	this.set('template', templates.addService.free)
			    	break;
			}
		},

		getState: function () {
			return this.get('state');
		},

		getTemplate: function () {
			return this.get('template');
		}

	});

});