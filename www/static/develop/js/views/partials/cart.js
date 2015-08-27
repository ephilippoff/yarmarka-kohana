/*global define */
define([
    "marionette",
    "templates",
], function (Marionette, templates) {
    "use strict";

    var CartItemModel = Backbone.Model.extend({
        urlRoot:"rest_service/get_temp_item",
        sum: function() {
            return this.get('total');
        }
    });

    var CartList = Backbone.Collection.extend({
        model: CartItemModel
    });

    var CartOrder = Backbone.Model.extend({
        urlRoot: "cart/save"
    });


    var CartItemView = Marionette.ItemView.extend({
        ui: {
            edit: ".js-edit",
            sum: ".js-itemsum",
            delete: ".js-delete"
        },
        events: {
            "click @ui.edit": "edit",
            "click @ui.delete": "deleteItem",
            "click": "click"
        },
        initialize: function() {
            this.bindUIElements();
        },
        click: function(e) {
           console.log(this.model.toJSON());
        },
        edit: function(e) {
            var type = this.model.get("type");
            var id = this.model.get("id");
            var object_id = this.model.get("object_id");
            this.model.fetch({
                data:{id: id},
                success: function(resp){
                    var orderItem = resp.get("result");
                    app.services[type](object_id, {
                        is_edit: id,
                        edit_params: orderItem.params,
                        success: function(result) {
                            location.reload();
                            console.log(result);
                        },
                        error: function(result) {
                           console.log(result);
                        }
                    });
                }
            })
           
        },
        deleteItem: function(e) {
            var s = this;
            e.preventDefault();
            this.model.destroy( {
                url: "cart/remove_item/" +this.model.id,
                success: function() {
                    s.destroy();
                },
                wait: true
            })
        }
    });

    return Marionette.ItemView.extend({
        el : 'body',
        ui : {
            endSum: ".js-endsum",
            comment: ".js-comment",
            save: ".js-save",
            error: ".js-error",
            deliveryType: ".js-delivery-type"
        },
        events: {
            "click @ui.save": "saveOrder",
            "change @ui.deliveryType": "changeDeliveryType"
        },
        initialize: function() {
            this.bindUIElements();
            var s = this,
                cartList = new CartList();

            cartList.on("change:quantity", function(model){
                s.recalcsum();
            });
            cartList.on("remove", function(model){
                if (this.models.length == 0) {
                    document.location = "cart";
                }
                s.recalcsum();
            });
            cartList.on("add", function(model){
                new CartItemView({
                    model: model,
                    el: model.get("$item")
                });
            });
            _.each($(".js-cartitem"), function(item){
                cartList.add({
                    id: $(item).data("id"),
                    object_id: $(item).data("object-id"),
                    type: $(item).data("type"),
                    total: $(item).data("total"),
                    $item: item
                });
            });

            this.cartList = cartList;
            this.cartOrder = new CartOrder();
            this.recalcsum();
        },

        recalcsum: function() {
            var sum = 0;
            this.cartList.each(function(item){
                sum += item.sum();
            });
            this.ui.endSum.text(sum + " руб.");
            this.sum = sum;
        },

        saveOrder: function(e) {
            var s = this;
            e.preventDefault();
            this.recalcsum();
            if (this.sum <= 0) return;
            s.showError("");
            var nextPage = s.ui.save.data("next-page");
            var cartListInJSON = this.cartList.toJSON();
            var ids = _.pluck(cartListInJSON, "id");

            this.cartOrder.set({
                sum: this.sum
            });

            this.cartOrder.save({},{
                success: function(model, response, xhr) {
                    console.log(response)
                    if (response.code == 200) {
                        document.location = nextPage+response.order_id;
                    } else {
                        s.showError(response.message);
                    }
                },
                error: function(response) {
                    s.showError("Ошибка при сохранении заказа");
                }
            });
        },
        showError: function(message) {
            if (message) {
                this.ui.error.html(message)
            } else {
                this.ui.error.html("");
            }
        },
        changeDeliveryType: function(e) {
            e.preventDefault();
            var value = $(e.currentTarget).val();
            console.log(value)
            $(".js-delivery-cont").hide();
            $(".js-"+value+"-cont").show();
        }

    });
});