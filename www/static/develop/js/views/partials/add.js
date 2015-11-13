/*global define */
define([
    "marionette",
    "templates/set/add",
    "views/partials/behaviors/contacts",
    "fileupload",
    "nicEdit",
    "maskedInput",
    "ymap",
    //use cropper
    //'lib/cropper.js'
    'cropper'
], function (Marionette, templates, ContactsBehavior) {
    "use strict";

    var photoList = Backbone.Collection.extend({

    });

    var paramList = Backbone.Collection.extend({
        comparator: function( collection ){
            return( collection.get('weight') );
        }
    });

    var paramView = Backbone.View.extend({
        tagName : 'div',
        containers : {
            "list" : '.fn-list-parameters',
            "row"  : '.fn-rows-parameters'
        },
        events : {
            'change' : 'change',
            'keyup' : 'keyup'
        },
        initialize : function (options) {
            _.extend(this, options);
            this.$el = $("#"+this.model.get("id"));

            if (!this.model.get("type")){
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

        render: function(){
            var self = this;

            var template_name = this.model.get("type");
            if (this.model.get("custom"))
                template_name = "custom"+this.model.get("custom");

            var html = _.template(templates[template_name])(this.model.toJSON());

            /* вставляем элемент на свою позицию */
            this.appendToOwnPosition(this.model, html);

            this.$el = $("#"+this.model.get("id"));
            /* элемент будет одиночным в строке если есть соответствующая настройка */
            this.whitespace();
        
            return html;
        },

        appendToOwnPosition : function(model, html){
            var container = $(this.containers[model.get("container")]);

            /* берем элементы уже находящиеся в контейнере */
            var collection  = new paramList(this.collection.where({"container":model.get("container")}));

            /* определяем позицию нового элемента в коллекции */
            var index = collection.indexOf(this.model);

            /* если коллекция не пуста */
            if (collection.length > 1) {
                /* если элемент не первый */
                if (index) {
                    /* ищем предыдущую по порядку модель */
                    var previousModel = collection.at(index-1);
                    /* размещаем элемент после него */
                    $(html).insertAfter( "#div_"+previousModel.get("id") );
                } else {
                    var previousModel = collection.at(1);
                    $(html).insertBefore( "#div_"+previousModel.get("id") );
                }
            } else {
                /* если коллекция пуста, то размещаем новый элемент первым в контейнере */
                container.append(html);
            }          
        },

        whitespace : function(){
            if (this.model.get("options") == 'whitespace')
                this.$el.closest(".col-md-6").addClass("whitespace");
        },

        change : function(e) {
            this.removeChilds(this.model.get("childs"));
            this.model.set("value", $(e.target).val());
            var childs = this.initChilds();
            this.model.set("childs", childs);
            var wrapper = "div_"+this.model.get("id");
            if (this.model.get("value")){
                this.app.removeError(wrapper);
                this.app.removeRequired(wrapper);
            } else {
                this.app.addRequired(wrapper);
            }
            this.paramsBlock.initAddressPrecision();
        },

        keyup : function(e) {
            this.model.set("value", $(e.target).val());
            var wrapper = "div_"+this.model.get("id");
            if (this.model.get("value")){
                this.app.removeError(wrapper);
                this.app.removeRequired(wrapper);
            } else {
                this.app.addRequired(wrapper);
            }
        },

        initChilds : function(){
            var self = this,
                d_attr = this.model.get("data"),
                d_attr_childs = [];

            if (this.model.get("type") != "list")
                return;
            
            if (_.isArray(this.model.get("value"))){
                _.each(this.model.get("value"), function(item){
                     d_attr_childs.push(d_attr[item]);   
                });
            } else {
                d_attr_childs.push(d_attr[this.model.get("value")]);    
            }

            var childs = [];

            _.each(d_attr_childs, function(dch){
                if (_.isObject(dch)){
                    _.each(dch, function(item, key){
                        if (key == 0) return;
                        var param = {
                            classes : "fn-param",
                            value : "",
                            data : item,                   
                            added : 1,
                            parent_id : self.model.get("id")
                        }
                        _.extend(param, item[0]);
                        self.collection.add(param);
                        childs.push(param.id);
                    });
                }
            });

            return childs;
        },

        removeChilds : function(childs){
            var self = this;
            _.each(childs, function(item){

               if (self.collection.get(item).get("childs"))
                    self.removeChilds(self.collection.get(item).get("childs"));
                
                self.collection.remove( self.collection.get(item) );
            });
        }

    });

    var paramsView = Backbone.View.extend({
        el : '#div_params',
        template : templates.parameters,
        initialize : function (options) {
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

            _.each(d_cat, function(item_data, key){  
                if (key == 0) return;          

                    var elem = $('#'+item_data[0].id);
                    var cl = elem.attr("class");
                    var val = elem.val();

                    var param = {
                        classes : (cl) ? cl : "fn-param",
                        value   : (val) ? val : "",
                        data    : item_data,                   
                        added   : (elem.length) ? 1 : 0
                    }
                    _.extend(param, item_data[0]);
                    self.collection.add(param);
            });

            this.initAddressPrecision();
        },

        render : function() {
            var params = { lists : 0 };        
            var html     =  _.template(this.template)({});
            this.$el.html(html);
            return this;
        },

        addItem: function(item){
            item.set("container", this.getContainer(item));
            if (item.get("type") == "text" && item.get("is_textarea"))
                item.set("type", "textarea");
            if (item.get("type") == "ilist")
                item.set("type", "list");
            new paramView({model : item, collection : this.collection, app : this.app, paramsBlock : this});

            if (item.get("custom") == "address")
                this.app._init_map( item.get("id") );
        },

        removeItem: function(item){
            if (item.get("custom") == "address")
                this.app.cmap.trigger("disable");        

            $("#div_"+item.get("id")).remove();
        },

        getContainer : function(model){
            var type = model.get("type");
            var custom = model.get("custom");
            if (_.contains(["list","ilist"], type) > 0 && custom != 'multiselect')
                return "list";
            else 
                return "row";
        },
        
        destroy: function(){
          this.$el.empty();
          this.stopListening();
          return this;
        },

        initAddressPrecision : function(){
            if (!this.address_precisions)
                return;
            var self = this,
                _filter = null;

            _.each(this.address_precisions, function(item){
                _filter = item.filters;
                
                delete _filter["rubricid"];

                if (_.keys(_filter).length){
                    _.each(_filter, function(filter, name){

                        if (self.collection.get(name) 
                                && _.indexOf(filter, +self.collection.get(name).get("value").replace("_","")) != -1){
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

    var cityView = Backbone.View.extend({
        el: '#div_city',
        events : {
            'change select' : 'change',
            'click #real_city_exists' : 'setRealCity',
            'keyup #real_city' : 'changeRealCity'
        },
        initialize: function(options){
            _.extend(this, options);
            this.control = this.$el.find("select");
            if (!this.control.length)  {
                this.control = this.$el.find("input");  
                this.value = this.control.val();
                this.title = this.control.data("title");
            } else {
                this.value = this.control.val();
                this.title = this.control.find('option:selected').text();
            }
            this.init_real_city();
        },
        init_real_city : function(){
            this.app.real_city_exists = $("#real_city_exists").prop("checked");
            this.app.real_city = this.app.real_city_exists ?
                                        $("#real_city").val() :
                                            "";
        },
        change : function(){
            this.value = this.control.val();
            this.title = this.control.find('option:selected').text();
            this.setLatLon();
            if (this.app.cmap.mapcontrol)
                this.app.cmap.mapcontrol.keyup();
            var wrapper = "div_city";
            if (this.value){
                this.app.removeError(wrapper);
                this.app.removeRequired(wrapper);
            } else {
                this.app.addRequired(wrapper);
            }
        },
        setLatLon : function(){
            var option = this.control.find('option:selected');
            this.control.attr("data-lon", option.attr("lon"));
            this.control.attr("data-lat", option.attr("lat"));
        },
        setRealCity : function(event){
            $(".real_city_exists").toggle();
            this.changeRealCity();
        },
        changeRealCity : function(e){
            this.init_real_city();
            if (this.app.cmap.mapcontrol){
                this.app.cmap.mapcontrol.setMessage("");
                this.app.cmap.mapcontrol.keyup(e);
            }
        }
    });

    var mapView = Backbone.View.extend({
        el: '#div_map',
        initialize: function(options){
            _.extend(this, options);
            this.bind("enable", this.on);
            this.bind("disable", this.off);
        },
        on : function(options){
            var self = this;
            _.extend(self, options);

            self.$el.removeClass("hidden");
           
            ymaps.ready(function(){
           
                var coords = $('#object_coordinates').val();
                var default_lat = $('#city_id').data("lat");
                var default_lon = $('#city_id').data("lon");
                var default_coords = [default_lat, default_lon];
                var default_zoom   = 10;
                var zoom = null;
                if (!default_lat){
                    default_coords = [57.140738,65.573836];//Тюмень
                    default_zoom = 7;
                }

                if (!coords){
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
                    style: _.extend(app.map.getIconSettings("house"), {draggable: true})
                })

                self.map.geoObjects.add(self.placemark);

                self.placemark.events.add('dragend', function (e) {
                    $('#object_coordinates').val(self.placemark.geometry.getCoordinates());
                });

                self.map.events.add('click', function (e) {
                    placemark.geometry.setCoordinates(e.get('coordPosition'));
                    $('#object_coordinates').val(self.placemark.geometry.getCoordinates());
                });

                self.mapcontrol = new mapcontrolView({app : self.app, city : self.city, address_field : self.address_field, map : self.map, placemark : self.placemark});
            });
        },
        off : function(){
            this.$el.addClass("hidden");
            if (this.map)
                this.map.destroy();
            this.map = null;
        }
    });

    var mapcontrolView = Backbone.View.extend({
        events: {
            'keyup' : 'keyup',
            'focusout': 'keyup',
            'paste': 'keyup'
        },

        precisions : { 'other' : 1, 'street' : 2 ,  'exact' : 3},

        initialize: function(options){
           
            _.extend(this, options);
            this.$el = $('#'+this.address_field);

            //if (this.app.is_edit)
                this.keyup();
        },

        keyup: function(){
            var self = this;
            var city = null;

            if (this.app.real_city_exists && this.app.real_city)
                city = this.app.real_city;
            else if (self.city.value)
                city = self.city.title;

            if (city){
                self.search = city+', '+this.$el.val();
                window.clearTimeout(self.timeout);
                self.timeout = setTimeout(function(){
                    self.kladr_autocomplete();
                    self.geoCoder();
                }, 500);
            } else {
                this.setMessage("Город не определен. Поле 'Город'  обязательно для заполнения", "red");
            }
        },

        setMessage : function(message, color) {
            var cmessage = $('#div_'+this.address_field).find(".inform");
            if (message){
                cmessage.html(message);
                cmessage.css("color", color);
                if (color == 'red')
                    $('#div_'+this.address_field).find(".inp-cont").addClass("error");
                else 
                    $('#div_'+this.address_field).find(".inp-cont").removeClass("error");
            } else {
                cmessage.html("Например: ул. Мельникайте, д. 44, корп. 2");
                $('#div_'+this.address_field).find(".inp-cont").removeClass("error");
            }
        },

        geoCoder : function() {
            var self = this;
            var zoom = 14;
            var myGeocoder = ymaps.geocode(self.search, { results: 1, json: true });
            myGeocoder.then(
                function(res) {
                    if (res.GeoObjectCollection.featureMember.length == 0) {
                        $('#object_coordinates').val('');
                        self.setMessage("Адрес не найден, видимо он не существует.", "red");
                    }
                    else {
                        self.setMessage();
                        var gobj = res.GeoObjectCollection.featureMember[0].GeoObject;
                        var points = gobj.Point.pos.split(" ");
                        self.placemark.geometry.setCoordinates([Number(points[1]), Number(points[0])]);
                        self.map.setCenter([Number(points[1]), Number(points[0])], zoom);
                        $('#object_coordinates').val(self.placemark.geometry.getCoordinates());
                       
                       var precision =  self.precisions[gobj.metaDataProperty.GeocoderMetaData.precision];

                        if (self.app.address_precision 
                                && self.precisions[self.app.address_precision] <= precision && self.app.precision_error)
                            self.setMessage("Адрес введен верно. "+self.app.precision_error, "gray");
                        else 
                            if (self.app.precision_error)
                                self.setMessage("Адрес не найден. "+self.app.precision_error, "red");
                    }

                },
                function(err) {
                    self.setMessage("Адрес не найден, видимо он не существует", "red");
                }
            );
        },

        kladr_autocomplete : function() {
            /*$('#'+this.address_field).autocomplete({
                source: function( request, response ) {
                    request.parent_id = $('#city_kladr_id').val();
                    request.address_required = 0;

                    $.getJSON( "/ajax/kladr_address_autocomplete", request, function( data, status, xhr ) {
                        response( data );
                    });
                },
                minLength: 1,
                autoFocus: true,
                select: function( event, ui ) {
                    $('#address_kladr_id').val(ui.item.id);
                    $('#map_block_div').show();

                    $('#error_address').hide();
                    $('#error_address').parents('div.input').removeClass('error');
                    setTimeout(function(){
                        GetCoordinates();
                    }, 100);
                },
                change: function( event, ui ) {
                    if (ui.item == null) {
                        // $('#city_kladr_id').val('');
                        // $('#address_selector').val('');
                        $('#address_kladr_id').val('');
                        GetCoordinates();
                        $('#map_block_div').show();
                    }
                }
            }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li>" )
                .append( "<a>" + item.label + "</a>" )
                .appendTo( ul );
            };  */     
        }

    });

    var subjectView = Backbone.View.extend({
        template : templates.subject,
        tagName : 'div',
        initialize: function(options){
            _.extend(this, options);
            this.bind("destroy", this.destroy);
            this.control = this.$el.find("input");
            this.maxlength = (this.app.settings.subject_max_length) ? this.app.settings.subject_max_length : 75;
            this.inform = this.$el.find(".inform");
            this.value = this.control.val();
            this.error = this.inform.html();
            if (!this.title_auto && !this.app.is_edit
                    || !this.title_auto && this.app.is_edit) // 
                        this.render();        
        },

        render: function(){
            var html     =  _.template(this.template)({ value : this.value, error : this.error, maxlength: this.maxlength });
            this.$el.html(html);
            return this;
        },
        
        destroy: function(){
          this.$el.empty();
          this.stopListening();
          return this;
        }
    });

    var textView = Backbone.View.extend({
        template : templates.textadv,
        tagName : 'div',
        initialize: function(options){
            _.extend(this, options);
            this.bind("destroy", this.destroy);
            this.control = this.$el.find("textarea");    
            this.value = this.control.val();  
            if (!this.control.length)  
                this.render();

            var staticPath = app.settings.staticPath;
            new nicEditor({iconsPath : staticPath + 'images/nicEditorIcons.gif'}).panelInstance('user_text_adv');
        },

        render: function(){

            var html     =  _.template(this.template)({ value : this.value, text_required : this.text_required });
            this.$el.html(html);
            return this;
        },
                        
        destroy: function(){

            
          this.$el.empty().off();
          this.stopListening();
          return this;
        }
    });

    var photoView  = Backbone.View.extend({
        tagName : "div",
        className : "img-b",
        template : templates.photo,
        events : {
            'click .fn-remove' : 'remove',
            'click .img' : 'setActive'
        },
        initialize : function(options){        
            _.extend(this, options);
            this.render();
        },

        render : function(){
            var html     =  _.template(this.template)(this.model.toJSON());
            this.container.append(this.$el.html(html));
            return this;
        },

        remove : function(){
            this.collection.remove(this.model);
        }, 

        setActive : function(){
            this.model.set("active", true);
        }
    });

    /* cropper view */
    // extend photo item view
    var photoViewBase = photoView;
    photoView = photoView.extend({
        //extend events
        events: _.extend(photoViewBase.prototype.events, {
            'click .fn-crop': 'showCropper'
        })
        //override initialize method
        , initialize: function (options) {
            //console.log('photoView[Cropper extension] initialize');

            photoViewBase.prototype.initialize.call(this, options);
        }
        //initialize cropper view
        , showCropper: function () {
            //console.log('photoView[Cropper extension] showCropper');
            //console.log(this.model);
            var me = this;
            CropperViewFactory(this.model.get('original'))
                .on('cropper_save', function (e) {
                    me.saveCroppedPicture(this.cropBoxData);
                })
                .render();
        }
        //picture change processor
        , saveCroppedPicture: function (data) {
            var me = this;
            $.ajax({
                url: 'add/crop'
                , dataType: 'json'
                , method: 'GET'
                , data: _.extend(data, {
                    fileName: this.model.get('filename')
                })
                , success: function (answer) {
                    //var avoidCache = '?v=' + Math.random();
                    me.model.set('filename', answer.fileName);
                    me.model.set('filepath', answer.thumbnails['120x90']);
                    me.model.set('original', answer.thumbnails['original']);
                    me.$el.find('img').attr('src', answer.thumbnails['120x90']);
                    me.$el.find('input[type=hidden]').val(answer.fileName);
                }
            });
        }
    });
    //check prototype
    //console.log(photoView.prototype);
    /* cropper view */
    var CurrentCropperView = null; //singleton
    var CropperView = Backbone.View.extend({
        //cropper data
        cropBoxData: null
        , cropCanvasData: null
        , cropperOptions: {}
        //bind events
        , events: {
            'click [data-save]': 'save'
            , 'click .js-close': 'destroy'
            , 'click [data-rotate]': 'rotate'
            , 'click [data-zoom]': 'zoom'
            , 'click [data-refresh]': 'refresh'
        }

        , initialize: function (options) {
            this.$image = this.$el.find('img');
        }

        , setAspectRatio: function () {
            if (!this.oldAspectRatio) {
                this.oldAspectRatio = 3 / 4;
            }
            if (this.oldAspectRatio < 1) {
                this.oldAspectRatio = 4 / 3;
            } else {
                this.oldAspectRatio = 3 / 4;
            }

            this.$image.cropper('setAspectRatio', this.oldAspectRatio);
        }

        , rotate: function (event) {
            event.preventDefault();
            //this.$image.cropper({ center: false, autoCropArea: 0 })
            var cropBoxData = this.$image.cropper('getCropBoxData');
            //console.log(cropBoxData);
            this.setAspectRatio();

            var degrees = +$(event.currentTarget).data('rotate');
            this.$image.cropper('rotate', degrees);

            //calc move offset
            if (cropBoxData.width > cropBoxData.height) {
                cropBoxData.top -= (cropBoxData.width - cropBoxData.height) * 0.5;
            } else {
                cropBoxData.left -= (cropBoxData.height - cropBoxData.width) * 0.5;
            }

            //rotate crop box
            var temp = cropBoxData.width;
            cropBoxData.width = cropBoxData.height;
            cropBoxData.height = temp;
            this.$image.cropper('setCropBoxData', cropBoxData);
        }

        , refresh: function (event) {
            event.preventDefault();
            this.$image.cropper('destroy');
            this.initCropper();
        }

        , zoom: function (event) {
            event.preventDefault();
            var level = +$(event.currentTarget).data('zoom');
            this.$image.cropper('zoom', level);
        }

        , render: function () {
            if (CurrentCropperView != this) {
                if (CurrentCropperView != null) {
                    CurrentCropperView.destroy();
                }
                CurrentCropperView = this;
            }
            //append dom
            $('.cropper-cont').append(this.$el);
            this.initCropper();
        }

        , initCropper: function () {
            var me = this;
            //init cropper
            this.$image.cropper(_.extend(this.cropperOptions, {
                //some defaults - TODO
                aspectRatio: this.oldAspectRatio = 4/3,
                //center: true,
                //autoCropArea: 1
                built: function () {
                    var imageData = me.$image.cropper('getImageData');
                    var toSet = {
                        x: 0
                        , y: 0
                        , width: 0
                        , height: 0
                        , rotate: 0
                        , scaleX: 1
                        , scaleY: 1
                    };
                    
                    if (imageData.naturalWidth < imageData.naturalHeight) {
                        me.setAspectRatio();
                        toSet.width = imageData.naturalWidth;
                        toSet.height = imageData.naturalWidth / me.oldAspectRatio;
                    } else {
                        toSet.height = imageData.naturalHeight;
                        toSet.width = imageData.naturalHeight * me.oldAspectRatio;
                    }

                    toSet.x = (imageData.naturalWidth - toSet.width) * 0.5;
                    toSet.y = (imageData.naturalHeight - toSet.height) * 0.5;

                    me.$image.cropper('setData', toSet);
                }
            }));
            //set initial data
            this.updateData();
        }

        , updateData: function () {
            this.cropBoxData = this.$image.cropper('getData');
            this.cropCanvasData = this.$image.cropper('getCanvasData');
        }

        , save: function () {
            //save data
            this.updateData();
            //trigger done event
            this.trigger('cropper_save');
            this.destroy();
        }

        , destroy: function () {
            //destroy cropper
            this.$image.cropper('destroy');
            //remove dom
            this.$el.remove();
        }
    });
    /* cropper view done */
    //factory for cropper view
    //simple creates bootstrap modal dialog
    //TODO - export factory to usage in other modules
    var CropperViewFactory = function (image) {
        //markup
        /* bootstrap version */
        /*
        var html = 
            '<div class="modal fade">'
                + '<div class="modal-dialog">'
                    + '<div class="modal-content">'
                        + '<div class="modal-body">'
                            + '<img src="" />'
                        + '</div>'
                    + '</div>'
                + '</div>'
            + '</div>';
        */
        /* other version */
        var html = 
            /*'<div class="popup-wrp z400 cropper-popup">'
                + '<div class="popup-window mw500">'
                    + '<div class="header">'
                        + 'Редактирование изображения'
                        + '<div class="popup-window-close js-close">'
                            + '<i class="ico close-ico16"></i>'
                        + '</div>'
                    + '</div>'
            */
                    '<div>'
                        + '<div class="cropper-image">'
                            + '<img src="" />'
                        + '</div>'
                        + '<div class="cropper-actions">'
                            + '<button class="btn" data-rotate="90"><span class="fa fa-undo"></span></button>'
                            //+ '<button class="btn" data-rotate="-10"><span class="fa fa-repeat"></span></button>'
                            + '<button class="btn" data-zoom="0.1"><span class="fa fa-search-plus"></span></button>'
                            + '<button class="btn" data-zoom="-0.1"><span class="fa fa-search-minus"></span></button>'
                            + '<button class="btn" data-refresh>Отменить</button>'
                            + '<button class="btn" data-save>Сохранить</button>'
                        + '</div>'
                    + '</div>'
            /*
                + '</div>'
            + '</div>';
            */

        //compile
        var $compiled = $(html);

        //push values
        $compiled.find('img').attr('src', image);

        //create view
        var view = new CropperView({
            el: $compiled
        });

        return view;
    };
    /* cropper feacture done */

    var photoControlView = Backbone.View.extend({
        el : '#div_photo', 
        photos : [],
        maxLength : 10,
        hints: {
            main: "Главным по умолчанию является первое фото, щелкните по любому фото, чтобы сделать его главным<br>До 10 фотографий с расширениями jpg, png, gif, не более 5мб",
            requires: "До 10 фотографий с расширениями jpg, png, gif, не более 5мб. Фото можно перетащить в эту зону мышкой."
        },
        initialize : function(options){
            var self = this;
            _.extend(this, options);    

            this._init_ajax_upload();

            this.collection = new photoList();
            this.collection.comparator = function(model) {
                return model.get('id');
            }
            this.collection.on("add", this.addItem, this);
            this.collection.on("remove", this.removeItem, this);
            this.collection.on("change:active", this.changeActive, this);

            _.each(this.$el.find(".img-b"), function(item){
                var params = {
                    id : $(item).attr("id"),
                    filename : $(item).find("input").val(),
                    filepath :  $(item).find("img").attr("src"),
                    active : $(item).find(".img").hasClass("active")
                };
                self.collection.add(params);
            });
            this.renderHint();
        },

        renderHint: function() {
            if (this.collection.length) {
                $(".fn-photo-hint").html(this.hints.main);
            } else {
                $(".fn-photo-hint").html(this.hints.requires);
            }
        },

        _init_ajax_upload : function(){
            var self = this;
            // new AjaxUpload('userfile_upload', {
            //     action: '/add/object_upload_file',
            //     name: 'userfile1',
            //     data : {context :self},
            //     autoSubmit: true,
            //     onSubmit: self.onSubmit,
            //     onComplete: self.onComplete
            // });
            $('#fileupload').fileupload({
                uidropzone: $(".fn-photo-list"),
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                maxFileSize: 6000,
                autoUpload: false,
                // disableImageResize: /Android(?!.*Chrome)|Opera/
                //             .test(window.navigator.userAgent),
                dataType: 'json',
                success: function(result, data){
                    if (self.collection.length >= self.maxLength) {
                        self.setError("Можно загрузить не более 10 фотографий");
                        return;
                    }
                   if (result.filename) {

                        var active = false;
                        if (self.collection.length == 0){
                            $("#active_userfile").val(data.filename);
                            active = true;
                        }

                        self.collection.add({
                            filename : result.filename,
                            filepath : result.filepaths['120x90'],
                            active : active
                            //fix to use crop feature
                            , original: result.filepaths.original
                        });
                        self.setError("");
                   } else
                   if (result.error) {
                        self.renderHint();
                        self.setError(result.error);
                   } else {
                        self.setError('Произошла непредвиденная ошибка');
                   }
                },
                // error: function(e, data){
                //     alert("Ошибка при загрузке фото");
                // }
            }).on("fileuploadadd", function(e, data){
                var jqXHR = data.submit();
                if (self.collection.length >= self.maxLength) {
                    self.setError("Можно загрузить не более 10 фотографий");
                    jqXHR.abort();
                }
                this.jqXHR = jqXHR;
            }).on("fileuploaddone", function(e, data){
                if (self.collection.length >= self.maxLength) {
                    self.setError("Можно загрузить не более 10 фотографий");
                    if (!this.jqXHR) {
                        this.jqXHR.errorThrown = "Можно загрузить не более 10 фотографий";
                        this._trigger('fail', e, data);
                      } else {
                        this.jqXHR.abort();
                      }
                }
            }).on("fileuploadfail", function(e, data){
                self.renderHint();
            }).on('fileuploadprogressall', function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                if (progress == 100) {
                    $('.fn-photo-hint').html("Файл загружен. Идет сохранение...");
                } else {
                    $('.fn-photo-hint').html("Идет загрузка... " + progress + '%');
                }
            });
        },

        addItem : function(item){
            var container = this.$el.find(".fn-photo-list");
            var el = undefined;
            if (item.get("id"))
                el = $('#'+item.get("id"));
            this.photos[item.cid] = new photoView({el : el, model : item, collection : this.collection, container : container});
            if (this.collection.length > 0) {
                $(".fn-photo-hint").text();
            }
            this.renderHint();
        },

        removeItem : function(item){
            this.photos[item.cid].unbind();
            this.photos[item.cid].$el.remove();
            if(this.collection.length && item.get("active")){
                this.collection.at(0).set("active", true);
            }
            this.setError("");
            this.renderHint();
        },

        changeActive : function(main_item){
            var photo_cont = this.photos[main_item.cid].$el.find(".img");
            if (main_item.get("active")){            
                photo_cont.addClass("active");
                this.collection.each(function(item){
                    if (item.cid != main_item.cid)
                        item.set("active", false);
                });
                $("#active_userfile").val(main_item.get("filename"));
            } else {
                photo_cont.removeClass("active");
            }
        },

        onSubmit: function(file, doc){
            var self = this._settings.data.context;
            self.setError("");      
        },

        onComplete: function(file, data){
            var self = this._settings.data.context;

            if (data) 
                data = $.parseJSON(data);

            if( data === null || !data.filename) {
                self.setError('Произошла непредвиденная ошибка');
                return;
            } else if(data.error) {
                self.setError(data.error);
                return;
            }    
            var active = false;
            if (self.collection.length == 0){
                $("#active_userfile").val(data.filename);
                active = true;
            }

            self.collection.add({
                filename : data.filename,
                filepath : data.filepaths['120x90'],
                active : active
            });         
                        
        },

        setError : function(text){
            $("#error_userfile1").html(text);
        }

    });

    var categoryView = Backbone.View.extend({
        el : '#div_category',
        events : {
            'change select' : 'change'
        },

        initialize : function (options) {
            _.extend(this, options); 
            this.control = this.$el.find("select"); 
            if (!this.control.length)   
                this.control = this.$el.find("#fn-category");  
            this._init_data(); 
            this._init_description();  
            this._init_price();
        },

        _init_data : function() {
            this.settings = {};
            this.category_id = this.control.val();
            if (this.category_id && this.category_id != 0){
                this.data = data[this.category_id];
                if (this.data) {
                    _.extend(this.settings, this.data[0]);
                }
            }
            this.app.descriptions = data["descriptions"];
        },

        change : function(e) {
            this.value = $(e.target).val();
            this._init_data();
            this.app.initialize({reinit_after_change : true});
            var wrapper = "div_category";
            if (this.value){
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
                new categoryDescriptionView({text : this.app.descriptions[this.settings.description]});
        },

        _init_price: function() {
            $("#div_price").hide();
            if (this.settings.price_enabled)
                $("#div_price").show();
        }

    });

    var categoryDescriptionView = Backbone.View.extend({
        el : '#div_category_description',
        className: "row mb10",
        template : templates.description,

        initialize : function (options) {
            _.extend(this, options);
            this.render();     
        },

        render : function(){

            if (this.text){
                var html     =  _.template(this.template)({text: this.text});
                this.$el.html(html);
            } else {
                this.$el.html("");
            }
        }
    });

    var additionalView = Backbone.View.extend({
        el : '#div_additional',

        initialize : function (options) {
            var self = this;
            _.extend(this, options);
            if (!this.app.org_type)
                this.app.org_type = 1;
            this.additional_fields = this.additional_fields || [];
            this.additional_fields = this.additional_fields[this.app.org_type];
            this.render();
        },

        render : function(){
            var self = this;
            this.$el.find(".fn-additional").each(function(key, item){
                _.indexOf(self.additional_fields, $(item).attr("id")) >= 0 ? $(item).show() : $(item).hide();
            });
        }
    });

    var verifyWindowView = Backbone.View.extend({
        tagName : "div",
        className : "popup enter-popup fn-verify-contact-win",
        template : templates.verifyContactWindow,
        events : {
            'click .fn-verify-contact-win-close' : 'close',
            'click .fn-verify-contact-win-submit' : 'doVerifyCode'
        },
        initialize : function (options){
            _.extend(this, options);
            this.render();

            if (this.model.get("type") == "1" ||
                    this.model.get("type") == "5")
                this.sendSms();
            else 
                this.showVerificationCode();
        },
        render : function(){
            var html     =  _.template(this.template)(this.model.toJSON());
            $('body').append(this.$el.html(html));
            $('body').find('.popup-layer').fadeIn();
            this.$el.fadeIn();
            return this;
        },
        close : function(){        
            this.unbind();
            this.remove(); 
            $('body').find('.popup-layer').fadeOut();
        },
        doVerifyCode : function(){
            if (this.model.get("type") == "1" ||
                    this.model.get("type") == "5")
                this.checkCode();
            else 
                this.checkHomePhoneCode();

        },
        sendSms : function(force){
            var self = this;
            var params = {   
                    contact_type_id : this.model.get("type"), 
                    force : force
                };
            if (this.model.get("type") == "5" )
                params.email = this.model.get("value");
            else
                params.phone = this.model.get("value");
            $.post('/ajax/sent_verification_code', params, 
                function(json){
                    self.responseSms(json, self);
                }, 
            'json');
        },
        responseSms : function(json, context){
            var self = context;
            self.contact_id = json.contact_id;
            switch (+json.code){
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
                case 401 :
                    self.setError('Это не мобильный телефон, выберите "городской"');
                break;
            }
        },
        checkCode : function(){
            var self = this;
            var code = this.$el.find(".fn-input-code").val();

            if (!self.contact_id)
                return;
            
            $.post('/ajax/check_contact_code/'+self.contact_id, {code:code}, function(json) {
                if (json.code == 200) {
                    self.model.set("status", 'verified');
                    self.close();
                } else {
                    self.setError("Неправильный код");
                }
            }, 'json');
        },
        checkHomePhoneCode : function(){
            var self = this;
            var code = this.$el.find(".fn-input-code").val();
            if (code == this.verificationCode) {
             $.post('/ajax/verify_home_phone', {contact : self.model.get("value")}, 
                function(json){
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
        showVerificationCode : function() {
            this.verificationCode = this.generateVerificationCode(); 
            this.setError('Этот номер телефона пройдет проверку. Подтвердите что вы согласны, введите код:'+this.verificationCode);
        },
        generateVerificationCode : function() {
            var verificationCode = _.random(1000, 9999);
            return verificationCode;
        },
        setError : function(text){
            this.$el.find(".fn-error-block").html(text);
        }

    });

    var contactModel = Backbone.Model.extend({
        defaults : {
            type : 1,
            value : "",
            status : "clear"
        }
    });

    var contactList = Backbone.Collection.extend({
        model : contactModel
    });

    var contactView = Marionette.ItemView.extend({
        tagName : "div",
        className : "contact-cont fn-contact",
        template : templates.contact,
        ui: {
            delete: ".fn-contact-delete-button",
            verify: ".fn-contact-verify-button",
            type: ".fn-contact-type",
            value: ".fn-contact-value"
        },
        events : {
            'click @ui.delete' : 'remove',
            'click @ui.verify' : 'doVerify',
            'change @ui.type' : 'changeType'
        },
        initialize : function (options){
            _.extend(this, options);
        },
        _init_inputs : function(){
            this.contact = this.$el.find(".fn-contact-value");
            this.type = this.$el.find(".fn-contact-type");
            this.format = this.type.find("option:selected").data("format");
            this.validation_type = this.type.find("option:selected").data("validation-type");

            this.setValues();
        },
        _init_mask : function(){
            var self = this;
            if (this.model.get("type") != 5)
            {
                $(this.contact).mask(this.format , {  
                    "completed": function(){
                        self.model.set("status", "valided");
                        self.setValues();
                    }
                });
            } else {
                //$(this.contact).val("");
                $(this.contact).unbind();       
            }
        },
        setValues : function(){
            this.model.set("value", this.contact.val());
            this.model.set("type", this.type.val());
        },
        onRender : function(){
            // var html     =  _.template(this.template, this.model.toJSON());
            // this.container.append(this.$el.html(html));
            this._init_inputs();
            this._init_mask();
            // return this;
        },
        remove : function(){
            console.log(this.model)
            this.model.collection.remove(this.model);
        },
        changeType : function(){
            this._init_inputs();
            this._init_mask();
        },
        doVerify : function(){
            this.setValues();
            this.window = new verifyWindowView({model: this.model});
        },

        changeStatus : function(){
            this.$el.removeClass("verified");
            this.$el.removeClass("noverified");
            this.$el.find(".fn-contact-inform").html(""); 

            if (this.model.get("status") == "clear"){
                
            } else
            if (this.model.get("status") == "valided"){
                
            } else
            if (this.model.get("status") == "verified"){
                this.$el.addClass("verified");
                this.$el.find(".fn-contact-inform").html("Контакт подтвержден");
            }  else
            if (this.model.get("status") == "noverified"){
                this.$el.addClass("noverified");
            }  
        },

    });

    var contactListView = Marionette.CollectionView.extend({
        el : "#div_contacts",
        contacts : [],
        childView: contactView,
        buildChildView: function(child, ChildViewClass, childViewOptions){
            var options = _.extend({model: child}, childViewOptions);
            if ($('#contact_'+child.id).length) {
                options.$el = $('#contact_'+child.id);
            }
            var view = new ChildViewClass(options);
            return view;
        },
        ui: {
            "addContact": ".fn-add-contact-button-text",
            "contacts": ".contact-cont"
        },
        events : {
            'click @ui.addContact' : 'addContact'
        },

        initialize : function (options) {
            _.extend(this, options);
            var self = this;
            this.bindUIElements();

            this.collection = new contactList();
            // this.collection.on("add", this.addItem, this);
            // this.collection.on("remove", this.removeItem, this);
            this.collection.on("change:status", this.changeStatus, this);

             _.each(this.ui.contacts, function(item){
                var status = "clear",
                    $item = $(item);
                if ($item.hasClass("verified"))
                    status = "verified";
                else if ($item.hasClass("noverified"))
                    status = "noverified";

                var params = {
                    id : $item.data("item-id"),
                    value : $item.find(".fn-contact-value").val(),
                    type :  $item.find(".fn-contact-type").val(),
                    status : status
                };
                self.collection.add(params);
            });
        },

        addContact : function(){
            this.collection.add({id:this.collection.length+1});
        },

        changeStatus : function(item){
            this.contacts[item.cid].changeStatus();
        }

    });


    return Marionette.ItemView.extend({
        form_id : 'element_list',
        events : {
            'click #submit_button' : 'submitForm'
        },

        behaviors: {
            ContactsBehavior: {
                behaviorClass: ContactsBehavior
            },
        },

        initialize : function (options) {
            _.extend(this, options);
            var self = this;
            self.is_edit = ($("#object_id").val());
            
            this.settings = ($("#moderate").text()) ? eval($("#moderate").text()) : {};
            if (this.reinit_after_change) 
                this._destroy_controls();

            self._init_controls();
        },

        _init_controls : function(){
            if (!this.reinit_after_change) {
                this.category = new categoryView({app : this});
                this.city     = new cityView({      
                                                category_id : this.category.category_id, 
                                                app : this
                                            });
                this.photo    = new photoControlView({app : this});
                // this.contacts = new contactListView({app : this});
                // this.contacts.render();
            }
            this.address_precision = null;
            this.precision_error = null;
            this.cmap     = new mapView({      
                                            category_id : this.category.category_id, 
                                            app : this
                                        });
            this.params   = new paramsView({
                                            category_id : this.category.category_id, 
                                            data : this.category.data,
                                            address_precisions : this.category.settings.address_precisions,
                                            app : this
                                        });    
            this.subject  = new subjectView({
                                            el : "#div_subject",
                                            category_id : this.category.category_id, 
                                            title_auto : this.category.settings.title_auto,
                                            app : this
                                        });
            this.text     = new textView({  el : "#div_textadv",
                                            category_id : this.category.category_id, 
                                            text_required : this.category.settings.text_required,
                                            app : this
                                        });
            this.additional   = new additionalView({  el : "#div_additional",
                                            category_id : this.category.category_id, 
                                            app : this,
                                            additional_fields : this.category.settings.additional_fields,
                                        });

        },

        _destroy_controls : function(){
            this.params.trigger("destroy");        
            this.subject.trigger("destroy");
            this.text.trigger("destroy");  
            this.cmap.trigger("disable");      
        },

        _init_map : function(field_address_id){
            if (this.cmap)
                this.cmap.trigger("enable", {city : this.city, address_field : field_address_id});
        },

        removeError : function(el){
            var wrapper = $("#"+el);
            wrapper.find(".fn-error").remove();
            wrapper.find(".inp-cont").removeClass("error");

        },

        removeRequired : function(el){
            var wrapper = $("#"+el);
            wrapper.find(".required-label").html("");
        },

        addRequired : function(el){
            var wrapper = $("#"+el);
            wrapper.find(".required-label").html("*");
        },

        submitForm : _.once(function(){
            if (this.text)
            {
                nicEditors.findEditor('user_text_adv').saveContent();
            }
            $('#'+this.form_id).submit();
        })

    });
});