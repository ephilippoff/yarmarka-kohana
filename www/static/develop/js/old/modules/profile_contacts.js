(function( $ ){
	var defaults = {
		'verifyButtonSelector'	: '.fn-contact-verify-button',
		'verifiedContactsSelector' : '#verified_user_contacts',
		'contactsSelector'		: '#user_contacts',
	};

	var self = this;

	var methods = {
		init : function (settings) {
			self.options = $.extend({}, defaults, settings);

			$(document.body).on('click.profileContacts', 
				self.options.contactsSelector+' '+self.options.verifyButtonSelector, 
				methods.clickVerify
			);

			return this;
		},
		bindElement : function(element) {
			return element.on('click.profileContacts', $.proxy(methods.clickVerify, this));
		},
		detectContactType : function(contact) {
			var contact_clear = contact.toString().replace(/\D/g,'');

			if (contact_clear == '') {
				return 5; // email
			} else if (contact_clear.indexOf('79') == 0) {
				return 1;// mobile
			} else {
				return 2; // home phone
			}
		},
		clickVerify : function() {
			this.contactValue 	= $(this).data('contact');
			this.contactId 		= $(this).data('id');
			this.contactType 	= methods.detectContactType(this.contactValue);
			var verifyWindow = new verifyContactWindow({
				contact_type : this.contactType,
				contact_value : this.contactValue,
				linkToUser : true
			});
			verifyWindow.win.bind('verifyEnd', methods.reloadContacts);
		},
		reloadContacts : function(e, state) {
			if (state == 'verified') {
				$(self.options.verifiedContactsSelector).load('/ajax/verified_profile_contacts?'+Math.floor(Math.random() * 100));
				$(self.options.contactsSelector).load('/ajax/user_profile_contacts?'+Math.floor(Math.random() * 100));
			}
		}
	};

	$.fn.profileContacts = function(method) {
		if ( methods[method] ) {
			return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error('Метод "' +  method + '" не найден');
		}
	};
})( jQuery );