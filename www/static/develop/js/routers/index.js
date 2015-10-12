/*global define */
define([
    "marionette",
    "controllers/index"
    ],function (Marionette, SectionController) {
    "use strict";
   
   return Marionette.AppRouter.extend({
        routes: {
            "": "startSection",
            "*notFound": "startSection"
        },

        onRoute: function(name, path, args){
            //BaseRouter.prototype.onRoute.call(this, name, path, args);
            console.log("index route fired: " + name, path, args);
        },

        getSectionController: function() {
            return new SectionController();
        },

        startSection: function(args) {
            var controller = this.getSectionController();
            if (controller["start_" + app.settings.page + "Section"]) {
                controller["start_" + app.settings.page + "Section"](args);
            }
        },

        notFound: function() {

        }
    });
});