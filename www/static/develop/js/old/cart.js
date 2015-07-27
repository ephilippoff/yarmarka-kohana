var CartItemModel = Backbone.Model.extend({
    sum: function() {
        console.log();
        return this.get('price') * this.get('quantity');
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
        quantity: ".fn-quanitity",
        sum: ".fn-itemsum",
        delete: ".fn-delete"
    },
    events: {
        "change @ui.quantity": "changeQuanitity",
        "click @ui.delete": "deleteItem"
    },
    initialize: function() {
        this.bindUIElements();
    },
    click: function(e) {
       console.log(this.model.toJSON());
    },
    changeQuanitity: function(e) {
        this.model.set("quantity", this.ui.quantity.val());
        this.ui.sum.text(this.model.sum() + " р.");
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

var ApplicationView = Marionette.ItemView.extend({
    el : 'body',
    ui : {
        endSum: ".fn-endsum",
        comment: ".fn-comment",
        save: ".fn-save",
        error: ".fn-error"
    },
    events: {
        "click @ui.save": "saveOrder"
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
        _.each($(".fn-cartitem"), function(item){
            cartList.add({
                id: $(item).data("id"),
                object_id: $(item).data("object-id"),
                type: $(item).data("type"),
                price: $(item).data("price"),
                quantity: $(item).data("quantity"),
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
        this.ui.endSum.text(sum + " р.");
        this.sum = sum;
    },

    saveOrder: function(e) {
        var s = this;
        e.preventDefault();
        this.recalcsum();
        if (this.sum <= 0) return;
        s.showError("");
        var cartListInJSON = this.cartList.toJSON();
        var ids = _.pluck(cartListInJSON, "id");
        var quantityes = _.pluck(cartListInJSON, "quantity");

        this.cartOrder.set({
            comment: this.ui.comment.val(),
            items: _.object(ids, quantityes),
            sum: this.sum
        });

        this.cartOrder.save({},{
            success: function(model, response, xhr) {
                console.log(response)
                if (response.code == 200) {
                    document.location = "cart/order/"+response.order_id;
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
    }

});

$(document).ready(function(){
    var app = new ApplicationView();
})

