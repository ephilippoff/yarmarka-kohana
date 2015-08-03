require.config({
    paths : {
        underscore : 'lib/underscore',
        backbone   : 'lib/backbone',
        marionette : 'lib/backbone.marionette',
        paginator  : 'lib/backbone.paginator',
        localStorage : 'lib/backbone.localStorage',
        jquery     : 'lib/jquery',
        async      : 'lib/async',
        propertyParser: 'lib/propertyParser',
        jssorSlider : 'lib/jssor.slider.mini',
        menuAim : 'lib/jquery.menu-aim',
        jcookie: 'lib/jquery.cookie',
    },
    shim : {
        'lib/backbone-localStorage' : ['backbone'],
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
        paginator : {
            deps : ['backbone'],
            exports: 'Backbone.Paginator'
        }
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
        city_id: _globalSettings.city_id
    };

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
            var msg = "Could not find template: '" + templateId + "'";
            var err = new Error(msg);
            err.name = "NoTemplateError";
            throw err;
        }
        return template;
    };

    app.start();
});