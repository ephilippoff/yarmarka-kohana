require.config({
    paths : {
        underscore : 'lib/underscore',
        backbone   : 'lib/backbone',
        marionette : 'lib/backbone.marionette',
        //paginator  : 'lib/backbone.paginator',
        localStorage : 'lib/backbone.localStorage',
        jquery     : 'lib/jquery.min',
        //menuAim : 'lib/jquery.menu-aim',
        jcookie: 'lib/jquery.cookie'
        //iframeTransport: 'lib/vendor/jquery.iframe-transport',
        //fileupload: 'lib/vendor/jquery.fileupload',
        //nicEdit: 'lib/vendor/nicEdit',
        //maskedInput: 'lib/vendor/jquery.maskedinput',
        //ymap: "http://api-maps.yandex.ru/2.1/?load=package.full&lang=ru-RU",
        //isotope: 'lib/vendor/isotope.pkgd'
    },
    shim : {
        localStorage : ['backbone'],
        underscore : {
            exports : '_'
        },
        backbone : {
            exports : 'Backbone',
            deps : ['jquery', 'underscore']
        },
        marionette : {
            exports : 'Backbone.Marionette',
            deps : ['backbone']
        },
        localStorage : {
            deps : ['backbone']
        },
        
    },
    deps : ['jquery', 'underscore']
});

require([ 'app',
          'jquery',
          'marionette',
          'backbone',
          'underscore'
        ], 
function (app, $, Marionette, Backbone, _) {
    "use strict";

    app.settings = {
        page: _globalSettings.page,
        data: _globalSettings.data,
        category_id: _globalSettings.category_id,
        city_id: _globalSettings.city_id,
        objects_for_map: _globalSettings.objects_for_map,
        kohana_host: _globalSettings.host,
        mainHost: _globalSettings.mainHost,
        khQuery: false
    };

    $.ajaxPrefilter( function( options, originalOptions, jqXHR ) {
        if (app.settings.khQuery && app.settings.kohana_host) {
            options.crossDomain = true,
            options.url = "http://" + app.settings.kohana_host + options.url;
        }
    });

    var originalSync = Backbone.sync;
    Backbone.sync = function(method, model, options) {
        options.headers = options.headers || {};
        if (app.settings.khQuery && !options.crossDomain) {options.crossDomain = true;}
        //if (!options.xhrFields) { options.xhrFields = {withCredentials:true};}

        originalSync.call(model, method, model, options);
    };

    //Backbone.emulateHTTP = true;
    //Backbone.emulateJSON = true;

    Marionette.Behaviors.behaviorsLookup = function() {
        return window.Behaviors;
    };

    //переопредляем загрузку шаблонов для совместимостис requirejs
    Marionette.TemplateCache.prototype.loadTemplate = function(templateId) {
        var template = templateId;
        if (!template || template.length === 0){
            var msg = "Could not find template: '" + templateId + "'";
            var err = new Error(msg);
            err.name = "NoTemplateError";
            throw err;
        }
        return template;
    };

    String.prototype.hashCode = function(){
        if (Array.prototype.reduce){
            return this.split("").reduce(function(a,b){a=((a<<5)-a)+b.charCodeAt(0);return a&a},0);              
        } 
        var hash = 0;
        if (this.length === 0) return hash;
        for (var i = 0; i < this.length; i++) {
            var character  = this.charCodeAt(i);
            hash  = ((hash<<5)-hash)+character;
            hash = hash & hash; // Convert to 32bit integer
        }
        return hash;
    }

    app.start();
});