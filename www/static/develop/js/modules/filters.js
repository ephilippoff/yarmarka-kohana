/*global define */
define([
    "marionette",
    "backbone",
    "templates"
], function (Marionette, Backbone, templates) {
    'use strict';



    var FiltersModel = Backbone.Model.extend({
        defaults: {
            "value": undefined
        },
        // unselectMult: function(value) {
        //     if (value && this.get("select")) {
        //         this.set("select", _.filter(this.get("select"), function(num){ return num != value; }) );
        //     } else {
        //         this.set("select", false);
        //     }
        //     this.removeChildsMult();
        // },
        // selectMult: function(value) {
        //     //value = (value) ? "_"+value : value;
        //     if (!this.get("select")) this.set("select",[]);
        //     this.get("select").push(value);
        //     console.log(value, this.get("select"))
        //     this.addChilds();
        // },
        unselect: function() {
            this.set("select", false);
            this.removeChilds();
        },
        select: function(value) {
            if (_.isArray(value)) {
                value = _.map(value, function(value){ 
                    return '_' + value; 
                });
            } else {
                value = "_"+value;
                
            }
            this.set("select", value);
            this.addChilds();
        },
        addChilds: function() {
            var s = this,
                selected = this.get("select");
            if (!selected) return
            if (!_.isArray(selected)) selected = [selected];
            var index = this.collection.indexOf(this)+1;
            _.each(selected, function(_value){
                var chname = 'childs'+_value;
                s.set(chname, []);
                if (s.get(_value)) {
                    if ( _.isObject(s.get(_value) ) ) {
                        _.each(s.get(_value), function(item, key) {
                            if (key == 0) return;
                            if (item[0].options != "subelements" && s.collection.findWhere({"seo_name": item[0].seo_name})) return;
                            s.get(chname).push(item[0].ref_id);
                            s.collection.add(item, {at:index});
                            index++;
                        });
                    }
                }
            });

        },
        removeChilds: function() {
            var values = _.keys(this.get('values'));
            this.removeFromList(values);
        },
        removeChildsMult: function() {
            var values = _.keys(this.get('values')),
                selected = this.get("select");
            if (!_.isArray(selected)) selected = [selected];
            var toUnselect = _.difference(values, selected);
            this.removeFromList(toUnselect);
        },
        removeFromList: function(childs, removeChildFunction) {
            var s = this;
            _.each(childs, function(_value){
                var chname = 'childs'+_value;
                if (s.get(chname)) {
                    _.each(s.get(chname), function(ref_id) {
                        var forDelete = s.collection.findWhere({ref_id: ref_id});
                        if (forDelete) {
                            forDelete.removeChilds();
                            s.collection.remove(forDelete);
                        }
                    });
                }
            });
        }
    });

    var FiltersList = Backbone.Collection.extend({
        model: FiltersModel,
        comparator : function(item) {
            return item.get('weight');
        }
    });

    var FilterItem = Marionette.ItemView.extend({
        templateHelpers: function() {
            var s = this;
            return {
                clearValue: function(value) {
                    return (value) ? +value.replace("_","") : 0;
                },
                checked: function(inputValue){
                    var checked = '';
                    var value = s.model.get("value");
                    inputValue = +inputValue.replace("_","")
                    if (value && _.isArray(value)) {
                        checked = _.contains(value, inputValue) ? 'checked' : '';
                    } else if (value) {
                        checked = (value == inputValue) ? 'checked' : '';
                    }
                    return checked;
                }
            }
        },
        showHide: function(e) {
            e.preventDefault();
            this.ui.valuesCont.toggle();
            this.ui.valuesCont.parent().parent().siblings().find(".js-filter-values-cont").hide();
            if (!this.ui.valuesCont.parent().hasClass('active')) {
                $('.multiselect').removeClass('active');
                this.ui.valuesCont.parent().addClass('active');
            }else this.ui.valuesCont.parent().removeClass('active');
            
        },
        initialize: function(options){
            _.extend(this.options.attibute = {}, this.model.get(0))
            delete this.model.attributes[0];
            var s = this;

            this.options.values = {};
            _.each(this.model.attributes, function(value, key){
                if (key.indexOf("_") >= 0 || s.options.attibute["type"] == "ilist") {
                    s.options.values[key] = _.isObject(value) ? value[0]["title"] : value;
                }
            });

            this.model.set(this.options.attibute);
            this.model.set({values: this.options.values});
            this.model.on("change:str_value", function(){
                s.changeValue();
                this.collection.trigger("checkCount");
            });

            var value = this.getOption('queryParams')[this.model.get("seo_name")];
            if (value) {
                this.model.set("value", value);
            }
        },
        onShow: function() {
            (this.setValue) ? this.setValue() : '';
        },
        changeValue: function() {
            if (this.model.get("str_value")) {
                this.ui.strValue.text(this.model.get("str_value"));
            } else {
                this.ui.strValue.text("Не важно");
            }
        }
    });

    var TextFilterItem = FilterItem.extend({
        template: templates.filters.textFilterItem,
        className: 'filter-item',
        ui: {
            valuesCont: ".js-filter-values-cont",
            strValue: ".js-filter-str-value",
            input: "input"
        },
        events: {
            "click .js-filter-label, .js-filter-ok": "showHide",
            "keyup @ui.input": "inputKeyUp"
        },
        initialize: function(options){
            this.bindUIElements();
            FilterItem.prototype.initialize.call(this, options);
        },
        inputKeyUp: function() {
            var text = this.ui.input.val();
            if (text) {
                this.model.set("str_value", text.substr(0,10)+"...");
             } else {
                this.model.set("str_value", false);
             }
           
        },
        setValue: function() {
            var value = this.model.get("value");
            this.ui.input.trigger("keyup");
        }
    });

    var NumericFilterItem = FilterItem.extend({
        className: 'filter-item',
        template: templates.filters.numericFilterItem,
        ui: {
            valuesCont: ".js-filter-values-cont",
            strValue: ".js-filter-str-value",
            firstInput: "input.first",
            lastInput: "input.last"
        },
        events: {
            "click .js-filter-label, .js-filter-ok": "showHide",
            "keyup @ui.firstInput": "inputKeyUp",
            "keyup @ui.lastInput": "inputKeyUp"
        },
        initialize: function(options){
            this.bindUIElements();
            FilterItem.prototype.initialize.call(this, options);
        },
        inputKeyUp: function() {
            var min = parseInt(this.ui.firstInput.val());
            var max = parseInt(this.ui.lastInput.val());
            if (min > 0 || max > 0) {
                 this.model.set("str_value", "от "+((min) ? min : "неогр.")+" до "+((max) ? max : "неогр.") );
             } else {
                this.model.set("str_value", false);
             }
           
        },
        setValue: function() {
            var value = this.model.get("value");
            if (!_.isObject(value)) this.model.set("value", {});
            _.each(this.ui.valuesCont.find("input"), function(input){
                $(input).trigger("keyup");
            });
        }
    });

    var IListFilterItem = FilterItem.extend({
        template: templates.filters.ilistFilterItem,
        ui: {
            valuesCont: ".js-filter-values-cont",
            strValue: ".js-filter-str-value",
            select: "select"
        },
        events: {
            "click .js-filter-label, .js-filter-ok": "showHide",
            "change @ui.select": "selectChange"
        },
        initialize: function(options){
            this.bindUIElements();
            FilterItem.prototype.initialize.call(this, options);
        },
        selectChange: function() {
            var min = parseInt(this.ui.select.first().val());
            var max = parseInt(this.ui.select.last().val());
            if (min > 0 || max > 0) {
                 this.model.set("str_value", "от "+((min) ? min : "неогр.")+" до "+((max) ? max : "неогр.") );
             } else {
                this.model.set("str_value", false);
             }
           
        },
        setValue: function() {
            var value = this.model.get("value");
            if (!_.isObject(value)) this.model.set("value", {});
            _.each(this.ui.valuesCont.find("select"), function(input){
                $(input).trigger("change");
            });
        }
    });

    

    var ListFilterItem = FilterItem.extend({
        template: templates.filters.listFilterItem,
        ui: {
            select: "select",
            option: "option"
        },
        events: {
            "change @ui.select": "changeSelect"
        },
        initialize: function(options){
            FilterItem.prototype.initialize.call(this, options);
        },
        changeSelect: function(e) {
            e.preventDefault();
            var value = this.ui.select.val();
            
            var str_value = this.ui.select.find("this.ui.select").text();
            this.model.set("str_value", str_value);

            this.model.unselect();
            this.model.select(value);
        },
    });

    var ListMultiFilterItem = ListFilterItem.extend({
        template: templates.filters.listBoxFilterItem,
        className: 'multi-cont clearfix filter-item',
        ui: {
            valuesCont: ".js-filter-values-cont",
            input: "input",
            strValue: ".js-filter-str-value",
            anyInput: ".js-filter-anyinput"
        },
        events: {
            "click .js-filter-label, .js-filter-ok": "showHide",
            "change input:not(.js-filter-anyinput)": "changeInput",
            "change @ui.anyInput": "changeAnyInput"
        },
        initialize: function(options){
            var s = this;
            this.bindUIElements();
            ListFilterItem.prototype.initialize.call(this, options);
        },
        changeInput: function(e) {
            e.preventDefault();

            var values = [], urls = {}, str_value;
            _.each(this.ui.valuesCont.find("input:checked"), function(input){
                if ($(input).val() == "on") return;
                values.push($(input).val());
                str_value = $(input).data("str");
            });

            this.model.unselect();
            this.model.select(values);

            if (values.length > 1) {
                this.model.set("str_value", "Выбрано: "+values.length);
            } else {
                this.model.set("str_value", str_value);
            }

            if (values && values.length > 0) {
                this.ui.anyInput.removeAttr("disabled");
                this.ui.anyInput.removeAttr("checked");
            } else {
                this.ui.anyInput.prop("disabled", true);
                this.ui.anyInput.prop("checked", true);
            }
        },
        setValue: function() {
            var value = this.model.get("value");
            value = (_.isArray(value)) ? value : ( (value) ? [value] : false);
            this.model.set("value", value);
            _.each(this.ui.valuesCont.find("input:checked"), function(input){
                if ($(input).val() == "on") return; 
                $(input).trigger("change");
            });
        },
        changeAnyInput: function(e) {
            e.preventDefault();
            _.each(this.ui.valuesCont.find("input:checked"), function(input){
                $(input).prop("checked", false);
                $(input).trigger("change");
            });
        }
    });

    var FiltersListView = Marionette.CollectionView.extend({
        className: "clearfix main-filters-cont",
        getChildView: function(item) {
            var attribute = item.get(0)
            if ( !attribute ) {
                attribute = item.toJSON();
            }

            if ( _.contains(["list"], attribute["type"]) ) return ListMultiFilterItem;
            if ( _.contains(["ilist"], attribute["type"]) ) return IListFilterItem;
            if ( _.contains(["integer","numeric"], attribute["type"]) ) return NumericFilterItem;
            if ( _.contains(["text"], attribute["type"]) ) return TextFilterItem;
        },
        childViewOptions: function() {
            var s = this;
            return {
                queryParams: s.getOption("queryParams")
            }
        }
    });

    var SubmitModel = Backbone.Model.extend({
        url: "/ajax_search/filters_submit"
    });

    var CheckCountModel = Backbone.Model.extend({
        url: "/ajax_search/filters_check"
    });

    var FiltersView = Marionette.LayoutView.extend({
        ui: {
            form: ".js-filters-form",
            submit: ".js-filters-submit",
            infoForm: '.submit-cont>span',
            staticFilters: ".js-filters-static input",
            staticFiltersSelect: ".js-filters-static select"
        },
        regions: {
            filters: ".js-filters-cont"
        },
        events: {
            "click @ui.submit": "submitClick"
        },
        initialize: function(options) {
            this.bindUIElements();
            this.initStaticFilters();
            this.initVariableFilters();
        },
        initStaticFilters: function() {
            var queryParams = this.getOption("queryParams");
            _.each(this.ui.staticFilters, function(item){
                if (queryParams[$(item).attr("name")]) {
                    $(item).prop("checked", true);
                }
            });
            _.each(this.ui.staticFiltersSelect, function(item){
                if (queryParams[$(item).attr("name")]) {
                    $(item).val(queryParams[$(item).attr("name")]);
                }
            });
        },
        initVariableFilters: function() {
            var s = this,
                data = this.getOption("data");

            var filtersList = new FiltersList();
            

            var i = 0;
            _.each(data, function(item) {
                filtersList.add(item, {at:i});
                i=i+10;
            });

            this.filters.show( new FiltersListView({
                collection: filtersList,
                queryParams: this.getOption("queryParams")
            }));

            $(document).mouseup(function (e) {
                var container = $(".js-filters-cont");
                if (container.has(e.target).length === 0){
                    container.find(".js-filter-values-cont").hide();
                }
            });

            this.filtersList = filtersList;
            this.filtersList.on("checkCount", function(model){
                if (s.timer) clearTimeout(s.timer);
                s.timer = setTimeout(function(){
                   
                   var checkCountModel = new CheckCountModel();
                   var formData = s.getFormValues(),
                       queryString = s.getQueryString(formData);

                        checkCountModel.save({
                           queryString: queryString,
                           category_id: app.settings.category_id,
                           city_id: app.settings.city_id
                        }, {
                            success: function(model, response) {
                                var count = model.get("count");
                                if (count > 0) {
                                    s.ui.infoForm.html('<i class="fa fa-check green mr5" aria-hidden="true"></i>Найдено ' + count + ' объявлений');
                                } else {
                                     s.ui.infoForm.html(' <i class="fa fa-exclamation red mr5" aria-hidden="true"></i>Не найдено');
                                }
                            }
                        });
                }, 1500);
            });
        },
        submitClick: function(e) {
            e.preventDefault();
            var s = this,
                formData = this.getFormValues(),
                queryString = this.getQueryString(formData),
                submitModel = new SubmitModel();

            submitModel.save({
                    queryString: queryString,
                    category_id: app.settings.category_id,
                    city_id: app.settings.city_id
                }, {
                    success: function(model, response) {
                        var result = model.toJSON(),
                            category_url = (s.ui.form.attr("action")) ? "/" + s.ui.form.attr("action") : "",
                            seo_segment_url = (result.seo_segment_url) ? "/" + result.seo_segment_url : "",
                            query = (result.query) ? "?" +result.query : "";

                        //submit
                        $(window).attr('location', category_url + seo_segment_url + query);
                    }
                });
        },
        getFormValues: function() {
            var s = this, formData = {};
            this.ui.form.serializeArray().map(function(x){formData[x.name] = x.value;});
            _.each(formData, function(inputValue, inputName){
                if (!inputValue || inputValue == '0') {
                    delete formData[inputName];
                }
            });
            return formData;
        },
        getQueryString: function(data) {
            var result = [];
            _.each(data, function(value, key){
                result.push(key + "=" + value);
            });
            return result.join("&");
        }
    });

    return Marionette.Module.extend({
        initialize: function() {
            this.data = app.settings.data;
            this.queryParams = app.settings.query_params;
            
        },
        initFilters: function(category_id) {
            // if (!this.data) {
            //     this.filtersLoaded();
            //     return;
            // }

            var data = this.data[category_id];
            // if (!_.isObject(this.categoryData)) {
            //     this.filtersLoaded();
            //     return;
            // }

            if (_.isObject(data)) {
                delete data[0];
            } else {
                data = null;
            }
            var filtersView = new FiltersView({
                el: ".js-filters",
                data: data,
                queryParams: this.queryParams
            });

            
            if ( !_.isEmpty(this.queryParams) ) {
                $(".js-filters").show();
            }

            this.filtersLoaded();

            
        },
        filtersLoaded: function() {
            $(".js-search-extend i").remove();
            $(".js-search-extend").click(function(e){
                e.preventDefault();
                $(".js-filters").slideToggle();
            });
        }
    });
});