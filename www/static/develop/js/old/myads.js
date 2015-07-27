$(document).ready(function() {
	$('#islide_myads').click();

	$('#region_id').change(function(){
		if ($(this).val() == '') {
			$('#city_id option').removeAttr('selected');
		}
	});

	$('#city_id, #category_id, #region_id').change(function(){
		$('#ads_filter').submit();
	});

	$('#select_all').click(function(){
		var checkBoxes = $("input[name=to_del\\[\\]]");
		checkBoxes.attr("checked", !checkBoxes.attr("checked"));	
	});

	$('#delete_selected').click(function(){
		var to_del = $('input[name=to_del\\[\\]]:checked');

		if ( ! to_del.length) {
			return false;
		}

		if (!confirm('Удалить выделенные объявления?')) {
			return false;
		}

		$.post('/ajax/delete_ads', to_del.serialize(),
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

function load_moderator_comments(object_id, obj){
    $.getJSON('/ajax/load_moderator_comments/'+object_id, function(json){
		if (json.code == 200) {
			$(obj).parents('span.comments_block').html(json.html);
		}
    });
}

function service_up(id, obj)
{
    $.getJSON('/ajax/service_up/'+id, function(json){
            var text = "";
            if (json.code == 300) {
                text = "Вы можете поднять это объявление не раньше " + json.date_service_up_available;
            }
            if (json.code == "error") {
                alert("Вы не можете поднять это объявление. Исправьте ошибки : "+json.errors);
            }
            if (json.code == 200) {
                text = "Объявление поднято. Следующее поднятие не раньше " + json.date_service_up_available;
            }

            change_status(obj, text);
            $("#service-up-"+id).removeClass('active').addClass('noactive').attr('onClick', 'return false;')

        });
}

function pub_toggle (id, obj) {
    $.getJSON('/ajax/pub_toggle/'+id+'?'+Math.floor(Math.random() * 100),
        function(json){
            if (json.code == 200) {
                if (json.is_published == 1) {
                    $('#pub_toggle_link_'+id).find('span').html('Снять');
					$('#pub_toggle_link_'+id).find('i.ico').removeClass('show hide').addClass('hide');
					$('#pub_toggle_link_'+id).attr('title','Снять объявление с публикации');	
					$(obj).parents('.li').removeClass('blocked');
					change_status(obj, 'Размещено');
                }
                else {
                    $('#pub_toggle_link_'+id).find('span').html('Разместить');
					$('#pub_toggle_link_'+id).find('i.ico').removeClass('show hide').addClass('show');
					$('#pub_toggle_link_'+id).attr('title','Разместить объявление в публикацию');
					$(obj).parents('.li').addClass('blocked');
					change_status(obj, 'Снято');
                }
            } else {
                alert("Вы не можете опубликовать это объявление. Исправьте ошибки : "+json.errors);
            }
        }
    );
    return false;
}

function delete_ad(id, obj) {
    if (!confirm('Удалить объявление?')) {
        return false;
    }
    $.post('/ajax/delete_ads', {to_del:[id]},
        function(json){
            if (json.code == 200) {
				$(obj).parents('div.li').remove();
            }
        }, 'json'
    );
    return false;
}

function fix_ad(id, obj)
{
    $.getJSON('/ajax/fix_object/'+id,
        function(json){
            if (json.code == 200) {
                window.location.href = $(obj).data('url');
            } else {
                alert('Выполнить операцию не удалось');
            }
        }
	);
}

function change_status(obj, status) {
	$(obj).parents('.top-bl').find('.col1 .number span').text(status);
}

function prolong(id) {

    $.post('/ajax/prolong_object/'+id, {lifetime:$('#prolong_'+id).val()},
        function(json){
            if (json.code == 200) {

                //$('#status'+id).html('Ваше объявление было продлено до ' + json.date_expiration + ' и  поднято.');				
                $('#prolong_'+id).attr('disabled', 'disabled');
                $("#prolong-btn"+id).removeClass('active').addClass('noactive').attr('onClick', 'return false;')
				change_status("#prolong-btn"+id, 'Ваше объявление было продлено до ' + json.date_expiration + ' и  поднято.');
            } else {
                alert("Вы не можете продлить это объявление. Исправьте ошибки : "+json.errors);
            }

        }, 'json'
    );
    return false;
}

function premium(id, obj) {
    $.post('/ajax/premium_object/'+id, {},
        function(json){
            if (json.code == 200) {
                $('#premium'+id).attr('disabled', 'disabled');
                $("#premium-btn"+id).removeClass('active').addClass('disable').addClass('noactive').attr('onClick', 'return false;').attr("")
                $("#premium-btn"+id).closest(".li").addClass("premium");
                alert('Объявление добавлено в "Премиум" на 7 дней и поднято.');
                $('#fn-premium-balance').text(json.count);
            } else {
                alert("Лимит Премиум услуг израсходован");
                window.location.href = $(obj).data('url');
            }

        }, 'json'
    );
    return false;
}