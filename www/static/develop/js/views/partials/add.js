/*global define */
define('jquery', [], function() {
    return jQuery;
});

require.config({
    paths : {
         underscore : 'lib/underscore',
         backbone   : 'lib/backbone',
         marionette : 'lib/backbone.marionette',
         cropper: 'lib/cropper',
         ymap: 'http://api-maps.yandex.ru/2.1/?load=package.full&lang=ru-RU',
         fileupload: 'lib/vendor/jquery.fileupload',
         nicEdit: 'lib/vendor/nicEdit',
         maskedInput: 'lib/vendor/jquery.maskedinput',
         ckeditor: 'lib/ckeditor/ckeditor',
         ckeditorJqueryAdapter: 'lib/ckeditor/adapters/jquery'
    },
    shim : {
        localStorage : ['backbone'],
        underscore : {
            exports : '_'
        },
        backbone : {
            exports : 'Backbone',
            deps : ['underscore']
        },
        marionette : {
            exports : 'Backbone.Marionette',
            deps : ['backbone']
        },
        paginator : {
            deps : ['backbone'],
            exports: 'Backbone.Paginator'
        },
        fileupload: {
             deps : ['iframeTransport']
        },
         ckeditorJqueryAdapter: {
            deps: [ 'ckeditor' ]
        }
    },
    deps : ['underscore']
});

var CkEditor;

