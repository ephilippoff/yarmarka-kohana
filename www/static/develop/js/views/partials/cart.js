/*global define */
define([
    "marionette",
    "templates",
    "maskedInput"
], function (Marionette, templates) {
    "use strict";

    var CheckNumberModel = Backbone.Model.extend({
        urlRoot:"/rest_service/check_kupon_number",
    });

    var CartItemModel = Backbone.Model.extend({
        urlRoot:"/rest_service/get_temp_item",
        sum: function() {
            return this.get('total');
        }
    });

    var CartList = Backbone.Collection.extend({
        model: CartItemModel
    });

    var CartOrder = Backbone.Model.extend({
        urlRoot: "/cart/saveorder"
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
           //console.log(this.model.toJSON());
        },
        edit: function(e) {
            var serviceName = this.model.get("name");
            var id = this.model.get("id");
            var object_id = this.model.get("object_id");
            var city_id = this.model.get("city_id");
            this.model.fetch({
                data:{id: id},
                success: function(resp){
                    var orderItem = resp.get("result");
                    app.services[serviceName](object_id, {
                        is_edit: id,
                        edit_params: orderItem.params,
                        city_id: city_id,
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
                url: "/cart/remove_item/" +this.model.id,
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
            form: "#cart-form",
            checkNumberform: "#check-number-form",
            checkNumber: ".js-check-number",
            checkNumberSubmit: ".js-check-number-submit",
            messages: ".js-messages",
            endSum: ".js-endsum",
            comment: ".js-comment",
            save: ".js-save",
            error: ".js-error",
            deliveryType: ".js-delivery-type"
        },
        events: {
            "click @ui.save": "saveOrder",
            "change @ui.deliveryType": "changeDeliveryType",
            "click @ui.checkNumberSubmit": "checkNumberSubmit"
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
                    city_id: $(item).data("city"),
                    name: $(item).data("name"),
                    total: $(item).data("total"),
                    $item: item
                });
            });

            this.cartList = cartList;
            this.cartOrder = new CartOrder();
            this.recalcsum();
            if ($(".js-mobile-phone").length) {
                $(".js-mobile-phone").mask("+7(999)999-99-99" , {  
                    "completed": function(){

                    }
                });
            }
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
            var s = this, f = {};
            e.preventDefault();
            this.recalcsum();

            this.ui.form.serializeArray().map(function(x){f[x.name] = x.value;});

            s.showError("");
            var nextPage = s.ui.save.data("next-page");
            var cartListInJSON = this.cartList.toJSON();
            var ids = _.pluck(cartListInJSON, "id");

            this.cartOrder.set({
                sum: this.sum
            });

            this.cartOrder.save(f,{
                success: function(model, response, xhr) {
                    if (response.code == 200) {
                        document.location = "/cart/order/"+response.order_id;
                    } else if (response.code == 300) {
                        document.location = "/cart/order/"+response.order_id;
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
        },

        checkNumberSubmit: function(e) {
            e.preventDefault();
            var s = this;
            var model = new CheckNumberModel();
            model.save({number: this.ui.checkNumber.val(), captcha: $("#captcha_number").val()}, {
                success: function(result){
                    var result = result.toJSON();
                    if (result.code == "200") {
                        s.ui.messages.html("<p class='ta-c green'>Купон действителен</p>");
                    } else if (result.code == "300") {
                        s.ui.messages.html(result.result);
                    } else {
                        s.ui.messages.html("<p class='ta-c red'>Купон недействителен</p>");
                    }
                    
                }
            });

        }

    });
});