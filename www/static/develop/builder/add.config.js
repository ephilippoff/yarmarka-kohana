({

    baseUrl: "../js",
    out: '../production/js/add.build.js',
    paths: {
        underscore : 'lib/underscore',
        backbone   : 'lib/backbone',
        marionette : 'lib/backbone.marionette',
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