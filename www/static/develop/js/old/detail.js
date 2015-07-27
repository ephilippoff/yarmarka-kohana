$(function(){

	//Обработчик для нового макета
	$('.fn-show-contacts').click(function(){
		var obj = this;
		var contact_table = $('.fn-contacts-cont');

		$.post('/ajax/object_contacts_n/'+$(this).data('id'), function(html){
			$(html).insertAfter(contact_table);
			contact_table.remove();
			$(obj).hide()
		});
	});	
	
	$('.show-cont-bl').click(function(){
		var obj = this;
		var contact_table = $(this).closest('.contact-bl').find('.contact-bl-info');

		$.post('/ajax/object_contacts/'+$(this).data('id'), function(html){
			$(html).insertAfter(contact_table);
			contact_table.remove();
			$(obj).hide()
		});
	});		
	
    $('.answerButton').click(function(){

		var commentIdInput = $(this).parent('p').find('input:hidden').clone();
		var text = $("textarea[name=text]").val();
		var username = $("input[name=username]").val();

		var addCommentPlace = $('#add_comment_place').clone().wrap('<div></div>').parent();

		addCommentPlace.find('form').append(commentIdInput);

		var ht = addCommentPlace.html();

		$('#add_comment_place').remove();
//		$(this).parent('p').parent('.comment_area').parent('.comment').after(ht);
		$(this).closest('.comment').after(ht);
//		$('#comments_place').after(ht);

		$('#add_comment').submit(comment_form_submit);

		$('#add_comment_place').find('.cancel').show();

		if (hideAnswerButton != null)
			$(hideAnswerButton).show();

		hideAnswerButton = this;

		$(this).hide();

		$("textarea[name=text]").keyup(countRemainingChars);
		$("textarea[name=text]").val(text);
		$("input[name=username]").val(username);
		
		//input focus
		$('.inp-cont').on('focus', '.inp', function(){$(this).closest('.inp').addClass('focus')})
		$('.inp-cont').on('blur', '.inp', function(){$(this).closest('.inp').removeClass('focus')})
		//textarea focus
		$('.input').on('focus', 'textarea', function(){$(this).closest('.textarea').addClass('focus')})
		$('.input').on('blur', 'textarea', function(){$(this).closest('.textarea').removeClass('focus')})
});

	$('#add_comment').submit(comment_form_submit);
});

$(document).click( function(event){
      if( $(event.target).closest(".send-comment").length ) 
        return;
      $(".validity-tooltip").fadeOut("slow");
      event.stopPropagation();
});

function return_comment_form()
{
	var addCommentPlace = $('#add_comment_place').clone().wrap('<div></div>').parent();
	
	var text = $("textarea[name=text]").val();
	
	var username = $("input[name=username]").val();
	
	$('#add_comment_place').remove();
	
	$('#comments_place').before(addCommentPlace.html());
	
	if (hideAnswerButton != null)
		$(hideAnswerButton).show();
		
	$('#add_comment').find('input[name=commentId]').remove();
	
	$('#add_comment_place').find('.cancel').hide();
	
	$('#add_comment').submit(comment_form_submit);
	
	$("textarea[name=text]").keyup(countRemainingChars);
	$("textarea[name=text]").val(text);
	
	$("input[name=username]").val(username);
	
	
		
	if ($("input[name=username]").val() != 'Ваше имя' && $("input[name=username]").val() != '')
		$("input[name=username]").attr('class', '');
	else
		$("input[name=username]").attr('class', 'mcolor2');
		
	if ($("input[name=email]").val() != 'Ваш e-mail' && $("input[name=email]").val() != '')
		$("input[name=email]").attr('class', '');
	else
		$("input[name=email]").attr('class', 'mcolor2');
		
	if ($("input[name=phone]").val() != 'Ваш телефон (только автору)' && $("input[name=phone]").val() != '')
		$("input[name=phone]").attr('class', '');
	else
		$("input[name=phone]").attr('class', 'mcolor2');			
}



function favorites(is_favor,id_object){
    objRef={};
    objRef.action="change_favorites";
    objRef.favor=is_favor;
    if(is_favor==1){
        text="Удалить объявление из избранного?";
    }
    else{
        text="Добавить объявление в избранное?";
    }
    objRef.id_object=id_object;
    searchParams=JSON.stringify(objRef);

    if (true){
        $.post('/search/ajax','params='+searchParams,function(data){
            if(data=="add_ok"){
                $('.favor-text').text('Удалить из избранного');
                $('[data-favor]').attr('data-favor', 1);
            }
            if(data=="del_ok"){
                $('.favor-text').text('Добавить в избранное');
                $('[data-favor]').attr('data-favor', 0);
            }
        });
    }
}


