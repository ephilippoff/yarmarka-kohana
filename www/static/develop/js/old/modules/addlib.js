var alreadyVerifiedContacts = [];

function verifyContactWindow(object){

    var self = this;
    var contactObject = object;
    var windowElem;
    var currentState = 'noverified';
    var contact_id = null;
    var resendTimeoutId = null;
    var verificationCode = null;

    var states = {
        noverified: function (){ currentState = 'noverified'; },
        verified: function (){ currentState = 'verified'; },
    }

    var alertMessage = {
        def : function () { 
            var alertText = "";
            $(windowElem.winErrorBlock).hide();
            resendButtonEnabling.disable();
            $(windowElem.winErrorBlockText).text(alertText);
            return alertText;
        },
        messageSent : function (force) { 
            var alertText;
            startCountdownForResend();
            if (force) {
                alertText = "Сообщение с кодом отправлено повторно";
            } else {
                alertText = "Сообщение с кодом отправлено";
            }
            $(windowElem.winErrorBlock).show();
            $(windowElem.winErrorBlockText).text(alertText);
            return alertText;
        },
        messageAlreadySent : function () { 
            var alertText = "Сообщение с кодом уже отправлено вам ранее";
            $(windowElem.winErrorBlock).show();
            resendButtonEnabling.disable();
            startCountdownForResend();
            $(windowElem.winErrorBlockText).text(alertText);
            return alertText;
        },
        messageWithVerificationCode : function() {
            alertMessage.customMessage('Этот номер телефона пройдет проверку. Подтвердите что вы согласны, введите код:'+verificationCode);
        },
        wrongCode : function () { 
            var alertText = "Неправильный код";
            $(windowElem.winErrorBlock).show();
            resendButtonEnabling.disable();
            $(windowElem.winErrorBlockText).text(alertText);
            return alertText;
        },
        blockedContact : function () { 
            var alertText = "Контакт в черном списке";
            $(windowElem.winErrorBlock).show();
            $(windowElem.winSubmitButton).hide();
            $(windowElem.winInputCode).attr('disabled', 'disabled');
            resendButtonEnabling.disable();
            $(windowElem.winErrorBlockText).text(alertText);
            return alertText;
        },
        alienContact : function () {
            //302
            stopCountdownForResend();
            var alertText = "Контакт принадлежит другому пользователю";
            $(windowElem.winErrorBlock).show();
            resendButtonEnabling.disable();
            $(windowElem.winErrorBlockText).text(alertText);
            return alertText;
        },
        exceedSmsLimit : function () {
            //304
            stopCountdownForResend();
            var alertText = "Вы исчерпали лимит SMS на сегодня";
            $(windowElem.winErrorBlock).show();
            resendButtonEnabling.disable();
            $(windowElem.winErrorBlockText).text(alertText);
            return alertText;
        },
        customMessage : function (msg) { 
            $(windowElem.winErrorBlock).show();
            resendButtonEnabling.disable();
            $(windowElem.winErrorBlockText).text(msg);
            return msg;
        },
    }

    var resendButtonEnabling = {
        enable : function(){
            $(windowElem.winResendButtonSpan).addClass('span-link').css('color','#2a76a1');
            bindElements({
                winResendButton : onResendButtonClick
            });
        },
        disable : function(){
             $(windowElem.winResendButtonSpan).removeClass('span-link').css('color','#ccc');
            unbindElements({winResendButton : true});
        }
    }

    var sendMessageWithCodeByType = {
        1 :  function(force){
            $.post('/ajax/sent_verification_code', 
                {contact_type_id : contactObject.contact_type, phone : contactObject.contact_value, force : force}, 
                _sendMessage, 'json');
        },
        2 : function() {
            $.post('/ajax/check_contact', 
                {contact_type_id : contactObject.contact_type, contact : contactObject.contact_value}, 
                function(json){
                    if (json.code == 201) {
                        state('verified');
                        closeWindow();
                    } else if (json.code == 400) {
                        alertMessage.customMessage('Этот контакт принадлежит другому пользователю');
                    }
                    else if (json.code == 401) {
                        alertMessage.customMessage('Это не городской телефон, выберите "мобильный"');
                    }
                    else if (json.code == 305) {
                        alertMessage.blockedContact();
                    }
                    else {
                        generateVerificationCode();
                        alertMessage.customMessage('Этот номер телефона пройдет проверку. Подтвердите что вы согласны, введите код:'+verificationCode);
                    }
                },
            'json');
        },
        5 : function(force){
            $.post('/ajax/sent_verification_code', 
                {contact_type_id : contactObject.contact_type, email : contactObject.contact_value, force : force}, 
                _sendMessage, 
            'json');
        },
    }

    initialize();

    function initialize(){
        _prepareWindow();
        _subscribeEvents();
        _showWindow();
        state('noverified');
        alertMessage.def();
        if (state() != 'verified'){
            sendMessageWithCodeByType[contactObject.contact_type]();
        }
    }

    function _initWindow(){
        var win_id = '.fn-verify-contact-win';
        var win = $(win_id);
        windowElem = {
            win : win,
            winOverlay: $('body').find('.popup-layer'),
            winClass :  $(win).find('.fn-verify-contact-win'), 
            winInputCode :    $(win).find('input.fn-input-code'),
            winSubmitButton : $(win).find('.fn-verify-contact-win-submit'), 
            winResendButton:  $(win).find('.fn-btn-re-send'), 
            winResendButtonSpan:  $(win).find('.fn-btn-re-send').find('span'), 
            winCloseButton :  $(win).find('.fn-verify-contact-win-close'),
            winErrorBlock :   $(win).find('.fn-error-block'),
            winErrorBlockText : $(win).find('.fn-error-block-text'),
            codeVal : function (){ return  $(win).find('input.fn-input-code').val();},
        };
    };

    function _prepareWindow(){
        var templateWindow = $('#verify-contact-window').html();
        var data = { contact_value : contactObject.contact_value };
        var template = _.template(templateWindow, data);
        $('body').append( template );

        _initWindow();      
    };

    function _subscribeEvents(){
        //подписываемся на события окна
        bindElements({
            winSubmitButton :  onSubmitButtonClick,
            winCloseButton : onCloseButtonClick
        });

    }
    
    function _showWindow(){
        $(windowElem.winOverlay).fadeIn();
        $(windowElem.win).fadeIn();
    };

    function closeWindow(){   
        $(windowElem.win).trigger('verifyEnd', [state()]);
        $(windowElem.win).remove();
        $(windowElem.winOverlay).fadeOut();
        stopCountdownForResend();
    };

    function unbindElements(object){
        for (k in object){
            $(object[k]).unbind();
        }
    };

    function bindElements(object){
        for (k in object){
            $(windowElem[k]).bind('click', object[k]);
        }
    };

    function state(state_val){
        if (state_val){
            states[state_val]();
        } else {
            return currentState;
        }
    };

    function startCountdownForResend() {
        resendButtonEnabling.disable();
        resendTimeoutId = setTimeout(function() {
            resendButtonEnabling.enable();
        }, 30000);
    };

    function stopCountdownForResend() {
        clearTimeout(resendTimeoutId);
    };

    function generateVerificationCode() {
        verificationCode = _.random(1000, 9999);
        return verificationCode;
    };

    function resendCode(){
        startCountdownForResend();
        sendMessageWithCodeByType[contactObject.contact_type](true);
    };

    function checkCode(){
        var code = windowElem.codeVal(); 

        if (contactObject.contact_type == 2) {
            if (code == verificationCode) {
                $.post('/ajax/verify_home_phone', {contact : contactObject.contact_value, link_to_user : contactObject.linkToUser}, 
                    function(json){
                        if (json.code == 200) {
                            state('verified');
                            closeWindow();
                        } else {
                            // это на случай если кто-то верифицировал контакт пока вводили код
                            alertMessage.customMessage('Не удалось верифицировать контакт, возможно он уже принадлежит другому пользователю');
                        }
                    }, 'json');
            } else {
                alertMessage.wrongCode();
                setTimeout(alertMessage.messageWithVerificationCode, 2000);
            }
        } else {
            $.post('/ajax/check_contact_code/'+contact_id, {code:code, link_to_user : contactObject.linkToUser}, function(json) {
                if (json.code == 200) {
                    state('verified');
                    closeWindow();
                } else {
                    alertMessage.wrongCode();
                }
            }, 'json');
        }
    };

    function _sendMessage(json){ 
        contact_id = json.contact_id;
        switch (+json.code){
            case 200: 
                alertMessage.messageSent(json.force); 
            break;
            case 303:
                alertMessage.messageAlreadySent();
            break;
            case 301:
                state('verified');
                closeWindow();
            break;
            case 302:
                alertMessage.alienContact();
            break;
            case 304:
                alertMessage.exceedSmsLimit();
            break;
            case 305:
                alertMessage.blockedContact();
            break;
            case 401 :
                alertMessage.customMessage('Это не мобильный телефон, выберите "городской"');
            break;
        }
    };

    //events
    function onSubmitButtonClick(e){
        checkCode();
    };

    function onCloseButtonClick(e){
        closeWindow();
    };

    function onResendButtonClick(e){    
        resendCode();
    };

    return windowElem;

}

