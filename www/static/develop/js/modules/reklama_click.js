define([ 'jquery' ], function ($) {

	return function () {
		$('.reklama-cont a').on('click', function (e) {
			var $el = $(this);
			if ($el.data('disableReklamaClickIncrement')) {
				$el.data('disableReklamaClickIncrement', false);
				return;
			}
			e.preventDefault();
			
			$.ajax({
				url: '/rest_reklama/click',
				type: 'POST',
				data: JSON.stringify({ id: $el.data('id') }), 
				success: function () {
					$el.data('disableReklamaClickIncrement', true);
					$el[0].click();
				},
				dataType:'json'
			});
		});
	}

});