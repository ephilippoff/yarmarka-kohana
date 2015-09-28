/*global define */
define([
    "marionette",
    "backbone",
    "templates"
], function (Marionette, Backbone, templates) {
    'use strict';

    return Marionette.ItemView.extend({
        template: templates.components.services.kupon,
        ui: {
            group: ".js-group",
            price: ".js-price"
        },
        events: {
            "change @ui.group": "changeGroup"
        },
        changeGroup: function(e) {
            var groupId = parseInt($(e.currentTarget).val());
            var group = _.findWhere(this.model.get("info").groups, {id:groupId});
            console.log(groupId, group)
            var result = {
                quantity: 1,
                sum: group.service.price,
                id: groupId
            }
            _.extend(this.model.get("info"), group);
            this.model.set("result", result);
        },
        onRender:function() {
            this.bindUIElements();
            this.ui.group.first().trigger("change");
        }
    });
});