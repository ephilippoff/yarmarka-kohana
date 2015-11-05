$(document).ready(function() {
	// раскрываем меню
//	$('#islide_profile').click();

	$('#link_to_company').click(function(){
		$.getJSON('/ajax/link_user/'+$('#link_to').val(), function(json){
			if (json.code == 200) {
				$('#error_block').find('p.text span').text('Запрос отправлен');
				$('#link_to').val('');
			} else {
				$('#error_block').find('p.text span').text(json.error);
			}
			$('#error_block').show();
		});
	});

	$('#remove_link').click(function(e){
		e.preventDefault();

		var obj = this;

		if (confirm('Удалить привязку к компании?')) {
			$.getJSON('/ajax/remove_link/'+$(this).data('user_id'), function(json) {
				if (json.code == 200) {
					$(obj).parents('article.article').remove();
				}
			});
		}
	});

	$('#cancel_link').click(function(e){
		e.preventDefault();

		var obj = this;

		if (confirm('Отменить запрос?')) {
			$.getJSON('/ajax/delete_link/'+$(this).data('id'), function(json) {
				if (json.code == 200) {
					$(obj).parents('article.article').remove();
				}
			});
		}
	});
});
