
function Filter(object){
    var self = this;

    var filterObject = object;
    var filterElement = {};
 

    var states = {
        active: function (){ 
            $(filterElement.id).trigger('setActiveEvent', [true]);
            filterObject.state = 'active'; 
            $(filterElement.field).addClass('active');
            $(filterElement.filter_box).show();
        },
        disactive: function (){ 
            filterObject.state = 'disactive'; 
            $(filterElement.field).removeClass('active');
            var countChecked = $(filterElement.filter_box).find(':checked').not(filterElement.filter_anyinput).length;
            var countAll = $(filterElement.filter_box).find("input[type='checkbox']").not(filterElement.filter_anyinput).length;

            $(filterElement.filter_box).hide();
            if ((filterObject.parentFieldIdentify) && (countAll == 0)) { $(filterElement.field).hide(); }

            if (countChecked > 0){
                $(filterElement.filter_anyinput).removeAttr('checked').removeAttr('disabled');
                $(filterElement.filter_label).find('.info').text($(filterElement.filter_box).find(':checked').not(filterElement.filter_anyinput).eq(0).next('span').eq(0).text());  
                if (countChecked > 1){
                    $(filterElement.filter_label).find('.info').addClass('a');
                } else {
                    $(filterElement.filter_label).find('.info').removeClass('a');
                }
            } else 
            if ($(filterElement.filter_rangeInputs).length >0){
                var min_value = $(filterElement.filter_rangeInputs).eq(0).val();
                var max_value = $(filterElement.filter_rangeInputs).eq(1).val();
                var unit = $(filterElement.filter_rangeUnit).text();
                $(filterElement.filter_label).find('.info').text('от ' + min_value + ' до ' + max_value + ' ' + unit);
            } else
            if ($(filterElement.filter_textInputs).length >0){
                var value = $(filterElement.filter_textInputs).eq(0).val();
                $(filterElement.filter_label).find('.info').text(value);
            } else {
                $(filterElement.filter_label).find('.info').text('Не важно');
                $(filterElement.filter_label).find('.info').removeClass('a');   
            }
        },
    }

    initialize();

    function initialize (){ 
        _initElements();
        _subscribeEvents();
        state('disactive');
    }

    function _initElements(){

        var id  = filterObject.filter_identify; 

        var field = $(id);      
        filterElement = {
            id :  id,           
            field :  field,
            filter_label : $(field).find('.fn-filter-label'),
            filter_box : $(field).find('.fn-filter-box'),           
            filter_anyinput : $(field).find('input.fn-filter-box-anyinput'),
            filter_checkboxInputs : $(field).find('input[type="checkbox"]').not('.fn-filter-box-anyinput'),
            filter_childcontent: $(field).find('.fn-childcontent'),
            filter_rangeInputs : $(field).find('input[type="text"].fn-range'),
            filter_rangeUnit : $(field).find('.fn-range-unit'),
            filter_textInputs : $(field).find('input[type="text"].fn-text'),
            filter_additionalInfo : $(field).find('.fn-additional-info'),
        };

        if ($(filterElement.field).attr('child_for') !== undefined) {
            filterObject.parentFieldIdentify = '#'+$(filterElement.field).attr('child_for');
        }

    }

    function _subscribeEvents(){
        $(filterElement.filter_checkboxInputs).bind('click', onInputCheckboxClick);
        $(filterElement.filter_anyinput).bind('click', onAnyInputCheckboxClick);
        $(filterElement.filter_label).bind('click', onFilterLabelClick);
    }

    function state(state_val){
        if (state_val){
            states[state_val]();
        } else {
            return filterObject.state;
        }
    };

    function updateChild(){
        if (filterObject.child === undefined) {
            return;
        }
        var valuesArray = [];
        var checkedItems = $(filterElement.filter_box).find(':checked').not(filterElement.filter_anyinput);
        var countChecked = checkedItems.length;
        if (countChecked == 0) {
            $(filterObject.child.filterElement.field).hide();
        } else {
            $(filterObject.child.filterElement.field).show();
            $(checkedItems).each(function (index){
                    valuesArray.push($(this).attr('value'));
            });
            
            $.post('/ajax/get_subitems_view', {itemIds : valuesArray}, getSubitemsView, 'json');
        };
        
        
    }

    function getSubitemsView(json){
        if (json){
            if (json.code == 400) {
                $(filterObject.child.filterElement.field).hide();
            } else {
                $(filterObject.child.filterElement.filter_childcontent).html(json.view);
            }
        } else {
            alert('json');
        }
    }

    function onFilterLabelClick(){
        if (state() == 'active'){   
            state('disactive');
        } else {
            state('active');
        }
    }

    function onInputCheckboxClick(e){
        var countChecked = $(filterElement.filter_box).find(':checked').not(filterElement.filter_anyinput).length;
        if (countChecked == 0) {
             $(filterElement.filter_anyinput).attr('checked','checked').attr('disabled','disabled');
        } else {
             $(filterElement.filter_anyinput).removeAttr('checked').removeAttr('disabled');
        };
        updateChild();
    }

    function onAnyInputCheckboxClick(){
        $(filterElement.filter_checkboxInputs).removeAttr('checked');
        $(filterElement.filter_anyinput).attr('disabled','disabled');
        updateChild();
    }

    this.state = state;
    this.filterElement = filterElement;
    this.filterObject = filterObject;
}

