$(document).ready(function() {
	$('#islide_subscriptions').click();

	// change period
	$('.mysub .period li').click(function(){
		var obj = this;

		$.post('/ajax/change_subscription_period/'+$(this).data('id'), {period:$(this).data('period')}, function(json){
			if (json.code == 200) {
				$(obj).closest('ul').hide();
				$(obj).closest('.period').find('span').text($(obj).text());
			}
		}, 'json');
	});

	$('.unsubscribe').click(function(e){
		e.preventDefault();

		var obj = this;

		$.getJSON('/ajax/unsubscribe/'+$(this).data('id'), function(json){
			if (json.code == 200) {
				$(obj).parents('div.li').remove();
			}
		});
	});
});