function comment_form_submit(){

    $.validity.start();
	$("input[name=username]", this)
       .require('Вы не указали имя')
	   .match(/[^Ваше имя]/, 'Вы не указали имя');
    $("input[name=email]", this)
       .require('Вы не указали E-Mail')
	   .match(/[^Ваш e\-mail]/, 'Вы не указали E-Mail')
	   .match('email');
	
	if ($("input[name=phone]").val() != 'Ваш телефон (только автору)')
	{	
		$("input[name=phone]", this)
			.maxLength(15, "Телефон должен быть не более 15 символов.");   
	}
	
	$("textarea[name=text]", this)
       .require('Нужно ввести текст вопроса')
	   .match(/[^Ваш вопрос продавцу]/, 'Нужно ввести текст вопроса')
	   .match(/[^Комментарий]/, 'Нужно ввести текст вопроса')
	   .minLength(2, "Текст вопроса должен быть более 2 символов.")
	   .maxLength(100, "Текст вопроса должен быть не более 100 символов.");   
	 
    
    var form = $.validity.end();

	if (form.valid) {
	
		if ($("input[name=phone]").val() == 'Ваш телефон (только автору)')
			$("input[name=phone]").val('');
		
		$("#loading").show();

		$.post($(this).attr('action'), $(this).serialize(),
				function(json){

				$("textarea[name=text]").attr('class', 'mcolor2');
				
				if (json.error)
				{
					
				}
				else
				{
					if (json.parent_id)
					{
						var comment = "<div class=\"li-cont answer\">";
						
						comment += "<div class=\"aside\">";
						
						comment += "<span class=\"createdOn\">"+json.createdOn+"</span>";
						
						comment += "</div>";	
						
						comment += "<div class=\"cont\">";
						
						comment += "<div class=\"answerCaption\">";
						comment += "<a class=\"delete_comment\" onclick=\"delete_comment(";
						comment += json.comment_id;
						comment += ", this)\"></a> &nbsp;";
						comment += json.user_name;
						comment += "</div>";
						
						comment += "<p class=\"answer\" id=\"text-comment-"
						comment += json.comment_id;
						comment += "\">"; 
						comment += "&mdash;&nbsp;"+json.text;
						comment += "</p></div></div>";
						
						$("#comment-" + json.parent_id).closest('.li-cont').after(comment);
					}
					else
					{
						var comment = "<div class=\"comment\">";
						comment += "<div class=\"comment_area\">";
						comment += "<div class=\"li-cont\">";
						
						comment += "<div class=\"aside\">";
						
						comment += "<span class=\"createdOn\">"+json.createdOn+"</span>";
						
						comment += "</div>";						
						
						comment += "<div class=\"cont\">";
						
						comment += "<span class=\"user_name\" href=\"#\" >"+json.user_name+"</span>";
						
						
						
						if (json.user_id != null)
						{
							comment += "<a class=\"delete_comment\" onclick=\"delete_comment(";
							comment += json.comment_id;
							comment += ", this)\"></a>";
						}
						

						comment += "<p id=\"text-comment-";
						comment += json.comment_id;
						comment += "\">";
						comment += "&mdash;&nbsp;"+json.text;
						comment += "</p></div></div></div></div>";

						$("#comments_place").append(comment);
					}
				}
				//$("#loading").hide();
				$('#add_comment').trigger( 'reset' );
				return_comment_form();
				$('#remaining_characters_count').html('Осталось 100 знаков');
				}, 'json');
			}
			
	return false;
}

function delete_message_block(control, userId, objectId)
{
	$(control).remove();

	$.post('/ajax/block_message_delete', {user_id: userId, object_id: objectId},
				function(data){
					//alert(data);
				});
}

function block_message(userId, objectId, userName)
{
	$("div.block_message").remove();

	$.post('/ajax/block_messages', {user_id: userId, object_id: objectId},
				function(data){
					if (userId == null && objectId == null)
					{
						if ($("#comments_place").children(".notice").find("#block_all_comments").html() == null)
						{
							$("#comments_place").children(".notice").prepend('<p><a id="block_all_comments">Заблокированы сообщения для всех объявлений [x]</a></p>');
							
							$('#block_all_comments').click(function(){delete_message_block(this, null, null);});
						}
					}
					if (userId == null && objectId != null)
					{
						if ($("#comments_place").children(".notice").find("#block_comments_object").html() == null)
						{
							if ($("#comments_place").children(".notice").find("#block_all_comments").html() == null)
								$("#comments_place").children(".notice").prepend('<p><a id="block_comments_object">Заблокированы сообщения для этого объявления [x]</a></p>');
							else
								$("#comments_place").children(".notice").find("#block_all_comments").after('<p><a id="block_comments_object">Заблокированы сообщения для этого объявления [x]</a></p>');
								
							$('#block_comments_object').click(function(){delete_message_block(this, null, $('#add_comment').find('input[name=objectId]').val());});
						}
					}
					if (userId != null)
					{
							if ($("#comments_place").children(".notice").find("#blocked-user-"+userId).html() == null)
							{
								$("#comments_place").children(".notice").append('<p><a id="blocked-user-'+userId+'" class="blocked_user">Заблокированы сообщения от ' + userName + '[x]</a></p>');
								
								$('.blocked_user').click(function(){delete_message_block(this, $(this).attr('id').substr(13), null);});
							}
					}
				});
}

