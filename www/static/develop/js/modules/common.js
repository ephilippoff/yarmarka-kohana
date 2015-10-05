/*global define */
define([
    "marionette",
    "backbone"
], function (Marionette, Backbone) {
    'use strict';

    return Marionette.Module.extend({
        initialize: function() {
           this.mainSearchInputInit();
        },

        mainSearchInputInit: function() {
            $(".js-search-input").parent().on("mouseover", function(){
                $(this).addClass("focus")
            });
            $(".js-search-input").parent().on("mouseleave", function(){
                $(this).removeClass("focus")
            });
        }
    });
});