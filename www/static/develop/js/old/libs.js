function AuthWindow(){
	var self = this;

	var authWindow = {};

	var states = {
		Unknown : function (){
			//элементы
			$(authWindow.okButton).removeClass('hidden');
			$(authWindow.loginOverlay).removeClass('error');

			//отписка
			unbindElements({
				okButton : authWindow.okButton,
			});

			//подписка
			bindElements({
				okButton :  onOkButtonClick,
			});

			alerts.clear();
			currentState = 'Unknown';
		},
		NotRegistered : function (){
			//элементы
			$(authWindow.okButton).addClass('hidden');
			$(authWindow.loginOverlay).addClass('error');
			
			//отписка
			unbindElements({
				okButton : authWindow.okButton,
			});

			//подписка
			bindElements({
				okButton :  onOkButtonClick,
			});

			alerts.nofounded();
			currentState = 'NotRegistered';
		},
		NotRegisteredPhone : function (){
			//элементы
			$(authWindow.okButton).addClass('hidden');
			$(authWindow.loginOverlay).addClass('error');
			
			//отписка
			unbindElements({
				okButton : authWindow.okButton,
			});

			//подписка
			bindElements({
				okButton :  onOkButtonClick,
			});

			alerts.nofoundedphone();
			currentState = 'NotRegisteredPhone';
		},		
		NotRegisteredEmail : function (){
			//элементы
			$(authWindow.okButton).addClass('hidden');
			$(authWindow.loginOverlay).addClass('error');
			
			//отписка
			unbindElements({
				okButton : authWindow.okButton,
			});

			//подписка
			bindElements({
				okButton :  onOkButtonClick,
			});

			alerts.nofoundedemail();
			currentState = 'NotRegisteredEmail';
		},				
		AlreadyRegistered : function (){
			//элементы
			$(authWindow.okButton).removeClass('hidden');
			$(authWindow.loginOverlay).removeClass('error');
			$(authWindow.okButton).find('span').text('Войти');
			
			
			
			//отписка
			unbindElements({
				okButton : authWindow.okButton,
			});

			//подписка
			bindElements({
				okButton :  onOkButtonClick,
			});

			alerts.clear();
			currentState = 'AlreadyRegistered';
		}
	}


	var alerts = {
		clear : function(){
			var text = '';
			$(authWindow.noticeDiv).addClass('hidden');
			$(authWindow.noticeText).html(text);
		},
		nofounded : function(){ 
			var text = 'Такой Email не зарегистрирован';
			$(authWindow.noticeDiv).removeClass('hidden');
			$(authWindow.noticeText).html(text);
			$(authWindow.stand_noticeDiv).removeClass('hidden');
			$(authWindow.stand_noticeText).html(text);	
		},
		nofoundedphone : function(){ 
			var text = 'Такой Email не зарегистрирован';
			$(authWindow.noticeDiv).removeClass('hidden');
			$(authWindow.noticeText).html(text);
			$(authWindow.stand_noticeDiv).removeClass('hidden');
			$(authWindow.stand_noticeText).html(text);	
		},		
		nofoundedemail : function(){ 
			var text = 'Такой Email не зарегистрирован';
			$(authWindow.noticeDiv).removeClass('hidden');
			$(authWindow.noticeText).html(text);
			$(authWindow.stand_noticeDiv).removeClass('hidden');
			$(authWindow.stand_noticeText).html(text);	
		},				
		errorinregister : function(){
			var text = 'Ошибка при регистрации. Попробуйте позже';
			$(authWindow.noticeDiv).removeClass('hidden');
			$(authWindow.noticeText).html(text);
			$(authWindow.stand_noticeDiv).removeClass('hidden');
			$(authWindow.stand_noticeText).html(text);	
		},
		userisblocked : function(){
			var text = 'Ваш контакт заблокирован за нарушение правил ресурса';
			$(authWindow.noticeDiv).removeClass('hidden');
			$(authWindow.noticeText).html(text);
			$(authWindow.stand_noticeDiv).removeClass('hidden');
			$(authWindow.stand_noticeText).html(text);	
		},
		passwordiswrong : function(){
			var text = 'Неправильный пароль';
			$(authWindow.noticeDiv).removeClass('hidden');
			$(authWindow.noticeText).html(text);
			$(authWindow.stand_noticeDiv).removeClass('hidden');
			$(authWindow.stand_noticeText).html(text);	
		},
		sendpasswordimpossible : function(){
			var text = 'Вы уже отправляли пароль';
			$(authWindow.noticeDiv).removeClass('hidden');
			$(authWindow.noticeText).html(text);
			$(authWindow.stand_noticeDiv).removeClass('hidden');
			$(authWindow.stand_noticeText).html(text);	
		},
		passwordsended : function(){
			var text = 'Сообщение с инструкцией по восстановлению пароля отправлено';
			$(authWindow.noticeDiv).removeClass('hidden');
			$(authWindow.noticeText).html(text);
			$(authWindow.stand_noticeDiv).removeClass('hidden');
			$(authWindow.stand_noticeText).html(text);	
		},
		rulesunchecked : function(){
			var text = 'Вы не подтвердили правила использования ресурса';
			$(authWindow.noticeDiv).removeClass('hidden');
			$(authWindow.noticeText).html(text);
			$(authWindow.stand_noticeDiv).removeClass('hidden');
			$(authWindow.stand_noticeText).html(text);	
		},
		smssended : function(){
			var text = 'Сообщение с кодом отправлено';
			$(authWindow.noticeDiv).removeClass('hidden');
			$(authWindow.noticeText).html(text);
			$(authWindow.stand_noticeDiv).removeClass('hidden');
			$(authWindow.stand_noticeText).html(text);	
		},
		print_text: function(text){
			$(authWindow.noticeDiv).removeClass('hidden');
			$(authWindow.noticeText).html(text);
			$(authWindow.stand_noticeDiv).removeClass('hidden');
			$(authWindow.stand_noticeText).html(text);	
		}
	}

	var currentState = 'Unknown';
	 
	init();

	function init(){
		_initElements();
		_subscribeEvents();
		state('Unknown');
		/*setTimeout(function(){
	     	checkIsAlreadyRegistered();
	    }, 500 );*/
	}

	function _initElements(){
		var id  = '#auth_window'; 
		var stand_id = '.fn-auth-standart';
		var win = $(id);
		authWindow = {
			id :  id,
			win :  win,
			auth_form : $('#auth_form'),
			login : $(win).find('.login'),
			loginOverlay : $(win).find('.input.fn-login'),
			password : $(win).find('.password'),
			sendPasswordButton : $(win).find('.send_password'),
			agreeRulesCheckboxOverlay : $(win).find('.fn-agree-rules-checkbox'),
			okButton : $(win).find('.enter-without-register'),
			noticeText : $(win).find('.send_password_msg'),
			noticeDiv : $(win).find('.fn-popup'),
			stand_form : $(stand_id),
			stand_login : $('.fn-auth-standart-login'),
			stand_sendPasswordButton : $('.fn-auth-standart-password-remember'),
			stand_noticeText : $('.fn-auth-standart-notice').find('span'),
			stand_noticeDiv : $('.fn-auth-standart-notice'),
		}
	}

	function state(state){
		if (state){
			states[state]();
		} else {
			return currentState;
		}
	}

	function _subscribeEvents(){
		$(authWindow.sendPasswordButton).bind('click', {isWindow: true}, onSendPasswordClick);
		$(authWindow.stand_sendPasswordButton).bind('click', {isWindow: false}, onSendPasswordClick);
		$(authWindow.login).bind('keyup', onLoginChange);
	}

	function checkIsAlreadyRegistered(){
		var alreadyRegistered = false;
		var inputValue = $(authWindow.login).val();
		var isInputValided = ((isValidEmail(inputValue)) || (isValidPhone(inputValue)));
		if (( inputValue != '' ) && (isInputValided)){
			
			$.post("/ajax/get_contact_by_value", {value: inputValue }, function(data){
				if (data.code == 200 && data.verified_state == true) 
					alreadyRegistered = true ; 
				else
					alreadyRegistered = false;						

				if (alreadyRegistered)
					state('AlreadyRegistered');
				else
				{
					if (isValidEmail(inputValue))
						state('NotRegisteredEmail');
					else
						state('NotRegisteredPhone');
				}

							
			}, 'json');
			

		} else {
			state('Unknown');
		}		
	}

	function isValidEmail(email){		
		var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return regex.test(email);
	}

	function isValidPhone(phone){		
		var regex = /^[78]9[\d]{9}$/;
		return regex.test(phone);
	}

	function unbindElements(object){
		for (k in object){
			$(object[k]).unbind();
		}
	}

	function bindElements(object){
		for (k in object){
			$(authWindow[k]).bind('click',object[k]);
		}
	}

	//events
	function onSendPasswordClick(e){
		var ErrorSendPassword = true;
		var contact = (e.data.isWindow) ? $(authWindow.login).val() :  $(authWindow.stand_login).val();
		
		$.getJSON('/ajax/send_password', {contact:contact}, function(json){
			if (json.code == 200) {
				if (json.contact_type_id == 1) {
					alerts.smssended();
				} else if (json.contact_type_id == 5) {
					alerts.passwordsended();
				} else {
					// неизвестный тип контакта
				}
			} else if (json.code == 404) {
				alerts.nofounded();
			} else if (json.code == 303 || json.code == 304) {
				alerts.print_text(json.msg);
			}
		});

		return false;
		//
//		(ErrorSendPassword) ? alerts.sendpasswordimpossible() : alerts.passwordsended();
	}

	function onOkButtonClick(e){
		$(authWindow.auth_form).submit();
	}

	function onLoginChange(e){
		delay(function(){
	     	checkIsAlreadyRegistered();
	    }, 500 );		
	}

	var delay = (function(){
	  var timer = 0;
	  return function(callback, ms){
	    clearTimeout (timer);
	    timer = setTimeout(callback, ms);
	  };
	})();
	//

}