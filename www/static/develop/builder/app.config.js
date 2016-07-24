({
//- paths are relative to this app.build.js file
    //appDir: "../public/assets",
    baseUrl: "../js",
    mainConfigFile: '../js/main.js',
    out: '../production/js/build.js',
    //- this is the directory that the new files will be. it will be created if it doesn't exist
    //dir: "../app-build",
    paths: {
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
        //ymap: 'http://api-maps.yandex.ru/2.1/?load=package.full&lang=ru-RU',
        //gisMap: 'http://maps.api.2gis.ru/2.0/loader.js?lazy=true',
        templates: 'templates'
    },
    optimizeCss: "standard",
    include: ['main'],
    //optimize: "none",
    fileExclusionRegExp: /\.git/
})