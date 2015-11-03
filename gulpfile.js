var gulp = require('gulp'), // Сообственно Gulp JS
    csso = require('gulp-csso'), // Минификация CSS
    htmlmin = require('gulp-htmlmin'), // Минификация HTML
    imagemin = require('gulp-imagemin'), // Минификация изображений
    uglify = require('gulp-uglify'), // Минификация JS
    concat = require('gulp-concat'), // Склейка файлов
    jshint = require('gulp-jshint'),
    jslint = require('gulp-jslint'),
    connect = require('gulp-connect'), // Webserver
    stylish = require('jshint-stylish'), 
    inject = require('gulp-inject'),
    urlAdjuster = require('gulp-css-url-adjuster'),
    minifyHTML = require('gulp-minify-html');


gulp.task('bower', function() {
    gulp.src(['./bower_components/jquery/dist/jquery.js', 
                './bower_components/underscore/underscore.js',
                './bower_components/requirejs/require.js',
                './bower_components/requirejs-text/text.js',
                './bower_components/requirejs-plugins/src/async.js',
                './bower_components/requirejs-plugins/src/propertyParser.js',
                './bower_components/requirejs-plugins/src/goog.js',
                './bower_components/backbone/backbone.js',
                './bower_components/backbone.paginator/lib/backbone.paginator.js',
                './/bower_components/backbone.localStorage/backbone.localStorage.js',
                './bower_components/marionette/lib/backbone.marionette.js',
                './bower_components/backbone/backbone.js',
                './bower_components/jquery-dateFormat/dist/jquery-dateFormat.js'])
        .pipe(gulp.dest('./www/static/develop/js/lib'));
});

gulp.task('html', function() {
    gulp.src(['./public/**/*.html'])
        .pipe(connect.reload()); // даем команду на перезагрузку страницы
});

gulp.task('template', function() {
    gulp.src(['./public/static/js/templates/**/*.tmpl'])
        .pipe(connect.reload()); // даем команду на перезагрузку страницы
});

gulp.task('js', function() {
    gulp.src(['./public/static/js/**/*.js'])
        .pipe(connect.reload()); // даем команду на перезагрузку страницы
});

gulp.task('css', function() {
    gulp.src(['./public/static/css/**/*.css'])
        //.pipe(gulp.dest('./public/css'))
        .pipe(connect.reload()); // даем команду на перезагрузку страницы
});

gulp.task('images', function() {
    gulp.src('./public/static/img/**/*')
        //.pipe(gulp.dest('./public/img'))
});

gulp.task('connect', function() {
    connect.server({
        root:'public',
        livereload: false,
        port:9000
    });
});

// Запуск сервера разработки gulp watch
gulp.task('watch', function() {
        gulp.watch('public/**/*.html', ["html"]);
        gulp.watch('public/static/css/**/*.css', ["css"]);
        //gulp.watch('public/static/js/templates/**/*.tmpl', ["template"]);        
        //gulp.watch('public/static/js/**/*.js', ["js"]);
});

gulp.task('default', ['connect', 'watch']);

// build application
gulp.task('buildcss', function() {
    return gulp.src(['./www/static/develop/css/**/*.css'])
                //.pipe(concat('appstyles.css'))
                .pipe(csso())
                .pipe(gulp.dest('./www/static/develop/production/css/'));
});

gulp.task('buildconcatcss', function() {
    return gulp.src(['./www/static/develop/css/bootstrap.min.css',
                        './www/static/develop/css/bootstrap.tables.min.css',
                            './www/static/develop/css/font-awesome.css',
                            './www/static/develop/css/iLight.css',
                            './www/static/develop/css/css.css'])
                .pipe(concat('appstyles.css'))
                .pipe(csso())
                .pipe(gulp.dest('./www/static/develop/production/css/'));
});


gulp.task('buildfonts', function() {
    return gulp.src(['./www/static/develop/fonts/**/*'])
                .pipe(gulp.dest('./www/static/develop/production/fonts/'));
});

// gulp.task('buildhtml', function() {
//     return gulp.src('./public/index.html')
//                 .pipe(gulp.dest('./app-build'));
// });

gulp.task('buildtemplates', function() {
    return gulp.src(['./public/static/js/templates/**/*.tmpl'])
                .pipe(gulp.dest('./app-build/static/js/templates/'));
});

gulp.task('buildjs', function() {
    return gulp.src(['./www/static/develop/js/lib/require.js'])
                .pipe(uglify())
                .pipe(gulp.dest('./www/static/develop/production/js/'));
});

gulp.task('buildimages', function() {
    return gulp.src(['./www/static/develop/images/**/*'])
                .pipe(imagemin())
                .pipe(gulp.dest('./www/static/develop/production/images/'));
});

gulp.task('injects', [
    //'buildfonts',
    'buildcss', 
    //'buildjs'
], function() {

    // return gulp.src('././templates/page/base.html')
    //             .pipe(inject( gulp.src(['./app-build/static/css/**/*.css'], {read: false}), 
    //                 {
    //                     ignorePath: 'app-build',
    //                     addRootSlash: true
    //                 }
    //             ))
    //             .pipe(inject( gulp.src(['./app-build/static/js/lib/require.js'], {read: false}), 
    //                 {
    //                     ignorePath: 'app-build',
    //                     addRootSlash: true,
    //                     transform: function (filepath) {
    //                         var scripts = '<script>var _globalSettings =  { host: "' + settings.host + ', page: "{% block scripts_page %}index{% endblock %}" };</script>';
    //                         return scripts + '<script data-main="/static/outdoor/js/build.js" src="' + filepath + '"></script>';
    //                     }
    //                 }
    //             ))
    //             .pipe(gulp.dest('././templates/page'));
});

var settings = {
    host: 'http://globalloutdoor.com/',
    tilesServer : ''
}

gulp.task('build', [
    'buildimages',
    'buildfonts', 
    'buildcss', 
    'buildconcatcss',
    'buildjs',
    //'buildhtml', 
    //'injects'
]);


gulp.task('moveBuilded', function() {
    // gulp.src(['./app-build/*.html'])
    //     .pipe(gulp.dest('./../outdoor-server/restapi/templates'));
    // gulp.src(['./app-build/static/**/*.*'])
    //     .pipe(gulp.dest('./../static/outdoor/product'));
    gulp.src(['./app-build/images/**/*.*'])
        .pipe(gulp.dest('./../static/images'));
});

gulp.task('moveFiles', ['moveBuilded']);