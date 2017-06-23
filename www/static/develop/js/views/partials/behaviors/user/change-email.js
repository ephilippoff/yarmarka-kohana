/*global define */
define([
    'marionette',
], function (Marionette) {
    'use strict';

    var emailModel = Backbone.Model.extend({
        generateUrl: function(url) {
            return '/rest_user/'+url;
        },
        defaults: {
            email: $('#email').text(),
        },

        setState: function(state) {
            this.set('state', state);
        }
    });

    var buttons = {
        ok: {
            color: 'green',
            icon: "<i class='fa fa-check'></i>"
        },
        load: {
            color: 'gray',
            icon: "<i class='fa fa-spinner fa-spin'></i>"
        },
        fail: {
            color: 'red',
            icon: "<i class='fa fa-question' title='Возможно email введен неполностью или с ошибкой'></i>"
        },
        error: {
            color: 'red',
            icon: "<i class='fa fa-remove'></i>"
        },
        empty: {
            color: 'gray',
            icon:  "<i class='fa fa-question'></i>"
        },
    }

    var buttonColor = {
        green: "bg-color-lightgreen",
        red: "bg-color-crimson",
        gray: "bg-color-gray"
    };

    var verificationBlock = '<div class="row js-submit-code-block" style="display: none;">'
                        +'<div class="col-xs-8">'
                            +'<input class="form-control w100p js-check-code" type="text" placeholder="Код проверки">'
                        +'</div>'
                        +'<div class="col-xs-4">'
                            +'<span class="button button-style1 bg-color-blue js-submit-code-button">Подтвердить</span>'
                        +'</div>'
                        +'<div class="col-xs-12"><small></small></div>'
                        +'<div class="col-xs-12">'
                            +'<div class="inp-cont error">'
                               +' <span class="inform js-errors text-danger db mt10" style="display: none;"></span>'
                            +'</div>'
                        +'</div>'
                    +'</div>';

    var emailView = Marionette.ItemView.extend({
        ui: {
            startButton: '.js-change-email-start',
            mailInput: ".js-new-email",
            changeMailButton: '.js-submit-button',
            emailStatusIcon: '.js-icon',
            errorsField: '.js-errors',
            checkCodeInput: '.js-check-code',
            checkCodeButton: '.js-submit-code-button',
            changeEmailBlock: '.js-change-email-block',
            checkCodeBlock: '.js-submit-code-block'
        },

        events: {
            'click @ui.startButton': 'startBehavior',
            "keyup @ui.mailInput": "onChangeEmailValue",
            'click @ui.changeMailButton': 'onSubmitClicked',
            'click @ui.checkCodeButton' : 'checkCode'
        },

        checkCode: function() {
            var self = this,
                code = this.ui.checkCodeInput.val();

            if (!code.length) {
                return;
            }

            this.model.set('code', code);

            this.model.save({}, {
                url: this.model.generateUrl('check_email_code'),
                success: function(model, response) {
                    if (response.code !== 200) {
                        self.showErrorMessages(response.message);
                        return;
                    }

                    window.location = response.message;
                },
                error: function() {
                    self.showErrorMessages('Произошла ошибка на сервере. Повторите попытку позже');
                }
            });
        },

        onSubmitClicked: function() {
            var self = this;

            if (this.model.get('state') !== 'ok') {
                return;
            }

            this.ui.changeMailButton.attr('disabled', 'disabled');

            this.model.save({}, {
                url: this.model.generateUrl('send_email_code'),
                success: function(model, response) {
                    self.ui.changeMailButton.removeAttr('disabled');
                    self.ui.changeEmailBlock.after(verificationBlock).slideUp();
                    self.bindUIElements();
                    self.ui.checkCodeBlock.slideDown().find('small').text(response.message);
                    setTimeout(function() {
                        self.ui.changeEmailBlock.remove();
                    }, 300);
                },
                error: function() {
                    self.showErrorMessages('Произошла ошибка на сервере. Повторите попытку позже');
                }
            });
        },

        onChangeEmailValue: function(e) {
            if (this.timer) 
                clearTimeout(this.timer);

            this.model.setState('load');

            var value = e.target.value,
                self = this;

            if (!value.length) {
                this.model.setState('empty');
                self.showErrorMessages();
                return;
            }

            this.timer = setTimeout(function() {

                if (!this.isValidEmail(value)) {
                    self.model.setState('fail');
                    self.showErrorMessages();
                    return;
                }

                self.model.set('email', value);


                self.model.save({}, {
                    url: self.model.generateUrl('check_email'),
                    success: function(model, response) {
                        switch (response.code) {
                            case 200: {
                                self.ui.changeMailButton.slideDown();
                                self.model.setState('ok');
                                self.showErrorMessages();
                                break;
                            }

                            default: {
                                self.showErrorMessages(response.message);
                                break;
                            }
                        }
                    },
                    error: function() {
                        self.showErrorMessages('Произошла ошибка на сервере. Повторите попытку позже');
                    }
                });
            }, 600);
        },

        showErrorMessages: function(errors) {
            var message = errors || undefined;

            if (message) {
                this.ui.errorsField.show().text(message);
                return;
            }

            this.ui.errorsField.hide();
        },

        isValidEmail: function(email) {
            return /^[a-z0-9]+([-._][a-z0-9]+)*@([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,4}$/.test(email)
                        && /^(?=.{1,64}@.{4,64}$)(?=.{6,100}$).*/.test(email);
        },

        onStateChanged: function(model, state) {
            var prevState = model.changed.state;
            this.ui.emailStatusIcon.html(buttons[state]['icon'])
                .removeClass(Object.values(buttonColor).join(' '))
                .addClass(buttonColor[buttons[state]['color']]);
        },

        startBehavior: function() {
            this.ui.changeEmailBlock.slideDown();
        },

        initialize: function() {
            this.bindUIElements();
            this.listenTo(this.model, 'change:state', this.onStateChanged);
            this.model.setState('empty');
        }

    });



	return Marionette.Behavior.extend({

        initialize: function() {
            new emailView({
                el: $('#email-cont'),
                model: new emailModel({})
            })
        }

    });
});