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
                backcall: require('lib/text!templates/windows/backcall.tmpl'),
                message: require('lib/text!templates/windows/message.tmpl'),
                editService: require('lib/text!templates/windows/editService.tmpl')
            },
            services: {
                up: require('lib/text!templates/services/up.tmpl'),
                premium: require('lib/text!templates/services/premium.tmpl'),
                lider: require('lib/text!templates/services/lider.tmpl'),
                buyObject: require('lib/text!templates/services/buyObject.tmpl'),
                kupon: require('lib/text!templates/services/kupon.tmpl'),
            },
            detail: {
                baloon: require('lib/text!templates/detail/baloon.tmpl'), 
            }
        }
    };
});