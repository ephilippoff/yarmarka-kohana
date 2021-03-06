define('jquery', [], function() {
    return jQuery;
});

require.config({
    paths : {
        underscore : 'lib/underscore',
        backbone   : 'lib/backbone',
        marionette : 'lib/backbone.marionette',
        paginator  : 'lib/backbone.paginator',
        localStorage : 'lib/backbone.localStorage',
        //jquery     : 'lib/jquery',
        async      : 'lib/async',
        propertyParser: 'lib/propertyParser',
        menuAim : 'lib/jquery.menu-aim',
        jcookie: 'lib/jquery.cookie',
        iframeTransport: 'lib/vendor/jquery.iframe-transport',
        fileupload: 'lib/vendor/jquery.fileupload',
        //nicEdit: 'lib/vendor/nicEdit',
        maskedInput: 'lib/vendor/jquery.maskedinput',
        ymap: 'http://api-maps.yandex.ru/2.1/?load=package.full&lang=ru-RU',
        cropper: 'lib/cropper',
        //gisMap: 'http://maps.api.2gis.ru/2.0/loader.js?pkg=basic&lazy=true',
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
        }
    },
    deps : ['underscore']
});

require([ 'app',
          'jquery',
          'marionette',
          'backbone',
          'underscore'
        ], 
function (app, $, Marionette, Backbone, _) {
    'use strict';

    app.settings = {
        page: _globalSettings.page,
        data: _globalSettings.data,
        category_id: _globalSettings.category_id,
        city_id: _globalSettings.city_id,
        host: _globalSettings.host,
        mainHost: 'yarmarka.dev',
        debug: _globalSettings.debug,
    };

    if (app.settings.debug) {
        app.settings.staticPath = '/static/develop/';
    } else {
        app.settings.staticPath = '/static/develop/production/';
    }

    try {
        app.settings.query_params = (_globalSettings.query_params) ? JSON.parse(_globalSettings.query_params) : {};
    } catch (e) {
        app.settings.query_params = {};
    }

    Backbone.emulateHTTP = true;
    //Backbone.emulateJSON = true;

    Marionette.Behaviors.behaviorsLookup = function() {
        return window.Behaviors;
    };

    //переопредляем загрузку шаблонов для совместимостис requirejs
    Marionette.TemplateCache.prototype.loadTemplate = function(templateId) {
        var template = templateId;
        if (!template || template.length === 0){
            var msg = 'Could not find template: "' + templateId + '"';
            var err = new Error(msg);
            err.name = 'NoTemplateError';
            throw err;
        }
        return template;
    };

    app.start();
});

//Прилипающий сайдбар на страницах

$(document).ready(function(){
    var mainBlockHeight = $('.main_block').outerHeight();
    var rightsideHeight = $('.right-side').outerHeight();

    if ( ($('div').is('.right-side')) && (rightsideHeight < mainBlockHeight)) {
        var mainBlockBottomY = $(document).height() - ($('.main_block').offset().top + $('.main_block').outerHeight());
        $('.right-side').hcSticky({
            stickTo: document,
            bottomEnd: mainBlockBottomY
        });
    }
});

function animated (element, animation) {
    $(element).addClass("animated").addClass(animation);
    setTimeout(function(){
        $(element).removeClass('animated').removeClass(animation);
    }, 400);
}

$(function(){
        var i = 1;
        $('#vk_comments').each(function(){
            $(this).attr('id', 'vk_comments'+i);
            i++;
        });
    });