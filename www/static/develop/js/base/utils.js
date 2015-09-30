/*global define, window */
define([
        'jquery',
        'underscore',
        'jcookie'
        ], 
function ($, _) {
    "use strict";

    Date.prototype.addMonth = function(months) {
        this.setMonth(this.getMonth() + parseInt(months));
        return this;
    };

    Date.prototype.addHours = function(hours) {
        this.setHours(this.getHours() + parseInt(hours));
        return this;
    };

    return {
        getRemembedCity: function() {
            var city_id = null;
            var city_cookie = $.cookie("location_city_id");
            if (city_cookie) {
                city_id = parseInt(city_cookie.split('~')[1]);
            }
            return city_id;
        },
        parseQueryString: function(queryString) {
            if (!_.isString(queryString)) {
                return;
            }
            queryString = queryString.substring( queryString.indexOf('?') + 1 );
            var params = {};
            var queryParts = decodeURI(queryString).split(/&/g);
            _.each(queryParts, function(val) {
                    var parts = val.split('=');
                    if (parts.length >= 1)
                    {
                        val = undefined;
                        if (parts.length == 2) {
                            val = parts[1];
                            if (val.split(',').length > 1) {
                                val = val.split(',');
                            }
                        }
                        if (!params[parts[0]]) {
                            params[parts[0]] = val;
                        } else {
                            if (!_.isArray(params[parts[0]]))
                                params[parts[0]] = [params[parts[0]]];
                            params[parts[0]].push(val);
                        }
                    }
                });
            return params;
        },

        toQueryString: function(queryParams) {
            var i = 0, queryString = '';
            _.each(queryParams, function(value, key) {
                if (i !== 0) {
                    queryString += '&';
                }
                queryString += key + "=" + value;
                i++;
            });
            if (queryString !== '') {
                queryString = '?' + queryString;
            }
            return queryString;
        },

        priceFormat: function(_number) 
        {
            var rr,b;
            var decimal=0;
            var separator=' ';
            var decpoint = '.';
            var format_string = '# р.';
         
            var r=parseFloat(_number)
         
            var exp10=Math.pow(10,decimal);// приводим к правильному множителю
            r=Math.round(r*exp10)/exp10;// округляем до необходимого числа знаков после запятой
         
            rr=Number(r).toFixed(decimal).toString().split('.');
         
            b=rr[0].replace(/(\d{1,3}(?=(\d{3})+(?:\.\d|\b)))/g,"\$1"+separator);
         
            r=(rr[1]?b+ decpoint +rr[1]:b);
            return format_string.replace('#', r);
        },

        months: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],

        getFutureMonths: function(count) {
            count = count || 12;
            var now = new Date();
            var date = new Date();
            var i = 0;
            var result = [];
            while (i < count){
                var month = {};
                month.month = date.getMonth();
                month.realMonth = month.month + 1;
                month.monthName = this.months[month.month];
                month.year = date.getFullYear();
                month.fullDate = date.getFullYear()+"-"+(month.realMonth > 9 ? month.realMonth : '0'+month.realMonth)+"-01";
                month.showDate = "01."+(month.realMonth > 9 ? month.realMonth : '0'+month.realMonth)+"."+date.getFullYear();
                if (this.dayDiff(now, date) < 15) {
                    month.disabled = true;
                }

                result.push(month);
                date = date.addMonth(1);
                i++;
            }
            return result;
        },

        dayDiff: function(a, b) {
            var date1 = new Date(a);
            var date2 = new Date(b);
            return parseInt((date2 - date1) / (1000 * 60 * 60 * 24)); 
        },

        toLocaleDateTime : function(date) {
            return new Date(Date.parse(date));
        },

        iconPreload: function(list, $imgCont, defaultImg) {
            var i = 0;
            list = list || [];
            _.each(list, function(item){
                item = "/static/images/" + item;
                var image = document.createElement('img');
                image.onload = function () {
                     $imgCont.attr("src", item).attr("height", 25);
                     if (i == list.length-1 && defaultImg) {
                        $imgCont.attr("src", defaultImg);
                     }
                     i++;
                };
                image.error = function () {
                    i++;
                }
                image.src = item;
                
            });
        }

    };

});