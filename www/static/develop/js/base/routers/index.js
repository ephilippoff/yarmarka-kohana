/*global define */
define(['marionette', 'base/utils'],function (Marionette, utils) {
    'use strict';
   
   return Marionette.AppRouter.extend({
        onRoute: function(name, path, args){
            // var queryString = args[0];
                
            // var pattern = /\((.*)\)+/ig;
            // app.currentBaseUrl = (path) ? path.replace(pattern, "") : "";
            // app.currentQueryParams = utils.parseQueryString(queryString);
            //проверяем доступ к странице для авторизвоанных пользователей
            // var page = _.findWhere(app.pages, {url: app.currentBaseUrl});
            // if (page && page.needAuth && !app.auth.isAuthenticated) {
            //     app.auth.vent.trigger("needAuth");
            // }
        }
    });
});