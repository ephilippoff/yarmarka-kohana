$(document).ready(function() {
	// parseParams
	// var url = 'www.example.com?ferko=suska&ee=huu';
	// $.parseParams( url.split('?')[1] || '' ); // object { ferko: 'suska', ee: 'huu' }
	(function($) {
	var re = /([^&=]+)=?([^&]*)/g;
	var decodeRE = /\+/g;  // Regex for replacing addition symbol with a space
	var decode = function (str) { return decodeURIComponent( str.replace(decodeRE, " ") ); };
	$.parseParams = function(query) {
		var params = {}, e;
		while ( e = re.exec(query) ) { 
			params[ decode(e[1]).replace('?', '') ] = test = decode( e[2] );
		}
		return params;
	};
	})(jQuery);

	// in_array
	Array.prototype.in_array = function(p_val) {
		for(var i = 0, l = this.length; i < l; i++)	{
			if(this[i] == p_val) {
				return true;
			}
		}
		return false;
	}
	$(document).ajaxStart(function () {
		$('#loading').show();
	});

	$(document).ajaxStop(function () {
		$('#loading').hide();
	});
	
	// fix for remote modal windows(to not show same content for all links)
	// $('body').on('hidden', '.modal', function () {
	//   $(this).removeData('modal');
	// });

	// my implementation for twitter bootstrap modal, native script place content only in modal-body content
	$('a[data-toggle=modal]').click(function(e){
		var url = $(this).attr('href');
		var target = $(this).data('target');

		$(target).load(url, function(){
			$(target).modal({show:true});
		});
	});
});

function popup(obj) {
	var name = Math.random().toString(36).substr(2,16);
	var newWin = window.open($(obj).attr('href'), name, 'resizable=yes,scrollbars=yes');
	newWin.focus();

	return false;
}

function order(sort_by, direction) {
	var get = $.parseParams(window.location.search);
	get['sort_by'] = sort_by;
	get['direction'] = direction;
	window.location.search = decodeURIComponent($.param(get));
	return false;
}