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

    console.log(_globalSettings);

    app.settings = {
        page: _globalSettings.page,
        data: _globalSettings.data
    };

    Marionette.Behaviors.behaviorsLookup = function() {
        return window.Behaviors;
    };

    app.start();
});