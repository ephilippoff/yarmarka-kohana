({
//- paths are relative to this app.build.js file
    //appDir: "../public/assets",
    baseUrl: "../js",
    //mainConfigFile: '../js/views/partials/add.js',
    out: '../production/js/add.build.js',
    //- this is the directory that the new files will be. it will be created if it doesn't exist
    //dir: "../app-build",
    paths: {
        underscore : 'lib/underscore',
        backbone   : 'lib/backbone',
        marionette : 'lib/backbone.marionette',
        // paginator  : 'lib/backbone.paginator',
        // localStorage : 'lib/backbone.localStorage',
        // //jquery     : 'lib/jquery',
        // async      : 'lib/async',
        // propertyParser: 'lib/propertyParser',
        // menuAim : 'lib/jquery.menu-aim',
        // jcookie: 'lib/jquery.cookie',
        // iframeTransport: 'lib/vendor/jquery.iframe-transport',
         cropper: 'lib/cropper',
         ymap: 'http://api-maps.yandex.ru/2.1/?load=package.full&lang=ru-RU',
         fileupload: 'lib/vendor/jquery.fileupload',
         nicEdit: 'lib/vendor/nicEdit',
         maskedInput: 'lib/vendor/jquery.maskedinput',
         templates: 'templates',
         ckeditor: 'lib/ckeditor/ckeditor',
        ckeditorJqueryAdapter: 'lib/ckeditor/adapters/jquery'
    },
    optimizeCss: "standard",
    //optimize: "none",
    include: ['../js/views/partials/add'],

    fileExclusionRegExp: /\.git/
})