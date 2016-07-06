define([

    "marionette",
    "templates/set/add"
    ], function(Marionette, templates) {
        "use strict";

        var verifyWindowView = Backbone.View.extend({
            tagName: "div",
            className: "popup enter-popup fn-verify-contact-win",
            template: templates.verifyContactWindow,
            events: {
                'click .fn-verify-contact-win-close': 'close',
                'click .fn-verify-contact-win-submit': 'doVerifyCode'
            },
            initialize: function(options) {
                _.extend(this, options);
                this.render();

                if (this.model.get("type") == "1" ||
                    this.model.get("type") == "5")
                    this.sendSms();
                else
                    this.showVerificationCode();
            },
            render: function() {
                var html = _.template(this.template)(this.model.toJSON());
                $('body').append(this.$el.html(html));
                $('body').find('.popup-layer').fadeIn();
                this.$el.fadeIn();
                return this;
            },
            close: function() {
                this.unbind();
                this.remove();
                $('body').find('.popup-layer').fadeOut();
            },
            doVerifyCode: function() {
                if (this.model.get("type") == "1" ||
                    this.model.get("type") == "5")
                    this.checkCode();
                else
                    this.checkHomePhoneCode();

            },
            sendSms: function(force) {
                var self = this;
                var params = {
                    contact_type_id: this.model.get("type"),
                    force: force
                };
                if (this.model.get("type") == "5")
                    params.email = this.model.get("value");
                else
                    params.phone = this.model.get("value");
                $.post('/ajax/sent_verification_code', params,
                    function(json) {
                        self.responseSms(json, self);
                    },
                    'json');
            },
            responseSms: function(json, context) {
                var self = context;
                self.contact_id = json.contact_id;
                switch (+json.code) {
                    case 200:
                    self.setError("Сообщение с кодом отправлено");
                    break;
                    case 303:
                    self.setError("Сообщение с кодом уже отправлено вам ранее");
                    break;
                    case 301:
                            //уже верифицирован
                            self.model.set("status", 'verified');
                            self.close();
                            break;
                            case 302:
                            //"Контакт принадлежит другому пользователю"
                            self.setError(json.msg);
                            break;
                            case 304:
                            self.setError("Вы исчерпали лимит SMS на сегодня");
                            break;
                            case 305:
                            self.setError('Контакт в черном списке');
                            break;
                            case 401:
                            self.setError('Это не мобильный телефон, выберите "городской"');
                            break;
                        }
                    },
                    checkCode: function() {
                        var self = this;
                        var code = this.$el.find(".fn-input-code").val();

                        if (!self.contact_id)
                            return;

                        $.post('/ajax/check_contact_code/' + self.contact_id, {
                            code: code
                        }, function(json) {
                            if (json.code == 200) {
                                self.model.set("status", 'verified');
                                self.close();
                            } else {
                                self.setError("Неправильный код");
                            }
                        }, 'json');
                    },
                    checkHomePhoneCode: function() {
                        var self = this;
                        var code = this.$el.find(".fn-input-code").val();
                        if (code == this.verificationCode) {
                            $.post('/ajax/verify_home_phone', {
                                contact: self.model.get("value")
                            },
                            function(json) {
                                if (json.code == 200) {
                                    self.model.set("status", 'verified');
                                    self.close();
                                } else {
                                    self.setError(json.msg);
                                }
                            }, 'json');
                        } else {
                            self.setError("Неправильный код");
                        }
                    },
                    showVerificationCode: function() {
                        this.verificationCode = this.generateVerificationCode();
                        this.setError('Этот номер телефона пройдет проверку. Подтвердите что вы согласны, введите код:' + this.verificationCode);
                    },
                    generateVerificationCode: function() {
                        var verificationCode = _.random(1000, 9999);
                        return verificationCode;
                    },
                    setError: function(text) {
                        this.$el.find(".fn-error-block").html(text);
                    }

                });

        var contactModel = Backbone.Model.extend({
            defaults: {
                type: 1,
                value: "",
                status: "clear"
            }
        });

        var contactList = Backbone.Collection.extend({
            model: contactModel
        });

        var contactView = Marionette.ItemView.extend({
            tagName: "div",
            className: "contact-cont fn-contact",
            template: templates.contact,
            ui: {
                delete: ".fn-contact-delete-button",
                verify: ".fn-contact-verify-button",
                type: ".fn-contact-type",
                value: ".fn-contact-value"
            },
            events: {
                'click @ui.delete': 'remove',
                'click @ui.verify': 'doVerify',
                'change @ui.type': 'changeType'
            },
            initialize: function(options) {
                _.extend(this, options);
            },
            _init_inputs: function() {
                this.contact = this.$el.find(".fn-contact-value");
                this.type = this.$el.find(".fn-contact-type");
                this.format = this.type.find("option:selected").data("format");
                this.validation_type = this.type.find("option:selected").data("validation-type");

                this.setValues();
            },
            _init_mask: function() {
                var self = this;
                if (this.model.get("type") != 5) {
                    $(this.contact).mask(this.format, {
                        "completed": function() {
                            self.model.set("status", "valided");
                            self.setValues();
                        }
                    });
                } else {
                        //$(this.contact).val("");
                        $(this.contact).unbind();
                    }
                },
                setValues: function() {
                    this.model.set("value", this.contact.val());
                    this.model.set("type", this.type.val());
                },
                onRender: function() {
                    // var html     =  _.template(this.template, this.model.toJSON());
                    // this.container.append(this.$el.html(html));
                    this._init_inputs();
                    this._init_mask();
                    // return this;
                },
                remove: function() {
                    console.log(this.model)
                    this.model.collection.remove(this.model);
                },
                changeType: function() {
                    this._init_inputs();
                    this._init_mask();
                },
                doVerify: function() {
                    this.setValues();
                    this.window = new verifyWindowView({
                        model: this.model
                    });
                },

                changeStatus: function() {
                    this.$el.removeClass("verified");
                    this.$el.removeClass("noverified");
                    this.$el.find(".fn-contact-inform").html("");

                    if (this.model.get("status") == "clear") {

                    } else
                    if (this.model.get("status") == "valided") {

                    } else
                    if (this.model.get("status") == "verified") {
                        this.$el.addClass("verified");
                        this.$el.find(".fn-contact-inform").html("Контакт подтвержден");
                    } else
                    if (this.model.get("status") == "noverified") {
                        this.$el.addClass("noverified");
                    }
                },

            });

        return Marionette.CollectionView.extend({
            el: "#div_contacts",
            contacts: [],
            childView: contactView,
            buildChildView: function(child, ChildViewClass, childViewOptions) {
                var options = _.extend({
                    model: child
                }, childViewOptions);
                if ($('#contact_' + child.id).length) {
                    options.$el = $('#contact_' + child.id);
                }
                var view = new ChildViewClass(options);
                return view;
            },
            ui: {
                "addContact": ".fn-add-contact-button-text",
                "contacts": ".contact-cont"
            },
            events: {
                'click @ui.addContact': 'addContact'
            },

            initialize: function(options) {
                _.extend(this, options);
                var self = this;
                this.bindUIElements();

                this.collection = new contactList();
                    // this.collection.on("add", this.addItem, this);
                    // this.collection.on("remove", this.removeItem, this);
                    this.collection.on("change:status", this.changeStatus, this);

                    _.each(this.ui.contacts, function(item) {
                        var status = "clear",
                        $item = $(item);
                        if ($item.hasClass("verified"))
                            status = "verified";
                        else if ($item.hasClass("noverified"))
                            status = "noverified";

                        var params = {
                            id: $item.data("item-id"),
                            value: $item.find(".fn-contact-value").val(),
                            type: $item.find(".fn-contact-type").val(),
                            status: status
                        };
                        self.collection.add(params);
                    });
                },

                addContact: function() {
                    this.collection.add({
                        id: this.collection.length + 1
                    });
                },

                changeStatus: function(item) {
                    this.contacts[item.cid].changeStatus();
                }

            });

});