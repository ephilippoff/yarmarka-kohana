/*global define */
define([
    'marionette',
    'templates',
], function (Marionette, templates) {
    'use strict';

	return Marionette.Behavior.extend({
        ui: {
            favourite: ".js-favourite",
            favouriteCounter: ".js-favourite-counter",
            favouriteCount: ".js-favourite-counter > span",
            favouriteCaption: ".js-favourite-caption"
        },

        events: {
            "click @ui.favourite": "favouriteClick"
        },

        favouriteClick: function(e) {
            var s = this;
            e.preventDefault();
            var id = $(e.currentTarget).data("id");
            var $ico = $(e.currentTarget);
            var $icoCaption = $(e.currentTarget).find(s.ui.favouriteCaption);
            if (!$(e.currentTarget).hasClass("js-favourite-ico")) {
                $ico = $ico.find(".js-favourite-ico");
            }
            app.favourite.addRemove(id, {
                success: function(result, favourites) {
                    if (result) {
                        $ico.removeClass("fa-heart-o").addClass("fa-heart");
                        if ($icoCaption) {
                            $icoCaption.text("В избранном");
                        }
                    } else {
                        $ico.removeClass("fa-heart").addClass("fa-heart-o");
                         if ($icoCaption) {
                            $icoCaption.text("В избранное");
                        }
                    }
                    s.ui.favouriteCount.text(favourites ? favourites.length : 0);
                }
            });
        }
    });
});