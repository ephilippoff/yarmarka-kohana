/*global define */
define([
    'marionette',
    'templates',
], function (Marionette, templates) {
    'use strict';

    var List = Backbone.Collection.extend({});

    var ItemView = Marionette.ItemView.extend({
        events: {
            "mouseover": "mouseOver",
            "mouseout": "mouseOut",
            "click": "click"
        },
        mouseOut: function() {
            this.$el.removeClass("mark");
        },
        mouseOver: function() {
            this.$el.siblings().removeClass("mark");
            this.$el.addClass("mark");
            
            if (this.$el.hasClass("pricerow")){
               this.getOption("uiSearchInput").val(this.getOption("text"));
            } else {
               this.getOption("uiSearchInput").val(this.model.get("title"));
            }
        },
        click: function() {
            //this.getOption("uiSearchForm").submit();
            window.location.href = this.$('[data-goto]').data('goto');
        }
    });

    var ObjectItemView = ItemView.extend({
        template: "<span class='finded-title' data-goto='<%= url %>'><%=title %></span><a href='/<%=category_url %>'><%=category_title %></a>",
        className: "result-line",
        tagName: "li"
    });

    var PricerowItemView = ItemView.extend({
        template: "<span class='finded-title'><%=description %></span><span class='city'><%=city_name %></span><span class='city mr20' style='color:gray;'><a href='/detail/<%=object_id %>'><%=title %></a></span>",
        className: "result-line pricerow",
        tagName: "li"
    });

    var CollectionView = Marionette.CollectionView.extend({
        tagName: "ul",
        childViewOptions: function() {
            var s = this;
            return {
                uiSearchInput: s.getOption("uiSearchInput"),
                uiSearchForm: s.getOption("uiSearchForm"),
                text: s.getOption("text"),
            }
        }
    });

    var SearchPopup = Marionette.LayoutView.extend({
        template: templates.searchPopup,
        className: "z201",
        regions: {
            objects: ".js-objects",
            pricerows: ".js-pricerows"

        },
        templateHelpers: function () {
            var s = this;
            return {
                objects_found: function() {
                    return s.getOption("objects_found")
                },
                pricerows_found: function() {
                    return s.getOption("pricerows_found")
                }
            }
        },
        onRender: function() {
            console.log(this.$el);
            this.objects.show(new CollectionView({
                collection: this.getOption("objects"), 
                childView: ObjectItemView,
                uiSearchInput: this.getOption("uiSearchInput"),
                uiSearchForm: this.getOption("uiSearchForm"),
                text: this.getOption("text"),
            }));
            this.pricerows.show(new CollectionView({
                collection: this.getOption("pricerows"), 
                childView: PricerowItemView,
                uiSearchInput: this.getOption("uiSearchInput"),
                uiSearchForm: this.getOption("uiSearchForm"),
                text: this.getOption("text"),
            }));
        }
    });

	return Marionette.Behavior.extend({
        ui: {
            searchInput: ".js-search-input",
            searchForm: ".js-search-form",
            searchPopupCont: ".js-search-popup-cont",
            categoryBox: '[data-role=categories]',

        },

        events: {
            "keyup @ui.searchInput": "searchInputKeyUp",
            "click"                 : "onClick",
            'submit @ui.searchForm': 'beforeSubmitForm',
            'click @ui.categoryBox' : 'accordeonToggle',
            'click .optgroup' : 'openOptgroup',
            'click .back' : 'getBack',
            'click .option:not(.back)' : 'setValue'
        },

        onClick: function(e) {
            if (e.target != this.ui.searchPopupCont) {
                this.$container = $('.accordeon-menu');
                this.$container.addClass('brb2, bb');
                this.$container.find('.select_wrap').hide();
            }
        },

        accordeonToggle: function(e){
            e.stopPropagation();
            this.$container = $('.accordeon-menu');
            this.$container.toggleClass('brb2');
            this.$container.find('.select_wrap').toggle();
        },

        openOptgroup: function(e){
            e.stopPropagation();
            var self = $(e.currentTarget);
            self.addClass('active').children('.option').show();
            this.$container = $('.accordeon-menu');
            this.$container.find('.option').first().addClass('back').html('<i class="fa fa-long-arrow-left mr5" aria-hidden="true"></i> Назад');
            $('.optgroup:not(.active)').slideUp();         
            self.find('.optgroup_value').addClass('active bold');

        },

        getBack: function(e){
            e.stopPropagation();
            var self = $(e.currentTarget);
            $('.option').not('.back').slideUp();
            $('.optgroup_value').each(function(){
                $(this).removeClass('active bold');
            });
            $('.optgroup').removeClass('active').slideDown();

            $('.option.back').removeClass('back').html('Все объявления');
        },

        setValue: function(e){
            var self = $(e.currentTarget);
            if (self.data('value') == 1) {
                self.addClass('back');
            }

            this.ui.categoryBox.find('.current_value').html(self.html());

            this.getBack(e);
            this.accordeonToggle(e);


            this.ui.categoryBox.data({
                'value' : self.data('value'),
                'url'   : self.data('url')
            });

        },

        beforeSubmitForm: function(e) {
            this.ui.searchForm.attr('action', '/' + this.ui.categoryBox.data('url'));
        },

        searchInputKeyUp: function(e) {
            e.preventDefault();
            var $input = $(e.currentTarget),
                city = $input.data("city"),
                text = $input.val(),
                keyCode = e.keyCode,
                s = this;

            if (keyCode != 38 && keyCode != 40) {
                this.text = text;
                app.search.do(text, {
                    city_id: city,
                    success: function(objects, pricerows, objects_found, pricerows_found) {
                        console.log(objects, pricerows, objects_found, pricerows_found);
                        var popup = new SearchPopup({
                            objects: new List(objects),
                            pricerows: new List(pricerows),
                            objects_found: objects_found, 
                            pricerows_found: pricerows_found,
                            uiSearchInput: s.ui.searchInput,
                            uiSearchForm: s.ui.searchForm,
                            text: text
                        });
                        s.ui.searchPopupCont.html(popup.render().el);

                        if (!objects.length && !pricerows.length){
                           s.ui.searchPopupCont.hide();
                           return;
                        } else {
                            s.ui.searchPopupCont.show();
                        }
                    }
                }, this.ui.categoryBox.data('value'));
            }

            if (keyCode == 13 && this.ui.searchPopupCont.find('li.mark')) {
                e.preventDefault();
                e.stopPropagation();
                this.ui.searchPopupCont.find('li.mark').trigger('click');
                return;
            }

            if (!text) {
                s.ui.searchPopupCont.hide();
            }
            this.doMarkRow(keyCode);
        },
        doMarkRow: function(keyCode) {
            //38 up 40 down
            var s = this;
            if (keyCode != 38 && keyCode != 40)
                return;

            var rows = s.ui.searchPopupCont.find("li");
            var nowrow = rows.index($(".mark"));
            var pos = 0;
            if (keyCode == 38){
                if (nowrow < 0) {
                    pos = rows.length-1;
                }
                else {
                    pos = nowrow;
                    pos--;
                }

                rows.removeClass("mark");
                rows.eq(pos).addClass("mark");

            }
            if (keyCode == 40){
                if (nowrow < 0) {
                    pos = 0;
                }
                else {
                    pos = nowrow;
                    pos++;
                }
                rows.removeClass("mark");
                rows.eq(pos).addClass("mark");
            }

            if (rows.eq(pos).hasClass("pricerow")){
                s.ui.searchInput.val(this.text);
            } else {
                var text = rows.eq(pos).find(".finded-title").text();
                if (text)
                    s.ui.searchInput.val(text);
            }
        }
    });
});