function Contact(object){
    var self = this;
    
    var contactObject = object;
    var contactElem = {};
    var windowElem = {};

    var completeContact = false;

    var _errors = {
        wrongCode : 'Код не верный',
        exceedMaxMessagesCount : 'Вы отправили на этот номер макимальное количество сообщений.'
    }

    var alertMessage = {
        def : function () { return "";},
        noverified : function () { return "Не подтвержденные контакты не будут добавлены к объявлению";},
        verified : function () { return "Контакт подтвержден";},
    }

    var states = {
        clear : function (){
    
            setClassesElement('remove',contactElem.field, ['noverified','verified']);
            setClassesElement('remove',contactElem.contact_verify_button, ['hidden']); 
            setClassesElement('add', contactElem.contact_verify_button , ['link-noactive']);    
            
            $(contactElem.contact_verify_button).unbind();

            $(contactElem.contact_inform).text( alertMessage.def() );
            
            contactObject.state = 'clear';
        },
        novalided :  function (){ 

            contactObject.state = 'novalided'; 
        },
        valided :  function (){ 

            contactObject.state = 'valided';
        },
        noverified : function (){ 

            setClassesElement('remove', contactElem.field, ['verified']);
            setClassesElement('remove', contactElem.contact_verify_button, ['hidden','link-noactive']); 
            setClassesElement('add', contactElem.field , ['noverified']);

            $(contactElem.contact_verify_button).unbind();
            $(contactElem.contact_verify_button).bind('click', onContactVerifyButtonClick);

            $(contactElem.contact_inform).text( alertMessage.noverified() );            

            contactObject.state = 'noverified';
        },
        verified : function (){ 

            setClassesElement('remove', contactElem.field, ['noverified']);
            setClassesElement('remove', contactElem.contact_verify_button, ['link-noactive']); 
            setClassesElement('add', contactElem.field , ['verified']);
            setClassesElement('add', contactElem.contact_verify_button , ['hidden']);

            $(contactElem.contact_verify_button).unbind();

            $(contactElem.contact_inform).text( alertMessage.verified() );
            
            if (!verifiedContacts()) {
                alreadyVerifiedContacts.push(contactObject.contact_value);
            }

            contactObject.state = 'verified';
        }
    };

    initialize();

    function initialize(){  
        _initElements();
        _subscribeEvents();
        
    };

    function _initElements(){


        var id  = contactObject.contact_identify; 
        var field = $(id);      
        contactElem = {
            id :  id,           
            field :  field,
            contact_value : $(field).find('.fn-contact-value'),
            contact_type : $(field).find('.fn-contact-type'),
            contact_verify_button : $(field).find('.fn-contact-verify-button'),
            contact_inform : $(field).find('.fn-contact-inform'),
            contact_delete_button : $(field).find('.fn-contact-delete-button'),
            verified : $(field).hasClass('verified')
        };

        contactObject.contact_value =  $(contactElem.contact_value).val();
        contactObject.contact_type =  $(contactElem.contact_type).val();
        contactObject.verified =  contactElem.verified;
        if (contactObject.verified){
            state('verified');
        } else {
            state('clear');
        }
    };


    function _subscribeEvents(){

            $(contactElem.contact_value).unbind();
            $(contactElem.field).unbind();
            $(contactElem.contact_type).unbind();
            switch (+contactObject.contact_type){
                case 1:
                    $(contactElem.contact_value).inputmask('+7(999)999-99-99' , { "oncomplete": OnCompleteContact, "onincomplete": OnInCompleteContact,  });
                break;
                case 2:
                    $(contactElem.contact_value).inputmask('+7(9999)99-99-99' , { "oncomplete": OnCompleteContact, "onincomplete": OnInCompleteContact, });
                break;
                case 5:
                    $(contactElem.contact_value).bind('keyup', OnChangeContactForValidate);
                break;
            }
         
            $(contactElem.contact_value).bind('keyup', OnChangeContact);
            $(contactElem.contact_type).bind('change', OnChangeContactType);
		
			$('.init-noverified').keyup();

    };

    function checkContact(){
        var returnValue = false;
        $.ajax({
                url         : '/ajax/check_contact',
                dataType    : 'json',
                type        : 'POST',
                async       : false,
                data        : {contact: contactObject.contact_value, contact_type_id : contactObject.contact_type},
                success     : function(json) {
                    //>300 error
                    //201 already verifyed
                    if (json.code == 201) {
                        state('verified');
                        returnValue = true;
                    }
                }
            });

        return returnValue;
    }
    
    

    function contact_value(contact_value){
        if (contact_value){
            contactObject.contact_value = contact_value;
        } else {
            return contactObject.contact_value;
        }
    };

    function contact_type(contact_type){
        if (contact_type){
            contactObject.contact_type = contact_type;
        } else {
            return contactObject.contact_type;
        }
    };

    function state(state_val){
        if (state_val){
            states[state_val]();
        } else {
            return contactObject.state;
        }
    };

    function setClassesElement(method, object, classes){
        var k = 0;
        if (method == 'remove') {
            while (k < classes.length){
                $(object).removeClass(classes[k]);
                k++;
            }
        } else {
            while (k < classes.length){
                $(object).addClass(classes[k]);
                k++;
            }           
        }
    };

    function verifiedContacts(){
        //если не подтвержден
        if (_.indexOf(alreadyVerifiedContacts,contactObject.contact_value)<0){              
            return false;
        } else {
            return true;
        }
    };

    function onContactVerifyButtonClick(){
        if (!checkContact()){
            contactObject.verifyWindow = new verifyContactWindow(contactObject);
            $(contactObject.verifyWindow.win).bind('verifyEnd',onVerifyEnd);
        }
    };

    function onVerifyEnd(e, verifyState){
        state(verifyState);
        contactObject.verifyWindow = null;
    }

    function isValidEmail(email){       
        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    };

    function determineTypeContact(contact){ 
        return undefined;
        if (+contactObject.contact_type == 5) {
            return undefined;
        }
        var mobileRegex = /^((9)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/;
        if ( mobileRegex.test(contact) ){
                return 1;
        }
        var cityRegex = /^((3)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/;
        if ( cityRegex.test(contact) ){
                return 2;
        }
        return undefined;
    };

    //fields events
    function OnChangeContact(e){

        contactObject.contact_value = $(this).val();
        var assumptionContactType = determineTypeContact(contactObject.contact_value);
        
        if (contactObject.contact_type == 5){
            if (state() == 'valided'){
                if  ( verifiedContacts() ){
                    state('verified');
                } else {
                    state('noverified');
                }
            }
        } else {
            if  ( verifiedContacts() ){
                state('verified');
            } else {
                state('noverified');
            }
        }
        
        if ((assumptionContactType) && (contactObject.contact_type != assumptionContactType)){
            $(contactElem.contact_type).val( assumptionContactType );
            $(contactElem.contact_type).trigger('change', [true]);
        };
    };

    function OnChangeContactForValidate(e){
        var id = '#'+$(this).closest('.contact').attr('id');
        if ( isValidEmail( $(this).val() ) ) {
            state('valided');
        } else {
            state('clear');
        }       
    };

    function OnCompleteContact(e){
        state('valided');
    };

    function OnInCompleteContact(e){
        state('clear');
    };

    function OnChangeContactType(e, autoChangeType){
        if (!autoChangeType){
            $(contactElem.contact_value).val('');
            contactObject.contact_value ='';
            state('clear');
        }
        contactObject.contact_type = $(this).val(); 
        _subscribeEvents();
    };

    //
    this.contactElem = contactElem;
}

function ContactList() {
    var self = this;

    var MAX_COUNT = 3;  
    var contactListElems = {};
    var contactAddButton = {};
    var contacts = {};
    var accumulateCountContacts = 1;

    initialize();

    function initialize(){
        _initElements();
        _loadAlreadyLoadedContacts();
        _subscribeEvents();
    }


    function _initElements(){
        var contactsContainerClass  = '.fn-contacts-container'; 
        var contactClass = '.fn-contact';
        contactListElems = {
            contactClass : contactClass,
            contactsContainer :  $(contactsContainerClass),         
            contactList : $(contactsContainerClass).find(contactClass),
            length : function () { return $(contactsContainerClass).find(contactClass).length },
        };  

        var addContactButtonID = '.fn-add-contact-button';  
        var addContactButtonTextClass = '.fn-add-contact-button-text';
        contactAddButton = {
            addButtonContainer : $(addContactButtonID),
            addButtonText : $(addContactButtonID).find(addContactButtonTextClass),
            hide : function (){ $(addContactButtonID).hide(); },
            show : function (){ $(addContactButtonID).show(); },
        }
    }

    function _loadAlreadyLoadedContacts(){
            
        $(contactListElems.contactList).each(function (index){
            var contact_identify = '#'+$(this).attr('id');

            _initNewContactField(contact_identify);

        });
    }

    function _initNewContactField(contact_identify){

        contacts[contact_identify] = new Contact({contact_identify : contact_identify});
        
        _initChosen(contact_identify);

        if (  contactListElems.length() >= MAX_COUNT ){
            contactAddButton.hide();
        }

        accumulateCountContacts++;

    }

    function _initChosen(contact_identify){
        try{
            $(contacts[contact_identify].contactElem.contact_type).chosen({
                                        no_results_text: "Ничего не найдено", 
                                        allow_single_deselect: false});
            $(contact_identify).find('.chzn-search').hide(); 
        } catch (e){
            console.log('chosen not loaded');
        }
       
    }

    function _delContactField(contact_identify){
        $(contact_identify).remove();
        delete contacts[contact_identify];
        
        if ( contactListElems.length() < MAX_COUNT ){           
            contactAddButton.show();
        }
    }

    function _subscribeEvents(contact_identify){
        if (contact_identify) {
            $(contacts[contact_identify].contactElem.contact_delete_button).bind('click', onDeleteContactClick);
        } else {
            for (k in contacts){
                $(contacts[k].contactElem.contact_delete_button).bind('click', onDeleteContactClick);
            }   
            $(contactAddButton.addButtonContainer).bind('click',onAddContactClick);
        }
    }

    function _getNewContactTemplate(){
        var templateIdentify = '#contact_item_template_id';
        var newContactIdName = 'contact_'+accumulateCountContacts;
        var newContactIdentify = '#'+newContactIdName;


        var templateContactTypeIdentify = '#contact_type_select_template_id';
        var newContactTypeIdName = 'contact_type_select_'+accumulateCountContacts;
        var newContactTypeIdentify = '#'+newContactTypeName;
        var newContactTypeName = 'contact_'+accumulateCountContacts+'_type';

        var templateContactValueIdentify = '#contact_value_input_template_id';
        var newContactValueIdName = 'contact_value_input_'+accumulateCountContacts;
        var newContactValueIdentify = '#'+newContactValueName;
        var newContactValueName = 'contact_'+accumulateCountContacts+'_value';


        var templateWindow = $('#add-contact-template').html();
        var template = _.template(templateWindow, {});

        var jqTemplate = $(contactListElems.contactsContainer).append( template ).find(templateIdentify);
        
        $(jqTemplate).attr('id', newContactIdName);

        $(jqTemplate).find(templateContactValueIdentify).attr('name', newContactValueName).attr('id', newContactValueIdName);

        $(jqTemplate).find(templateContactTypeIdentify).attr('name', newContactTypeName).attr('id', newContactTypeIdName);        

        return newContactIdentify;
    }   

    //events
    function onDeleteContactClick(e){
        var id = '#'+$(this).closest(contactListElems.contactClass).attr('id');
        _delContactField(id);
    }

    function onAddContactClick(e){
        var contact_identify = _getNewContactTemplate();
        _initNewContactField(contact_identify);
        _subscribeEvents(contact_identify);
    }

}
