/*global define */
define(['marionette',
    'templates',
    'views/components/windows/service',
    'views/components/windows/backcall',
    'views/components/windows/moderate',
    'views/components/windows/message',
    'views/components/windows/object_callback'
], function (Marionette, templates, ServiceView, BackcallView, ModerateView, MessageView, ObjectCallbackView) {
    'use strict';

    var ErrorWindow = Marionette.ItemView.extend({
            className: "popup-wrp",
            ui: {close: ".s_close",login: ".s_enter",register: ".s_register"},
            events: {'click @ui.close': 'close',"click @ui.login": "toLogin","click @ui.register": "toRegister"},
            close: function(e) {app.windows.vent.trigger("closeWindow", this.getOption("name"));},
            initialize: function(options) {
                this.template = templates.components.windows.error[options.name];
            },
            toLogin:function(e) {
                e.preventDefault();
                app.windows.vent.trigger("closeWindow",this.getOption("name"));
                app.auth.showLoginWindow();
            },
            toRegister:function(e) {
                e.preventDefault();
                app.windows.vent.trigger("closeWindow",this.getOption("name"));
                app.auth.showRegisterWindow();
            }
    });

    return Marionette.Module.extend({
        $layer: null,
        windows : [],
        windowView: {
            service: ServiceView,
            backcall: BackcallView,
            message: MessageView,
            moderate: ModerateView,
            object_callback: ObjectCallbackView

        },
        zIndex: {
            //service: "z150",
            //backcall: "z150",
            //message: "z200"
        },
        label: {
            //panel: {class:".s_panel_label", winclass:".leftsidecont", depency:[] },
        },
        blackLayer: {
            service : true,
            backcall : true,
            moderate : true,
            message : true,
            object_callback: true
        },
        transparentLayer: {
            //galleryItem: "z151"
        },
        initialize: function (options) {
            var s = this;
            console.log("Windows module initialized");
            this.vent = new Backbone.Wreqr.EventAggregator();

            app.backLayer.on("click", function(){
                s.vent.trigger("closeWindow");
            });

            this.vent.on("showWindow", function (name, options) {
                var wnd;
                if (s.windows[name]) app.windows.vent.trigger("closeWindow", name);
                wnd = new s.windowView[name](options);
                $(wnd.render().el).appendTo('#windows');
                s.windows[name] = wnd;
                var label  = s.label[name];
                if (label) {
                    $(label.class).show();
                    label.expand = true;
                    $(".overlay").removeClass("w360").addClass("w30").show();
                    $(label.class).bind("click", function(e){
                        label.expand = !label.expand;
                        if (label.expand) { 
                            $(label.winclass).show();
                            $(label.class).addClass("expanded"); 
                            $(".overlay").addClass("w360").removeClass("w30").show();
                        } else { 
                            $(label.winclass).hide();
                            $(label.class).removeClass("expanded");
                            $(".overlay").removeClass("w360").addClass("w30").show();
                        }
                        if (label.depency.length) { 
                            _.each(label.depency, function(item) { 
                                if (s.label[item].exec) { 
                                    s.label[item].exec(!label.expand);
                                } else { 
                                    $(s.label[item].class).click();
                                }
                            });
                        }
                    });
                }
                if (s.zIndex[name]) {
                    $(".overlay").show().addClass(s.zIndex[name]);
                }
                if (s.blackLayer[name]) {
                    $("#popup-layer").show();
                    $("#popup-layer").bind("click", function(){
                        app.windows.vent.trigger("closeWindow", name);
                    });
                }
                if (s.transparentLayer[name]) {
                    // $("#gallery-layer").addClass(s.transparentLayer[name]).show();
                    // $("#gallery-layer").bind("click", function(){
                    //     app.windows.vent.trigger("closeWindow", name);
                    //     //if (name == "galleryItem") app.windows.vent.trigger("closeWindow", "gallery");
                    //     //if (name == "gomonitorItem") app.windows.vent.trigger("closeWindow", "gomonitor");
                    // });
                }
            });

            this.vent.on("closeWindow", function (name) {
                var wnd = s.windows[name];
                if (!wnd) return;
                wnd.destroy();
                delete s.windows[name];
                var label  = s.label[name];
                if (label) {
                    $(label.class).hide();
                    label.expand = false;
                    $(label.class).addClass("expanded");
                    $(label.class).unbind("click");
                }
                if (s.zIndex[name]) {
                    $(".overlay").hide().removeClass(s.zIndex[name]);
                }
                if (s.blackLayer[name]) {
                    $("#popup-layer").hide();
                    $("#popup-layer").unbind("click");
                }
                if (s.transparentLayer[name]) {
                    // $("#gallery-layer").removeClass(s.transparentLayer[name]).hide();
                    // $("#gallery-layer").unbind("click");
                }
            });

        },

    });
});