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
        },
        components: {
            windows: {
                service: require('lib/text!templates/windows/service.tmpl'),
            },
            services: {
                up: require('lib/text!templates/services/up.tmpl'),
                premium: require('lib/text!templates/services/premium.tmpl'),
                lider: require('lib/text!templates/services/lider.tmpl'),
                buyObject: require('lib/text!templates/services/buyObject.tmpl'),
            },
            detail: {
                baloon: require('lib/text!templates/detail/baloon.tmpl'), 
            }
        }
    };
});