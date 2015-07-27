(function( $ ){

    var options = {
        banners_in_queue: '#extra_banners',
        rotations_at_time: 2,
        timeout: 3000
    };

    var data = {};

    var methods = {
        init : function (settings) {
            options = $.extend({}, options, settings, this.data());

            data.existing_banners = _.shuffle(this.find('a'));
            data.banners_in_queue = _.shuffle($(options.banners_in_queue).find('a'));

            if (data.banners_in_queue.length > 0) {
                methods.rotate();
            }
        },
        rotate : function () {
            // rotate banners
            var interval_id = setInterval(function(){
                var in_rotation = 0;
                var rotations_at_time = options.rotations_at_time;
                if (rotations_at_time > Math.min(data.existing_banners.length, data.banners_in_queue.length)) {
                    rotations_at_time = Math.min(data.existing_banners.length, data.banners_in_queue.length);
                }
                while (in_rotation < rotations_at_time) {
                    var old_banner = data.existing_banners.shift();
                    var parent_div = $(old_banner).parent('div');
                    var new_banner = data.banners_in_queue.shift();

                    // add old banner to queue
                    data.banners_in_queue.push(old_banner);
                    // add new banner to existing
                    data.existing_banners.push(new_banner);

                    $(old_banner).css('display', 'inline-block');
                    $(new_banner).css('display', 'none');
                    parent_div.append(new_banner);
                    $(old_banner).slideUp('slow', methods.show_banner(new_banner, old_banner));

                    in_rotation++;
                }
            }, options.timeout);
        },
        show_banner: function(new_banner, old_banner) {
            $(new_banner).slideDown('slow');
            $(options.banners_in_queue).append(old_banner);
        }
    };

    $.fn.banners_rotation = function(method) {
        if ( methods[method] ) {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error('Метод "' +  method + '" не найден');
        }
    };
})( jQuery );
// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

// Place any jQuery/helper plugins in here.