define([

    "marionette",
    "templates/set/add",
    "views/partials/behaviors/contacts",
    "views/partials/behaviors/add-services",
    "nicEdit",
    "maskedInput",
    "ymap",
    'cropper',
    'views/partials/add/additional_contacts',
    'views/partials/add/photo'

    ], function(
        Marionette, 
        templates, 
        ContactsBehavior,
        AddServiceBehavior,
        nicEdit, 
        maskedInput, 
        ymap, 
        cropper, 
        AdditionalContacts,
        photoControlView
    ) {
        "use strict";

        var additionalContactsController = new AdditionalContacts.Controller({});

        var paramList = Backbone.Collection.extend({
            comparator: function(collection) {
                return (collection.get('weight'));
            }
        });

        var paramView = Backbone.View.extend({
            tagName: 'div',
            containers: {
                "list": '.fn-list-parameters',
                "row": '.fn-rows-parameters'
            },
            events: {
                'change': 'change',
                'keyup': 'keyup'
            },
            initialize: function(options) {
                _.extend(this, options);
                this.$el = $("#" + this.model.get("id"));

                if (!this.model.get("type")) {
                    this.type = this.model.get("data")[0]["type"];
                    this.model.set("type", this.type);
                }

                if (this.$el.length == 0)
                    this.render();
                else
                    this.model.set("value", this.$el.val());

                var childs = this.initChilds();
                this.model.set("childs", childs);
            },

            render: function() {
                var self = this;

                var template_name = this.model.get("type");
                if (this.model.get("custom"))
                    template_name = "custom" + this.model.get("custom");

                var html = _.template(templates[template_name])(this.model.toJSON());

                /* вставляем элемент на свою позицию */
                this.appendToOwnPosition(this.model, html);

                this.$el = $("#" + this.model.get("id"));


                //init ckeditor on kupon text
                if ((this.model.get('id') == 'param_1000' || this.model.get('id') == 'param_1003') &&
                    _globalSettings.allowCkEditor) {

                    CkEditor.replaceOne(this.$el, { fileUpload: true });
                }

                /* элемент будет одиночным в строке если есть соответствующая настройка */
                this.whitespace();

                return html;
            },

            appendToOwnPosition: function(model, html) {
                var container = $(this.containers[model.get("container")]);

                /* берем элементы уже находящиеся в контейнере */
                var collection = new paramList(this.collection.where({
                    "container": model.get("container")
                }));

                /* определяем позицию нового элемента в коллекции */
                var index = collection.indexOf(this.model);

                /* если коллекция не пуста */
                if (collection.length > 1) {
                    /* если элемент не первый */
                    if (index) {
                        /* ищем предыдущую по порядку модель */
                        var previousModel = collection.at(index - 1);
                        /* размещаем элемент после него */
                        $(html).insertAfter("#div_" + previousModel.get("id"));
                    } else {
                        var previousModel = collection.at(1);
                        $(html).insertBefore("#div_" + previousModel.get("id"));
                    }
                } else {
                    /* если коллекция пуста, то размещаем новый элемент первым в контейнере */
                    container.append(html);
                }
            },

            whitespace: function() {
                if (this.model.get("options") == 'whitespace')
                    this.$el.closest(".col-md-6").addClass("whitespace");
            },

            change: function(e) {
                this.removeChilds(this.model.get("childs"));
                this.model.set("value", $(e.target).val());
                var childs = this.initChilds();
                this.model.set("childs", childs);
                var wrapper = "div_" + this.model.get("id");
                if (this.model.get("value")) {
                    this.app.removeError(wrapper);
                    this.app.removeRequired(wrapper);
                } else {
                    this.app.addRequired(wrapper);
                }
                this.paramsBlock.initAddressPrecision();
            },

            keyup: function(e) {
                this.model.set("value", $(e.target).val());
                var wrapper = "div_" + this.model.get("id");
                if (this.model.get("value")) {
                    this.app.removeError(wrapper);
                    this.app.removeRequired(wrapper);
                } else {
                    this.app.addRequired(wrapper);
                }
            },

            initChilds: function() {
                var self = this,
                d_attr = this.model.get("data"),
                d_attr_childs = [];

                if (this.model.get("type") != "list")
                    return;

                if (_.isArray(this.model.get("value"))) {
                    _.each(this.model.get("value"), function(item) {
                        d_attr_childs.push(d_attr[item]);
                    });
                } else {
                    d_attr_childs.push(d_attr[this.model.get("value")]);
                }

                var childs = [];

                _.each(d_attr_childs, function(dch) {
                    if (_.isObject(dch)) {
                        _.each(dch, function(item, key) {
                            if (key == 0) return;
                            var param = {
                                classes: "fn-param",
                                value: "",
                                data: item,
                                added: 1,
                                parent_id: self.model.get("id")
                            }
                            _.extend(param, item[0]);
                            self.collection.add(param);
                            childs.push(param.id);
                        });
                    }
                });

                return childs;
            },

            removeChilds: function(childs) {
                var self = this;
                _.each(childs, function(item) {

                    if (self.collection.get(item).get("childs"))
                        self.removeChilds(self.collection.get(item).get("childs"));

                    self.collection.remove(self.collection.get(item));
                });
            }

        });

var adTypeView = Backbone.View.extend({
    el: '#ad_type',
    events: {
        'click': 'toggleContacts'
    },
    initialize: function() {
        this.toggleContacts();
    },

    toggleContacts: function(){
        console.log('clicked');
        var value = this.$el.val();
        if (value == 101) 
            $('.cont_block, #additional_contacts').slideUp();
        else 
            $('.cont_block, #additional_contacts').slideDown();
    }
});


var paramsView = Backbone.View.extend({
    el: '#div_params',
    template: templates.parameters,
    initialize: function(options) {
        var self = this;
        _.extend(this, options);

        this.bind("destroy", this.destroy);

        var d_cat = data[this.category_id];
        var attributes_exist = (_.keys(d_cat).length > 1)

        if (!attributes_exist) return;

        if (this.$el.children().length == 0) this.render();

        this.collection = new paramList();
        this.collection.on('add', this.addItem, this);
        this.collection.on('remove', this.removeItem, this);

        _.each(d_cat, function(item_data, key) {
            if (key == 0) return;

            var elem = $('#' + item_data[0].id);
            var cl = elem.attr("class");
            var val = elem.val();

            var param = {
                classes: (cl) ? cl : "fn-param",
                value: (val) ? val : "",
                data: item_data,
                added: (elem.length) ? 1 : 0
            }
            _.extend(param, item_data[0]);
            self.collection.add(param);
        });

        this.initAddressPrecision();
    },

    render: function() {
        var params = {
            lists: 0
        };
        var html = _.template(this.template)({});
        this.$el.html(html);
        return this;
    },

    addItem: function(item) {
        if (item.get("custom") == 'hidden') return;
        item.set("container", this.getContainer(item));

        if (item.get("type") == "text" && item.get("is_textarea"))
            item.set("type", "textarea");
        if (item.get("type") == "ilist")
            item.set("type", "list");
        new paramView({
            model: item,
            collection: this.collection,
            app: this.app,
            paramsBlock: this
        });

        if (item.get("custom") == "address")
            this.app._init_map(item.get("id"));
    },

    removeItem: function(item) {
        if (item.get("custom") == "address")
            this.app.cmap.trigger("disable");

        $("#div_" + item.get("id")).remove();
    },

    getContainer: function(model) {
        var type = model.get("type");
        var custom = model.get("custom");
        if (_.contains(["list", "ilist"], type) > 0 && custom != 'multiselect')
            return "list";
        else
            return "row";
    },

    destroy: function() {
        this.$el.empty();
        this.stopListening();
        return this;
    },

    initAddressPrecision: function() {
        if (!this.address_precisions)
            return;
        var self = this,
        _filter = null;

        _.each(this.address_precisions, function(item) {
            _filter = item.filters;

            delete _filter["rubricid"];

            if (_.keys(_filter).length) {
                _.each(_filter, function(filter, name) {

                    if (self.collection.get(name) && _.indexOf(filter, +self.collection.get(name).get("value").replace("_", "")) != -1) {
                        self.app.address_precision = item.precision;
                        self.app.precision_error = item.error_text;
                        if (self.app.cmap.mapcontrol)
                            self.app.cmap.mapcontrol.setMessage(item.error_text, "blue");
                    }
                });
            } else {
                self.app.address_precision = item.precision;
                self.app.precision_error = item.error_text;
                if (self.app.cmap.mapcontrol)
                    self.app.cmap.mapcontrol.setMessage(item.error_text, "blue");
            }

        });
    }

});

var addCitiesView = Backbone.View.extend({
    el: '#div_cities',
    events: {
        'click #select_all': 'selectAll',
        'mouseup #cities option' : 'checkSelectAll'
    },
    initialize: function() {
        this.checkSelectAll();
    },

    selectAll: function(){
        var options = this.$el.find('#cities option');
        if ($('#cities option:selected').length !== $('#cities option').length){
            options.prop('selected', true);
        }else options.prop('selected', false);
    },

    checkSelectAll: function(){
        if ($('#cities option:selected').length == $('#cities option').length){
            $('#select_all').attr('checked', 'checked').prop('checked', true);
        }
        else
            $('#select_all').removeAttr('checked');
    }
});

var cityView = Backbone.View.extend({
    el: '#div_city',
    events: {
        'change select': 'change',
        'click #real_city_exists': 'setRealCity',
        'keyup #real_city': 'changeRealCity'
    },
    initialize: function(options) {
        _.extend(this, options);
        this.control = this.$el.find("select");
        if (!this.control.length) {
            this.control = this.$el.find("input");
            this.value = this.control.val();
            this.title = this.control.data("title");
        } else {
            this.value = this.control.val();
            this.title = this.control.find('option:selected').text();
        }
        this.init_real_city();
    },
    init_real_city: function() {
        this.app.real_city_exists = $("#real_city_exists").prop("checked");
        this.app.real_city = this.app.real_city_exists ?
        $("#real_city").val() :
        "";
    },
    change: function() {
        this.value = this.control.val();
        this.title = this.control.find('option:selected').text();
        this.setLatLon();
        if (this.app.cmap.mapcontrol)
            this.app.cmap.mapcontrol.keyup();
        var wrapper = "div_city";
        if (this.value) {
            this.app.removeError(wrapper);
            this.app.removeRequired(wrapper);
        } else {
            this.app.addRequired(wrapper);
        }

        this.app.triggerMethod("ChangedCity", {city_id: +this.value, category_id: this.app.category.category_id});
    },
    setLatLon: function() {
        var option = this.control.find('option:selected');
        this.control.attr("data-lon", option.attr("lon"));
        this.control.attr("data-lat", option.attr("lat"));
    },
    setRealCity: function(event) {
        $(".real_city_exists").toggle();
        $(".real_city_not_exists").toggle();
        if ($('.real_city_not_exists').is(':visible')) {
            $('.real_city_exists input').val('');
        }
        this.changeRealCity();
    },
    changeRealCity: function(e) {
        this.init_real_city();
        if (this.app.cmap.mapcontrol) {
            this.app.cmap.mapcontrol.setMessage("");
            this.app.cmap.mapcontrol.keyup(e);
        }
    }
});

var mapView = Backbone.View.extend({
    el: '#div_map',
    initialize: function(options) {
        _.extend(this, options);
        this.bind("enable", this.on);
        this.bind("disable", this.off);
    },
    on: function(options) {
        var self = this;
        _.extend(self, options);

        self.$el.removeClass("hidden");

        ymaps.ready(function() {

            var coords = $('#object_coordinates').val();
            var default_lat = $('#city_id').data("lat");
            var default_lon = $('#city_id').data("lon");
            var default_coords = [default_lat, default_lon];
            var default_zoom = 10;
            var zoom = null;
            if (!default_lat) {
                    default_coords = [57.140738, 65.573836]; //Тюмень
                    default_zoom = 7;
                }

                if (!coords) {
                    coords = default_coords;
                    zoom = default_zoom;
                } else {
                    coords = coords.split(",");
                    zoom = 13;
                }

                self.map = new ymaps.Map('map_block', {
                    center: coords,
                    zoom: zoom,
                    controls: ['smallMapDefaultSet']
                });



                self.placemark = app.map.createPlacemark(coords, {
                    style: _.extend(app.map.getIconSettings("house1"), {
                       draggable: true
                    }),
                })

                self.map.geoObjects.add(self.placemark);

                self.placemark.events.add('dragend', function(e) {
                    $('#object_coordinates').val(self.placemark.geometry.getCoordinates());
                });

                self.map.events.add('click', function(e) {
                    placemark.geometry.setCoordinates(e.get('coordPosition'));
                    $('#object_coordinates').val(self.placemark.geometry.getCoordinates());
                });

                self.mapcontrol = new mapcontrolView({
                    app: self.app,
                    city: self.city,
                    address_field: self.address_field,
                    map: self.map,
                    placemark: self.placemark
                });
            });
},
off: function() {
    this.$el.addClass("hidden");
    if (this.map)
        this.map.destroy();
    this.map = null;
}
});

var mapcontrolView = Backbone.View.extend({
    events: {
        'keyup': 'keyup',
        'focusout': 'keyup',
        'paste': 'keyup'
    },

    precisions: {
        'other': 1,
        'street': 2,
        'exact': 3
    },

    initialize: function(options) {

        _.extend(this, options);
        this.$el = $('#' + this.address_field);

            //if (this.app.is_edit)
            this.keyup();
        },

        keyup: function() {
            var self = this;
            var city = null;

            if (this.app.real_city_exists && this.app.real_city)
                city = this.app.real_city;
            else if (self.city.value)
                city = self.city.title;

            if (city) {
                self.search = city + ', ' + this.$el.val();
                window.clearTimeout(self.timeout);
                self.timeout = setTimeout(function() {
                    self.geoCoder();
                }, 500);
            } else {
                this.setMessage("Город не определен. Поле 'Город'  обязательно для заполнения", "red");
            }
        },

        setMessage: function(message, color) {
            var cmessage = $('#div_' + this.address_field).find(".inform");
            if (message) {
                cmessage.html(message);
                cmessage.css("color", color);
                if (color == 'red')
                    $('#div_' + this.address_field).find(".inp-cont").addClass("error");
                else
                    $('#div_' + this.address_field).find(".inp-cont").removeClass("error");
            } else {
                cmessage.html("Например: ул. Мельникайте, д. 44, корп. 2");
                $('#div_' + this.address_field).find(".inp-cont").removeClass("error");
            }
        },

        geoCoder: function() {
            var self = this;
            var zoom = 14;
            var myGeocoder = ymaps.geocode(self.search, {
                results: 1,
                json: true
            });
            myGeocoder.then(
                function(res) {
                    if (res.GeoObjectCollection.featureMember.length == 0) {
                        $('#object_coordinates').val('');
                        self.setMessage("Адрес не найден, видимо он не существует.", "red");
                    } else {
                        self.setMessage();
                        var gobj = res.GeoObjectCollection.featureMember[0].GeoObject;
                        var points = gobj.Point.pos.split(" ");
                        self.placemark.geometry.setCoordinates([Number(points[1]), Number(points[0])]);
                        self.map.setCenter([Number(points[1]), Number(points[0])], zoom);
                        $('#object_coordinates').val(self.placemark.geometry.getCoordinates());

                        var precision = self.precisions[gobj.metaDataProperty.GeocoderMetaData.precision];

                        if (self.app.address_precision && self.precisions[self.app.address_precision] <= precision && self.app.precision_error)
                            self.setMessage("Адрес введен верно. " + self.app.precision_error, "gray");
                        else
                            if (self.app.precision_error)
                                self.setMessage("Адрес не найден. " + self.app.precision_error, "red");
                        }

                    },
                    function(err) {
                        self.setMessage("Адрес не найден, видимо он не существует", "red");
                    }
                    );
}

    });

var subjectView = Backbone.View.extend({
    template: templates.subject,
    tagName: 'div',
    initialize: function(options) {
        _.extend(this, options);
        this.bind("destroy", this.destroy);
        this.control = this.$el.find("input");
        this.maxlength = (this.app.settings.subject_max_length) ? this.app.settings.subject_max_length : 75;
        this.inform = this.$el.find(".inform");
        this.value = this.control.val();
        this.error = this.inform.html();
            if (!this.title_auto && !this.app.is_edit || !this.title_auto && this.app.is_edit) // 
                this.render();
        },

        render: function() {
            var html = _.template(this.template)({
                value: this.value,
                error: this.error,
                maxlength: this.maxlength
            });
            this.$el.html(html);
            return this;
        },

        destroy: function() {
            this.$el.empty();
            this.stopListening();
            return this;
        }
    });

var textView = Backbone.View.extend({
    template: templates.textadv,
    tagName: 'div',
    initialize: function(options) {
        _.extend(this, options);
        this.bind("destroy", this.destroy);
        this.control = this.$el.find("textarea");
        this.value = this.control.val();
        
        if (!this.control.length)
            this.render();

        var staticPath = app.settings.staticPath;

        if (this.text_required) {
            if (!_globalSettings.allowCkEditor) {
                new nicEditor({
                    iconsPath: staticPath + 'images/nicEditorIcons.gif'
                }).panelInstance('user_text_adv');
            } else {
                CkEditor.replaceOne('#user_text_adv', {
                    fileUpload: true
                });
            }
         } else {
            if (this.app.category.category_id) {
                this.clear();
            }
         }
    },

    render: function() {

        var html = _.template(this.template)({
            value: this.value,
            text_required: this.text_required
        });
        this.$el.html( html);
        return this;
    },

    clear: function() {
        this.$el.html( "");
    },

    destroy: function() {


        this.$el.empty().off();
        this.stopListening();
        return this;
    }
});



var categoryView = Backbone.View.extend({
    el: '#div_category',
    events: {
        'change select': 'change',
        'click #rubricid' : 'accordeonToggle',
        'click .optgroup' : 'openOptgroup',
        'click .back' : 'getBack',
        'click .option:not(.back)' : 'setValue'
    },

    initialize: function(options) {
        var me = this;
        $('body').on('click', function(){
            me.hideMenu();
        });
        _.extend(this, options);
        this.control = this.$el.find("#rubricid");
        if (!this.control.length)
            this.control = this.$el.find("#fn-category");
        this._init_data();
        this._init_description();
        this._init_price();

        var category_id = this.control.data('value');

        if (category_id && category_id != 0) {
             this.$el.find('.current_value').html($('#rubricid .option[data-value='+category_id+']').html());
        }
       
    },

    _init_data: function() {
        this.settings = {};
        this.category_id = this.control.data('value');
        if (this.category_id && this.category_id != 0) {
            this.data = data[this.category_id];
            if (this.data) {
                _.extend(this.settings, this.data[0]);
            }
        }
        this.app.descriptions = data["descriptions"];
    },

    accordeonToggle: function(e){
        e.stopPropagation();
        this.$container = $('#rubricid');
        this.$container.toggleClass('brb2, bb');
        this.$container.find('.select_wrap').toggle();
    },

    hideMenu: function(){
        this.$container = $('#rubricid');
        this.$container.addClass('brb2, bb');
        this.$container.find('.select_wrap').hide();
    },

    openOptgroup: function(e){
        e.stopPropagation();
        var self = $(e.currentTarget);
        self.addClass('active').children('.option').show();
        this.$container = $('#rubricid');
        this.$container.find('.option').first().addClass('back').html('<i class="fa fa-long-arrow-left mr5" aria-hidden="true"></i> Назад');
        this.$container.find('.option:not(.back, .optgroup .option)').slideUp(); //self.children('.option').show();
        $('.optgroup:not(.active)').slideUp();         
        self.find('.optgroup_value').addClass('active bold');

    },

    setValue: function(e){
        var self = $(e.currentTarget);
        if (self.data('value') == 0) {
            self.addClass('back');
        }

        this.$el.find('.current_value').html(self.html());

        $('.option .sign_icon').remove();

        self.append('<div class="sign_icon"><i class="fa fa-check" aria-hidden="true"></i></div>');
        this.getBack(e);
        this.accordeonToggle(e);

        this.$el.find('#rubricid').data('value', self.data('value'));

        $('input[name=rubricid]').val(self.data('value'));

        this.change(e);

    },

    getBack: function(e){
        e.stopPropagation();
        var self = $(e.currentTarget);
        $('.optgroup .option').not('.back').slideUp();
        $('.option').not('.optgroup .option').slideDown();
        $('.optgroup_value').each(function(){
            $(this).removeClass('active bold');
        });
        $('.optgroup').removeClass('active').slideDown();

        $('.option.back').removeClass('back').html('---');
    },

    change: function(e) {

        this.value = $(e.target).val();

        this._init_data();
        this.app.initialize({
            reinit_after_change: true
        });
        var wrapper = "div_category";
        if (this.value) {
            this.app.removeError(wrapper);
            this.app.removeRequired(wrapper);
        } else {
            this.app.addRequired(wrapper);
        }
        this._init_description();
        this._init_price();

       
    },

    _init_description: function() {
        if (this.app.descriptions)
            new categoryDescriptionView({
                text: this.app.descriptions[this.settings.description]
            });
    },

    _init_price: function() {
        $("#div_price").hide();
        if (this.settings.price_enabled)
            $("#div_price").show();
    }

});

var categoryDescriptionView = Backbone.View.extend({
    el: '#div_category_description',
    className: "row mb10",
    template: templates.description,

    initialize: function(options) {
        _.extend(this, options);
        this.render();
    },

    render: function() {

        if (this.text) {
            var html = _.template(this.template)({
                text: this.text
            });
            this.$el.html(html);
        } else {
            this.$el.html("");
        }
    }
});

var additionalView = Backbone.View.extend({
    el: '#div_additional',

    initialize: function(options) {
        var self = this;
        _.extend(this, options);
        if (!this.app.org_type)
            this.app.org_type = 1;
        this.additional_fields = this.additional_fields || [];
        this.additional_fields = this.additional_fields[this.app.org_type];
        this.render();
    },

    render: function() {
        var self = this;
        this.$el.find(".fn-additional").each(function(key, item) {
            _.indexOf(self.additional_fields, $(item).attr("id")) >= 0 ? $(item).show() : $(item).hide();
        });
    }
});




return Marionette.ItemView.extend({
    form_id: 'element_list',
    events: {
        'click #submit_button': 'submitForm'
    },

    behaviors: {
        ContactsBehavior: {
            behaviorClass: ContactsBehavior
        },
        AddServiceBehavior: {
            behaviorClass: AddServiceBehavior
        },
    },

    initialize: function(options) {
        _.extend(this, options);
        var self = this;
        self.is_edit = ($("#object_id").val());

        this.settings = ($("#moderate").text()) ? eval($("#moderate").text()) : {};
        if (this.reinit_after_change)
            this._destroy_controls();
            if ( _globalSettings.allowCkEditor) {

                window.CKEDITOR_BASEPATH = (app.settings.debug) ? '/static/develop/js/lib/ckeditor/' : '../static/develop/production/js/lib/ckeditor/';

                require([ "modules/ckeditor" ], function(ckeditor) {
                
                    CkEditor = ckeditor;
                    self._init_controls();
                
                });

           } else {
                self._init_controls();
           }
        
    },

    _init_controls: function() {
            if (!this.reinit_after_change) {
                this.category = new categoryView({
                    app: this
                });

                this.ad_type = new adTypeView({
                    app: this
                });

                this.city = new cityView({
                    category_id: this.category.category_id,
                    app: this
                });
                this.moderate_add_cities = new addCitiesView({
                    category_id: this.category.category_id,
                    app: this
                });
                this.photo = new photoControlView({
                    app: this
                });
                // this.contacts = new contactListView({app : this});
                // this.contacts.render();
            }

            this.triggerMethod("ChangedCategory", {category_id: this.category.category_id, city_id: +this.city.value});

            this.address_precision = null;
            this.precision_error = null;
            this.cmap = new mapView({
                category_id: this.category.category_id,
                app: this
            });
            this.params = new paramsView({
                category_id: this.category.category_id,
                data: this.category.data,
                address_precisions: this.category.settings.address_precisions,
                app: this
            });
            this.subject = new subjectView({
                el: "#div_subject",
                category_id: this.category.category_id,
                title_auto: this.category.settings.title_auto,
                app: this
            });
            this.text = new textView({
                el: "#div_textadv",
                category_id: this.category.category_id,
                text_required: this.category.settings.text_required,
                app: this
            });
            this.additional = new additionalView({
                el: "#div_additional",
                category_id: this.category.category_id,
                app: this,
                additional_fields: this.category.settings.additional_fields,
            });

        },

        _destroy_controls: function() {
            this.params.trigger("destroy");
            this.subject.trigger("destroy");
            // this.text.trigger("destroy");
            this.cmap.trigger("disable");
        },

        _init_map: function(field_address_id) {
            if (this.cmap)
                this.cmap.trigger("enable", {
                    city: this.city,
                    address_field: field_address_id
                });
        },

        removeError: function(el) {
            var wrapper = $("#" + el);
            wrapper.find(".fn-error").remove();
            wrapper.find(".inp-cont").removeClass("error");

        },

        removeRequired: function(el) {
            var wrapper = $("#" + el);
            wrapper.find(".required-label").html("");
        },

        addRequired: function(el) {
            var wrapper = $("#" + el);
            wrapper.find(".required-label").html("*");
        },

        submitForm: _.once(function() {

            if (this.text && nicEditors.findEditor('user_text_adv')) {
                console.log(123123123)
                nicEditors.findEditor('user_text_adv').saveContent();
            }
            $('#' + this.form_id).submit();
        })

    });
});
