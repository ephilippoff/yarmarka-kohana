/*global define */
define([
    "marionette",
    "templates",
    "jssorSlider"
], function (Marionette, templates, jssorSlider) {
    "use strict";

    return Marionette.LayoutView.extend({
        ui: {
            favourite: ".js-favourite",
            favouriteCounter: ".js-favourite-counter",
            favouriteCount: ".js-favourite-counter > span"
        },

        events: {
            "click @ui.favourite": "favouriteClick"
        },

        initialize: function() {
            this.bindUIElements();
        },

        favouriteClick: function(e) {
            var s = this;
            e.preventDefault();
            var id = $(e.currentTarget).data("id");
            app.favourite.addRemove(id, {
                success: function(result, favourites) {
                    if (result) {
                        $(e.currentTarget).addClass("in");
                    } else {
                        $(e.currentTarget).removeClass("in");
                    }
                    console.log(favourites)
                    s.ui.favouriteCount.text(favourites ? favourites.length : 0);
                }
            });
        }
    });
});