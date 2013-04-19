<?=HTML::script('js/jquery-1.9.1.js')?>
<?=HTML::script('bootstrap/js/bootstrap.min.js')?>
<script type="text/javascript" charset="utf-8">
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
		while ( e = re.exec(query) ) params[ decode(e[1]) ] = decode( e[2] );
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

});

function popup(obj) {
	var name = Math.random().toString(36).substr(2,16);
	var newWin = window.open($(obj).attr('href'), name, 'resizable=yes,scrollbars=yes');
	newWin.focus();

	return false;
}
</script>
