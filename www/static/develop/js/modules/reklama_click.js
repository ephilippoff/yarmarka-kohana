define([ 'jquery' ], function ($) {

	return function () {
		$('.reklama-cont a').on('click', function (e) {

			var $el = $(this);
			if ($el.data('disableReklamaClickIncrement')) {
				$el.data('disableReklamaClickIncrement', false);
				return;
			}

			
			$.ajax({
				url: '/rest_reklama/click',
				type: 'POST',
				data: JSON.stringify({ id: $el.data('id') }), 
				success: function () {
					$el.data('disableReklamaClickIncrement', true);
					var x = function () { $el.removeAttr('target'); };
					var y = function () { $el.attr('target', '_blank'); };
					if (is_mobile_user_agent()) {
						x();
					} else {
						y();
					}
					$el[0].click();
				},
				dataType:'json'
			});
		});
	}

});