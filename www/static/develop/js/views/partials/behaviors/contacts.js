/*global define */
define([
    'marionette',
    'templates',
    "maskedInput",
], function (Marionette, templates) {
    'use strict';

    var typeFormat = {
        mobile: "+7(999)999-99-99",
        phone: "+7(9999)99-99-99"
    }

    var okButtonValue = {
        ok: "<i class='fa fa-check'></i>",
        load: "<i class='fa fa-spinner fa-spin'></i>",
        fail: "<i class='fa fa-question' title='Возможно контакт введен не полностью или с ошибкой'></i>",
        ready: "Нажмите чтобы подтвердить",
        empty: "<i class='fa fa-question'></i>",
        error: "<i class='fa fa-remove'></i>",
        codeLoad: "<i class='fa fa-spinner fa-spin mr3'></i>Код отправлен"
    }

    var okButtonColor = {
        green: "bg-color-lightgreen",
        red: "bg-color-crimson",
        gray: "bg-color-gray"
    }

    var ContactModel = Backbone.Model.extend({
        urlRoot: "/rest_user/check_contact"
    });

    var CheckCodeModel = Backbone.Model.extend({
        urlRoot: "/rest_user/check_code",
        checkCode: function(options) {
            this.save({}, {
                success: function(respModel) {
                    var resp = respModel.toJSON();
                    if (resp.code == 200) {
                        options.success(resp);
                    } else {
                        options.error(resp);
                    }
                }
            });
        },
    });

    var VerificationModel = Backbone.Model.extend({
        urlRoot: "/rest_user/sent_code",
        sendCode: function(options) {
            this.save({}, {
                success: function(respModel) {
                    var resp = respModel.toJSON();
                    if (resp.code == 200) {
                        options.success(resp);
                    } else if (resp.code >= 300 && resp.code < 400) {
                        options.alreadyVerified(resp);
                    } else {
                        options.error(resp);
                    }
                }
            });
        },

    });

    var ContactView = Marionette.ItemView.extend({
        ui: {
            "inputValue": ".js-contact-value",
            "buttonOk": ".js-contact-ok",
            "contactCodeCont": ".js-contact-code",
            "contactDescription": ".js-contact-description",
            "inputCode": ".js-contact-code-value",
            "buttonCodeOk": ".js-contact-code-ok",
            "codeDescription":".js-contact-code-description"
        },
        events: {
            "keyup @ui.inputValue": "keyUpContact",
            "change @ui.inputValue": "keyUpContact",
            "click @ui.buttonOk": "submitContact",
            "click @ui.buttonCodeOk": "submitCode"
        },
        modelEvents: {
            "change:value": "changeContact",
            "change:state": "changeState"
        },
        initialize: function() {
            var s = this;
            this.bindUIElements();
            this.model.set("value", this.ui.inputValue.val());

            if (typeFormat[this.model.get("type")]) {
                this.ui.inputValue.mask(typeFormat[this.model.get("type")], {
                    "completed": function(){
                       s.model.set("ready", s.ui.inputValue.val());
                    }
                });
            }
        },
        changeContact: function(e) {
            var s = this;
            this.model.set("state","load");
            if (this.keyupTimeout) clearTimeout(this.keyupTimeout);
            this.keyupTimeout = setTimeout(function(){
                s.checkContact();
            }, 600);
        },
        keyUpContact: function(e) {
            e.preventDefault();
            this.showHideCodeInput(false);
            if (this.ui.inputValue.val() != this.model.get("ready")) {
                this.model.set("ready", false);
            }
            if (this.model.get("type") == "email") {
                this.model.set("ready", true);
            }
            this.model.set("value", this.ui.inputValue.val());
        },
        checkContact: function() {
            var s = this;
            this.model.save({},{
                success: function(respModel) {
                    var resp = respModel.toJSON();
                    s.showHideMessage(false);
                    if (resp.code == 200) {
                        s.model.set("state","ok");
                    } else if (resp.code == 300) {
                        if (s.model.get("ready")){
                            s.model.set("state","ready");
                        } else {
                            s.model.set("state","fail");
                        }
                    } else {
                        s.model.set("state","error");
                        s.showHideMessage(resp.text);
                    }
                }
            })
        },
        changeState: function()
        {
            var state = this.model.get("state");
            
            if (state == "load" || state == "fail" || state == "empty" || state == "codeLoad") {
                this.ui.buttonOk.removeClass(okButtonColor.red).removeClass(okButtonColor.green).addClass(okButtonColor.gray);
            } else if (state == "ok") {
                this.ui.buttonOk.removeClass(okButtonColor.red).addClass(okButtonColor.green).removeClass(okButtonColor.gray);
            } else if (state == "ready" || state == "error")  {
                this.ui.buttonOk.addClass(okButtonColor.red).removeClass(okButtonColor.green).removeClass(okButtonColor.gray);
            } 

            if ( this.model.get("description_" + this.model.get("state")) ) {
                this.ui.buttonOk.html(okButtonValue[state] + this.model.get("description_" + this.model.get("state")));
            } else {
                this.ui.buttonOk.html(okButtonValue[state]);
            }
        },
        showHideCodeInput: function(show) {
            this.ui.contactDescription.addClass("hidden");
            if (show) {
                this.ui.contactCodeCont.removeClass("hidden");
            } else {
                this.ui.inputCode.val("");
                this.ui.contactCodeCont.addClass("hidden");
            }
        },
        showHideMessage: function(text) {
            this.ui.contactCodeCont.addClass("hidden");
            if (text) {
                this.ui.contactDescription.text(text);
                this.ui.contactDescription.removeClass("hidden");
            } else {
                this.ui.contactDescription.text("");
                this.ui.contactDescription.addClass("hidden");
            }
        },
        showHideCodeDescription: function(text) {
            if (text) {
                this.ui.codeDescription.text(text);
                this.ui.codeDescription.removeClass("hidden");
            } else {
                this.ui.codeDescription.text("");
                this.ui.codeDescription.addClass("hidden");
            }
        },
        submitContact: function(e)
        {
            e.preventDefault();

            if (this.ui.contactDescription.text().trim().length && /android|iphone|iemobile|opera mini/.test(navigator.userAgent.toLowerCase())) {
                this.ui.contactDescription.toggleClass('show');
                return;
            }

            var s = this;
            var state = $(e.currentTarget).data("state");
            $(e.currentTarget).attr("data-state", false);
            if (state) {
                this.model.set("state", state);
            }
            if (this.model.get("state") == "error") {
                s.showHideMessage(false);
                this.ui.inputValue.val("");
                this.model.set("state", "empty");
                return;
            }
            if (this.model.get("state") != "ready") return;
            if (s.sendTimeout) clearInterval(s.sendTimeout);
            this.ui.inputValue.prop("disabled", true);

            var verificationModel = new VerificationModel({
                type: this.model.get("type"),
                value: this.model.get("value"),
            });

            this.model.set("state","codeLoad");

            verificationModel.sendCode({
                success: function(respModel){
                    s.showHideCodeInput(true);
                    s.showHideCodeDescription(false);
                    var i = 90;

                    s.sendTimeout = setInterval(function(){
                        if (i <= 0) {
                            s.model.set("description_codeLoad", null);
                            clearInterval(s.sendTimeout);
                            s.ui.inputValue.prop("disabled", false);
                            s.model.set("state","ready");
                        } else {
                            s.model.set("description_codeLoad",". Повтор через ("+i+")");
                        }
                        s.changeState();
                        i = i - 1;
                    },1000);
                },
                alreadyVerified: function(respModel) {
                    s.model.set("state","ok");
                    s.showHideMessage(false);
                    s.ui.inputValue.prop("disabled", false);
                    if (respModel.text) {
                        s.showHideMessage(respModel.text);
                    }
                },
                error: function(respModel){
                    s.showHideMessage(respModel.text);
                    s.ui.inputValue.prop("disabled", false);
                    s.model.set("state","error");
                }
            });

        },
        submitCode: function(e)
        {
            e.preventDefault();
            var s = this;
            if (this.model.get("state") != "codeLoad") return;

            var checkCodeModel = new CheckCodeModel({
                type: this.model.get("type"),
                value: this.model.get("value"),
                code: this.ui.inputCode.val()
            });

            checkCodeModel.checkCode({
                success: function(respModel){
                   console.log(respModel);
                   s.showHideCodeInput(false);
                   s.showHideCodeDescription(false);
                   s.model.set("state","ok");
                   s.ui.inputValue.prop("disabled", false);
                },
                error: function(respModel){
                    s.showHideCodeDescription(respModel.text);
                }
            });
        }
    });

	return Marionette.Behavior.extend({
        ui: {
            contacts: $(".js-contact")
        },

        events: {
           
        },
        initialize: function() {
            _.each(this.ui.contacts, function(item) {
                var type = $(item).data("type");

                new ContactView({
                    el: item,
                    model: new ContactModel({type: type})
                })

            });
        }
       
    });
});