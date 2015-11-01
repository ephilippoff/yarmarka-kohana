$(document).ready(function() {
	$('#islide_services').click();

	$('#select_all').click(function(){
		var checkBoxes = $("input[name=to_del\\[\\]]");
		checkBoxes.attr("checked", !checkBoxes.attr("checked"));	
	});

	$('#delete_selected').click(function(){
		var to_del = $('input[name=to_del\\[\\]]:checked');

		if ( ! to_del.length) {
			return false;
		}

		if (!confirm('Удалить выделенное?')) {
			return false;
		}

		$.post('/ajax/delete_newspapers', to_del.serialize(),
			function(json){
				if (json.code == 200) {
					to_del.each(function(key, input){
						$(input).parents('div.li').remove();
					});
				}
			}, 'json'
		);
	});
});