function Filters() {
    
    var self = this;

    var _fieldsClasses = {
        filterClass : '.fn-filter',
        formId : '#fn-form-filter'
    }   

    var filters = {};

    initialize();


    function initialize(){
        _loadAlreadyLoadedFilters();
        _subscribeEvents();
    }

    function _subscribeEvents(){
        $(_fieldsClasses.formId).find('input[type="submit"]').bind('click', onSubmitForm);  
        $(_fieldsClasses.formId).find('.fn-clear').bind('click', onClearForm);  
    }

    function _loadAlreadyLoadedFilters(){
            
        $(_fieldsClasses.filterClass).each(function (index){
            var filter_identify = '#'+$(this).attr('id');

            _initFilter(filter_identify);

        });
    }

    function _initFilter(filter_identify){

        filters[filter_identify] = new Filter({filter_identify : filter_identify});

        if (filters[filter_identify].filterObject.parentFieldIdentify !== undefined){
            filters[filters[filter_identify].filterObject.parentFieldIdentify].filterObject.child = filters[filter_identify]; 
        }

        $(filter_identify).bind('setActiveEvent',onSetActiveEvent); 
        
    }

    function onSubmitForm(e){
        $(_fieldsClasses.formId).submit();  
    }

    function onClearForm(e){        
		var url = window.location.href.split("?")[0];
		var params_arr = window.location.href.split("?")[1].split('&');
		var params = [], pairs = [];
		
		for (i=0; i < params_arr.length; i++)
		{
			pairs = params_arr[i].split('=');
			params[pairs[0]] = pairs[1];			
		}
		
		if (params['user_id'] != undefined && !isNaN(parseInt(params['user_id'])))
			url = url + '?user_id=' + params['user_id'];

		document.location.href = url;
    }

    function onSetActiveEvent(e){
        disactiveAll();
    }

    function disactiveAll(){
        for (k in filters){
            if (filters[k].state() == 'active'){
                filters[k].state('disactive');
            }
        }  
    }

    this.disactiveAll = disactiveAll;
    this.filters = filters;
    
}

$(document).ready(function() {

    var ControlFilters = new Filters();

    $(document).on('click', function(e) {  
        var id = $(e.target).closest('.fn-filter').attr('id');
        if (id === undefined) {
            ControlFilters.disactiveAll();
        }
    });

    $('div.fn-smart-breadscrumbs').bind('click', onSmartBreadscrumsClick);

    function onSmartBreadscrumsClick(e){
        var params = window.location.href.split("?")[1].split("&");
        var url = window.location.href;
        if ($('div.fn-smart-breadscrumbs').length <= 1){
            document.location.href = window.location.href.split("?")[0];
            return;
        }
        var name = $(this).data('seo');
        var type = $(this).data('type');
        var value = $(this).data('value-min');
        var valueMax = $(this).data('value-max');
        if ((type == 'list') || (type == 'boolean') || (type == 'text')){
           var urlPartArray = name + '%5B%5D=' + value;  
           var urlPart = name + '=' + encodeURIComponent(value); 
           url = url.replace(urlPartArray, '').replace(urlPart, '');
           while (url.indexOf('&&')>0){
                url = url.replace('&&','&');
           }
           document.location.href = url.replace(urlPartArray, '').replace(urlPart, '');   
           return;
        } else if ((type == 'numeric') || (type == 'integer')){
            var urlPartMax = name + '%5Bmax%5D=' + valueMax; 
            var urlPartMin = name + '%5Bmin%5D=' + value; 
            url = url.replace(urlPartMax, '').replace(urlPartMin, '');
            while (url.indexOf('&&')>0){
                url = url.replace('&&','&');
            }
            document.location.href = url.replace(urlPartMax, '').replace(urlPartMin, ''); 
            return;
        } else if (type == 'map'){
            document.location.href = window.location.href.split("?")[0];
            return;
        }
    }

});