var  hideAnswerButton = null;



function delete_comment(commentId, delete_link)
{
	if (window.confirm('Вы действительно хотите удалить комментарий?'))
	{
		$.post('/ajax/delete_comment', {comment: commentId},
				function(data){
				
					var message = "<i>Комментарий удален</i>";
					
					if (data == "author")
						message = "<i>Комментарий удален автором</i>";
						
					$("#text-comment-"+commentId).html(message);
					$(delete_link).remove();
				});
	}
}


$(function() {
	$("textarea[name=text]").keyup(countRemainingChars);
});

function countRemainingChars(){
	var maxchars = 100;
	var number = $("textarea[name=text]").val().length;
	if(number <= maxchars){
		var remaining_characters_count = maxchars - number;
		$("#remaining_characters_count").html("Осталось " + remaining_characters_count + " знаков");
	}
}




$(document).ready(function() {
	
$('#block_all_comments').click(function(){
	delete_message_block(this, null, null);
});

$('#block_comments_object').click(function(){
	delete_message_block(this, null, $('#add_comment').find('input[name=objectId]').val());
});

$('.blocked_user').click(function(){
	delete_message_block(this, $(this).attr('id').substr(13), null);
});

$('.block').click(function(){

	$("div.block_message").remove();
	
	var offset1 = $(this).offset();

	var userId = null;
	
	if ($(this).attr('id'))
		userId = $(this).attr('id').substr(11);
	
	var objectId = $('#add_comment').find('input[name=objectId]').val();
	
    var data  = '<div class="block_message" style="top:'+(offset1.top+0)+'px;left:'+(offset1.left+40)+'px;display:none;">'+
	'<p class="block_message-head">Заблокировать сообщения</p>';
	if (userId != null)
        data += '<p><a onclick="block_message(\''+userId+'\',\'' +objectId+'\', \'' + this.innerHTML.trim() +'\')">от пользователя</a></p>';
	data += '<p><a onclick="block_message(null,' +objectId+', null)">от всех пользователей</a></p>'+
	'<p><a onclick="block_message(null, null, null)">для всех моих объявлений</a></p>'+
	'</div>'

    $(this).after(data);
    $("div.block_message").show();
   
    $(document).click(function(event) {
       if ($(event.target).closest("div.block_message").length) return;
       $("div.block_message").hide("slow");
       $("div.block_message").remove();
       event.stopPropagation();
    });
   
    return false;
});	

// complaint form
$('#complaint_form').submit(function(e){
	e.preventDefault();
	var obj = this;
	$.post('/ajax/complaint/'+$(this).data('id'), $(this).serialize(), function(json){
		if (json.code == 200) {
			$('#complaint_captcha').html(get_captcha('complaint_form'));
			$(obj).find('textarea[name=text]').val('');
			$(obj).find('input[name=captcha]').val('');
			$(obj).closest('.popup').fadeOut();
			$('.popup-layer').fadeOut();
			$(obj).find('div.error').hide();
		} else {			
			$(obj).find('div.error .cont p').remove();
			$(obj).find('div.error .cont').append(json.error);
			$(obj).find('div.error').show();
		}

	}, 'json');
	return false;
});
})

function pub_toggle (id) {
	$('.visible_loader').show();
	$.post('/ajax/pub_toggle', {id:id},
		function(json){
			if (json.code == 200) {
				if (json.is_published == 1) 
					$('.pub_toggle_link_'+id).html('Снять');
				else 
					$('.pub_toggle_link_'+id).html('Разместить');
				
			} else {
				alert('Вы не можете разместить объявлений в эту рубрику. На этой рубрике существует ограничение на количество объявлений, поданных частным лицом. Вы можете снять более старые объявлений и разместить более новые.');
			}
			$('.visible_loader').hide();
		}, 'json'
	);
		
	
	return false;
}

function prolong_obj(id) {

    $.post('/ajax/prolong_object/'+id, {lifetime:$('.prolong_'+id).val()},
        function(json){
            if (json.code == 400) {
                $('.status'+id).html('Произошла ошибка. Попробуйте продлить объявление позднее.');
            }
            if (json.code == 200) {

                $('.status'+id).html('Ваше объявление было продлено до ' + json.date_expiration + ' и  поднято.');
                $('.prolong_'+id).attr('disabled', 'disabled');               
            }
            if (json.code == 300) {

                window.location = '/user/edit_ad/' + id;
            }
        }, 'json'
    );
    return false;
}