
$(document).ready(function(){

	var key = $.cookie("cartKey");
	if (key) {
		updateCart();
		setInterval(function() {
			updateCart();
		}, 10000);
	}

	function updateCart() {
		$.post('/ajax/cart_count', function(response){
			var respJson = JSON.parse(response);
			$(".fn-cartCounter").text(respJson.count);
		});
	}
	
});