/*global define, window */
define([
        'jquery',
        'underscore'
        ], 
function ($, _) {
    "use strict";


    var validation = {
        validationMessages: {
            require: function  (n) { return "Обязательно для заполнения"; },
            requireint: function  (n) { return "Обязательно для заполнения"; },
            minlen: function  (n, c) { return "Минимум " + c + " символов"; },
        },

        validationMethods: {
            require: function  (value) { return ($.trim(value) != undefined && $.trim(value) != 0); },
            requireint: function  (value) { return (+value); },
            minlen: function  (value, length) { return (value.length >= length); }
        },

        isValid: function(rules, name, value) {
            var _rule, result, error = [];
            _.each(rules, function (rule){
                _rule = rule.split("_");
                result = validation.validationMethods[_rule[0]].apply(this, [value, _rule[1]]);
                if (!result) {
                    error.push(validation.validationMessages[_rule[0]].apply(this, [name, _rule[1]]));
                }
            });

            return (error.length) ? {name: name, text: error.join(", ")} : false;
        }
    }

    return validation;
 });