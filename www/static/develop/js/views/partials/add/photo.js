define([

    "marionette",
    "templates/set/add",
    'cropper',
    'fileupload'

    ], function(
        Marionette,
        templates,
        cropper
    ) {
    
    "use strict";

    var photoList = Backbone.Collection.extend({

    });


    var photoView = Backbone.View.extend({
        tagName: "div",
        className: "img-b",
        template: templates.photo,
        events: {
            'click .fn-remove': 'remove',
            'click .fn-main': 'makeMain',
            'click span.rotate': 'rotate',
            'click .img': 'openCurtain'
        },
        initialize: function(options) {
            _.extend(this, options);
            this.render();
            this.$image = this.$el.find('img');
        },

        rotate: function(ev, deg) {
            var me = this;
            var img = $('<img />')[0];
            img.onload = function() {
                me.saveCroppedPicture({
                    x: 0,
                    y: 0,
                    width: img.naturalHeight,
                    height: img.naturalWidth,
                    rotate: 90,
                    scaleX: 1,
                    scaleY: 1,
                    disableRectValidate: true
                });
            };
            img.src = me.model.get('original');
        },

        makeMain: function(){
            $('#add-block').prepend(this.$el);
            this.model.set('active', true);
        },

        openCurtain: function(){
            if (!$('html').hasClass('desktop')) {
                $('.img-b .curtain').css('opacity', '0');
                this.$el.find('.curtain').css('opacity', '1');
            };
            
        },

        render: function() {
            var ctx = this;
            var html = _.template(this.template)(this.model.toJSON());
            this.container.append(this.$el.html(html));
            $('#add-block').sortable({
                revert: 300,
                start: function(event, ui) {
                    clearInterval(this.interval);
                },
                stop: function(event, ui) {
                    var self = this;
                    var i = 0;
                    var fileNames = [i];
                    var img = $(self).children('.img-b');
                    var i = $(img).each(function() {
                        var userfile = $(this).children('input').val();
                        fileNames[i] = userfile;
                        i++;
                    });
                    var i = 1;
                    this.interval = setInterval(function() {
                        increment();
                    }, 1000);

                    function increment() {
                        i++;
                        if (i >= 3) {
                            clearInterval(self.interval);
                            data_save();
                        };
                    }

                    function data_save() {
                        $.ajax({
                            url: '/add/set_order',
                            dataType: 'json',
                            method: 'GET',
                            data: {
                                fileName: fileNames
                            },
                            success: function(answer) {

                            }
                        });
                    }
                }

            });
        return this;
        },

        remove: function() {
            this.collection.remove(this.model);
        },
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
                ,
                initialize: function(options) {
                //console.log('photoView[Cropper extension] initialize');

                photoViewBase.prototype.initialize.call(this, options);
            },
            remove: function() {
                if (this.activeCropperView) {
                    this.activeCropperView.destroy();
                }
                photoViewBase.prototype.remove.call(this);
            }
                //initialize cropper view
                ,
                showCropper: function() {
                    //console.log('photoView[Cropper extension] showCropper');
                    //console.log(this.model);
                    var me = this;
                    me.activeCropperView = CropperViewFactory(this.model.get('original'), {})
                    .on('cropper_save', function(e) {
                        me.saveCroppedPicture(this.cropBoxData);
                    });
                }
                //picture change processor
                ,
                saveCroppedPicture: function(data) {
                    var me = this;
                    $.ajax({
                        url: '/add/crop',
                        dataType: 'json',
                        method: 'GET',
                        data: _.extend(data, {
                            fileName: this.model.get('filename')
                        }),
                        success: function(answer) {        
                        var avoidCache = '?v=' + Math.random();
                        me.model.set('filename', answer.fileName);
                        me.model.set('filepath', answer.thumbnails['120x90']);
                        me.model.set('original', answer.thumbnails['original']);
                        me.$el.find('img').attr('src', answer.thumbnails['120x90']);
                        me.$el.find('input[type=hidden]').val(answer.fileName);

                        if (me.$el.find('.img').hasClass('active')) {
                            $('#active_userfile').val(me.model.get('filename'));
                            // me.model.set('active', true);
                            console.log(me.model.attributes);
                        }; 
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
            cropBoxData: null,
            cropCanvasData: null,
            cropperOptions: {}
            //bind events
            ,
            events: {
                'click [data-save]': 'save',
                'click .js-close': 'destroy',
                'click [data-rotate]': 'rotate',
                'click [data-zoom]': 'zoom',
                'click [data-refresh]': 'refresh',
                'click [data-destroy]': 'destroy'
            }

            ,
            initialize: function(options) {
                this.$image = this.$el.find('img');

                //console.log('Initialize cropper view with options: ');
                //console.log(options);

                if (options.width) {
                    this.$image.width(options.width);
                }
            }

            ,
            setAspectRatio: function() {
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

            ,
            rotate: function(event) {
                event.preventDefault();
                    //this.$image.cropper({ center: false, autoCropArea: 0 })
                    var cropBoxData = this.$image.cropper('getCropBoxData');
                    //console.log(cropBoxData);
                    this.setAspectRatio();

                    var degrees = +$(event.currentTarget).data('rotate');
                    this.$image.cropper('rotate', degrees);

                    //set container dimensions
                    var moveY = this.$image.next().height() - this.$image.next().find('.cropper-canvas').height();
                    this.$image.next().css({
                        height: this.$image.next().find('.cropper-canvas').height() + 'px'
                    });
                    this.$image.data('cropper').container.height = this.$image.next().find('.cropper-canvas').height();
                    this.$image.data('cropper').limitCropBox(true, true);
                    this.$image.cropper('move', 0, -moveY / 2);
                    console.log(cropBoxData.top);
                    cropBoxData.top -= moveY / 2;

                    //rotate crop box
                    var temp = cropBoxData.width;
                    cropBoxData.width = cropBoxData.height;
                    cropBoxData.height = temp;

                    //calc move offset
                    cropBoxData.top += (cropBoxData.width - cropBoxData.height) * 0.5;
                    cropBoxData.left -= (cropBoxData.width - cropBoxData.height) * 0.5;

                    console.log(cropBoxData);
                    this.$image.cropper('setCropBoxData', cropBoxData);
                    //this.appendRotate(degrees);
                    console.log(this.$image.cropper('getCropBoxData'));
                }
                /*
                , rotateDegrees: 0
                , appendRotate: function (degrees) {
                    this.rotateDegrees = (this.rotateDegrees + degrees) % 360;
                    this.$image.next().css({
                        transform: 'rotate(' + this.rotateDegrees + 'deg)'
                    });
                }
                */
                ,
                refresh: function(event) {
                    event.preventDefault();
                    this.$image.cropper('destroy');
                    this.initCropper();
                }

                ,
                zoom: function(event) {
                    event.preventDefault();
                    var level = +$(event.currentTarget).data('zoom');
                    this.$image.cropper('zoom', level);
                }

                ,
                render: function() {
                    if (CurrentCropperView != this) {
                        if (CurrentCropperView != null) {
                            CurrentCropperView.destroy();
                        }
                        CurrentCropperView = this;
                    }

                //scroll to me
                $('.cropper-cont').get(0).scrollIntoView();

                //append dom
                $('.cropper-cont').append(this.$el);
                this.initCropper();
            }

            ,
            initCropper: function() {
                var me = this;
                //init cropper
                setTimeout(function() {
                    console.log(me.$image.height());
                    me.$image.cropper(_.extend(me.cropperOptions, {
                        //some defaults - TODO
                        aspectRatio: me.oldAspectRatio = 4 / 3,
                        strict: false,
                        //center: true,
                        //autoCropArea: 1
                        built: function() {
                            var imageData = me.$image.cropper('getImageData');
                            var toSet = {
                                x: 0,
                                y: 0,
                                width: 0,
                                height: 0,
                                rotate: 0,
                                scaleX: 1,
                                scaleY: 1
                            };

                            //console.log(imageData);

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

                            //console.log(toSet);

                            me.$image.cropper('setData', toSet);
                            //set initial data
                            me.updateData();
                        }
                    }));
                }, 1);
            }

            ,
            updateData: function() {
                this.cropBoxData = this.$image.cropper('getData');
                this.cropCanvasData = this.$image.cropper('getCanvasData');
            }

            ,
            save: function() {
                //save data
                this.updateData();
                //trigger done event
                this.trigger('cropper_save');
                this.destroy();
            }

            ,
            destroy: function() {
                //destroy cropper
                this.$image.cropper('destroy');
                //remove dom
                this.$el.remove();
                $('#div_photo').get(0).scrollIntoView();
            }
        });
    /* cropper view done */
        //factory for cropper view
        //simple creates bootstrap modal dialog
        //TODO - export factory to usage in other modules
        var CropperViewFactory = function(image, options) {
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
                        '<div>' + '<div class="cropper-image">' + '<img src="" />' + '</div>' + '<div class="cropper-actions">' + '<button class="btn" data-rotate="90"><span class="fa fa-undo"></span></button>'
                //+ '<button class="btn" data-rotate="-10"><span class="fa fa-repeat"></span></button>'
                + '<button class="btn" data-zoom="0.1"><span class="fa fa-search-plus"></span></button>' + '<button class="btn" data-zoom="-0.1"><span class="fa fa-search-minus"></span></button>' + '<button class="btn" data-refresh>Сбросить</button>' + '<button class="btn" data-destroy>Отменить</button>' + '<button class="btn" data-save>Сохранить</button>' + '</div>' + '</div>'
                /*
                    + '</div>'
                + '</div>';
                */

            //compile
            var $compiled = $(html);

            //create view
            var view = new CropperView(_.extend({
                width: $('.fn-cropper-cont').width()
            },
            options, {
                el: $compiled
            }));

            //continue only when image will be loaded
            $compiled.find('img')[0].onload = function(e) {
                view.render();
            };
            //push values
            $compiled.find('img').attr('src', image);

            return view;
        };
        /* cropper feacture done */

        return Backbone.View.extend({
            el: '#div_photo',
            photos: [],
            maxLength: 10,
            hints: {
                main: "Внимание! Первое фото в списке является главным.<br>До 10 фотографий с расширениями jpg, png, gif, не более 5мб",
                requires: "До 10 фотографий с расширениями jpg, png, gif, не более 5мб. Фото можно перетащить в эту зону мышкой."
            },
            initialize: function(options) {
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

                _.each(this.$el.find(".img-b"), function(item) {
                    var params = {
                        filename: $(item).find("input").val(),
                        filepath: $(item).find("img").attr("src"),
                        active: $(item).find(".img").hasClass("active"),
                        original: $(item).find('img').data('original')
                    };
                    self.collection.add(params);
                    $(item).remove();
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

            _init_ajax_upload: function() {
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
                    success: function(result, data) {
                        if (self.collection.length >= self.maxLength) {
                            self.setError("Можно загрузить не более 10 фотографий");
                            return;
                        }
                        if (result.filename) {

                            var active = false;
                            if (self.collection.length == 0) {
                                $("#active_userfile").val(data.filename);
                                active = true;
                            }

                            self.collection.add({
                                filename: result.filename,
                                filepath: result.filepaths['120x90'],
                                active: active
                                    //fix to use crop feature
                                    ,
                                    original: result.filepaths.original
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
                }).on("fileuploadadd", function(e, data) {
                    var jqXHR = data.submit();
                    if (self.collection.length >= self.maxLength) {
                        self.setError("Можно загрузить не более 10 фотографий");
                        jqXHR.abort();
                    }
                    this.jqXHR = jqXHR;
                }).on("fileuploaddone", function(e, data) {
                    if (self.collection.length >= self.maxLength) {
                        self.setError("Можно загрузить не более 10 фотографий");
                        if (!this.jqXHR) {
                            this.jqXHR.errorThrown = "Можно загрузить не более 10 фотографий";
                            this._trigger('fail', e, data);
                        } else {
                            this.jqXHR.abort();
                        }
                    }
                }).on("fileuploadfail", function(e, data) {
                    self.renderHint();
                }).on('fileuploadprogressall', function(e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    if (progress == 100) {
                        $('.fn-photo-hint').html("Файл загружен. Идет сохранение...");
                    } else {
                        $('.fn-photo-hint').html("Идет загрузка... " + progress + '%');
                    }
                });
            },

            addItem: function(item) {
                var container = this.$el.find(".fn-photo-list");
                var el = undefined;
                if (item.get("id"))
                    el = $('#' + item.get("id"));
                this.photos[item.cid] = new photoView({
                    el: el,
                    model: item,
                    collection: this.collection,
                    container: container
                });
                if (this.collection.length > 0) {
                    $(".fn-photo-hint").text();
                }
                this.renderHint();
            },

            removeItem: function(item) {
                this.photos[item.cid].unbind();
                this.photos[item.cid].$el.remove();
                if (this.collection.length && item.get("active")) {
                    this.collection.at(0).set("active", true);
                }
                this.setError("");
                this.renderHint();
            },

            changeActive: function(main_item) {
                var photo_cont = this.photos[main_item.cid].$el.find(".img");
                console.log('set active');
                if (main_item.get("active")) {
                    photo_cont.addClass("active");
                    this.collection.each(function(item) {
                        if (item.cid != main_item.cid)
                            item.set("active", false);
                    });
                    $("#active_userfile").val(main_item.get("filename"));
                } else {
                    photo_cont.removeClass("active");
                }
            },

            onSubmit: function(file, doc) {
                var self = this._settings.data.context;
                self.setError("");
            },

            onComplete: function(file, data) {
                var self = this._settings.data.context;

                if (data)
                    data = $.parseJSON(data);

                if (data === null || !data.filename) {
                    self.setError('Произошла непредвиденная ошибка');
                    return;
                } else if (data.error) {
                    self.setError(data.error);
                    return;
                }
                var active = false;
                if (self.collection.length == 0) {
                    $("#active_userfile").val(data.filename);
                    active = true;
                }

                self.collection.add({
                    filename: data.filename,
                    filepath: data.filepaths['120x90'],
                    active: active
                });

            },

            setError: function(text) {
                $("#error_userfile1").html(text);
            }

        });

});