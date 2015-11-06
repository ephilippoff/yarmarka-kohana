$(document).ready(function() {
	$('#islide_myads').click();

	$('#region_id').change(function(){
		if ($(this).val() == '') {
			$('#city_id option').removeAttr('selected');
		}
	});

	$('#region_id, #city_id').change(function(){
		$('#ads_filter').submit();
	});

	$('.btn-favorite').click(function(e){
		e.preventDefault();

		var obj = this;
		$.getJSON('/ajax/remove_from_user_favorites/'+$(this).data('id'), function(json){
			if (json.code == 200) {
				$(obj).parents('div.li').remove();
			}
		});
	});
});
