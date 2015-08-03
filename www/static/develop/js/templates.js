/*global define */
define(function(require){
    "use strict";
    return {
        searchPopup: require('lib/text!templates/searchPopup.tmpl'),
        filters: {
            listFilterItem: require('lib/text!templates/filters/listFilterItem.tmpl'),
            listBoxFilterItem: require('lib/text!templates/filters/listBoxFilterItem.tmpl'),
            numericFilterItem: require('lib/text!templates/filters/numericFilterItem.tmpl'),
            ilistFilterItem: require('lib/text!templates/filters/ilistFilterItem.tmpl'),
            textFilterItem: require('lib/text!templates/filters/textFilterItem.tmpl'),
            listPopup: require('lib/text!templates/filters/listPopup.tmpl'),
        }
    };
});