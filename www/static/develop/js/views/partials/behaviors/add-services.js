/*global define */
define([
    'marionette',
    'templates',
], function (Marionette, templates) {
    'use strict';

    var ServiceView = Marionette.ItemView.extend({
        className: "",
        tagName: "div"
    });


	return Marionette.Behavior.extend({

        ui: {
            el: $(".js-withservice"),
            tab: ".js-withservice-tab",
            field: $("#field_with_service"),
            cont: $(".js-withservice-container")
        },

        events: {
             "click @ui.tab": "onTabChange"
        },

        onChangedCategory: function(params) {
            this.preRender(params)
        },

        onChangedCity: function(params) {
            this.preRender(params)
        },

        onTabChange: function(e) {

            var serviceName = $(e.currentTarget).attr('data-service');
            $(e.currentTarget).siblings().removeClass('active');
            $(e.currentTarget).addClass('active');

            this.setServiceName(serviceName);
            this.preRender({category_id:this.categoryId, city_id:this.cityId});
        },

        setServiceName: function(name) {

            if (name) {
                this.serviceName = name;
                this.ui.field.val(name);
            } else {
                this.serviceName = this.ui.field.val();
            }
           
        },

        preRender: function(params) {
            if (this.view.is_edit) return;
            var categoryId = this.categoryId = params.category_id;
            var cityId = this.cityId = params.city_id;
            var $this = this;

            if (categoryId && cityId && this.ui.el.hasClass('hidden')) {
                this.ui.el.removeClass('hidden');
            }

            if ( (!categoryId || !cityId) && !this.ui.el.hasClass('hidden')) {
                this.ui.el.addClass('hidden');
            }

            if (categoryId && cityId) {

                $.post('/rest_service/get_prices',{category_id: categoryId, city_id: cityId}, function(params){

                    $this.setServiceName();
                    $this.renderService(params);

                });

                
            }
        },

        renderService: function (params) {
            this.ui.cont.html(
                new ServiceView({
                    model: new Backbone.Model({params: params}),
                    template: templates.addService[this.serviceName],
                    category_id: this.categoryId,
                    name:  this.serviceName
                }).render().el
            );

        },

        initialize: function() {

        }

    });
});