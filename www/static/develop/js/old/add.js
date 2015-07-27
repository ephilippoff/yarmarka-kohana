$(document).ready(function() {
		
	$('div.steps').find('ul').each(function() {$(this).css("display", "none")});
	$('div.steps').find('ul[data-parent-id=0]').css("display", "block");
	$('div.steps').find('li').click(function() 
	{
		$(this).parent().find('li.active').each(function() 
			{ 	
					deactivateLi($(this));
			});
		$(this).addClass('active');
		$('div.steps').find('ul[data-parent-id='+$(this).data("id")+']').css("display", "block");
	});

	
	
//	$('.banner').openxtag('spc', 99999);
	
      //alert($('#fast_rubric').val());
      var fast_rubric=$('#fast_rubric').val();
      var fast_action=$('#fast_action').val();
      if(fast_rubric > 1 && fast_action>0){
         $('.step-one a.lastrubric[href="/add/'+fast_rubric+'/'+fast_action+'"]').click(); 
         $('.step-one .button-next').click();      
      }
      
     aa=$(".step-one ul:first").offset();
     $(".step-one li").click(function(){$(".step-one ul").offset({top:aa.top});});
    
     $(document).click( function(event){
       if( $(event.target).closest(".button-send").length ) 
         return;
         $(".validity-tooltip").fadeOut("slow");
         event.stopPropagation();
     });
     
     $('.popup').hide();

	
	if ( document.getElementById('userfile_upload') != null )
	{

    new AjaxUpload('userfile_upload', {
            action: '/add/ajax_upload_file',
            name: 'userfile1',
            autoSubmit: true,
            //new!
            onSubmit: function(file, doc)
			{
                if (MAX_PHOTOS == uploaded_images_count) return false;
				$('.fn-wait').show();
				$("#error_userfile1").html('');
            },
            //
            onComplete: function(file, doc) {
                var data = doc;

                if (data) {
                    data = $.parseJSON(data);
                }

                if( data === null ) {
                    $("#error_userfile1").html('Произошла непредвиденная ошибка');
		    $('.fn-wait').hide();
                    return;
                }
				
                if(data.error) {
                    $("#error_userfile1").html(data.error);
		    $('.fn-wait').hide();
                    return;
                }

                if( !data.filename ) {
                    $("#error_userfile1").html('Произошла непредвиденная ошибка');
		    $('.fn-wait').hide();
                    return;
                }//

                $("#error_userfile1").html('');

                //new!
                  $('.fn-wait').hide();
                //
                
                //self.uploaded_images++;
				uploaded_images_count++;		
				active = '';
				if ($('.img-b').length == 0)
					active = 'active';

                var image = $('<img>').attr('src', data.filepaths['120x90'] ).attr('data-filename', data.filename);
				
				if ($('.img-b').length == 0)				
					$('input[name=active_userfile]').val(data.filename);
				
								
                var container = $('<div class="img-b"></div>');	
				
				var img_box = $('<div class="img '+active+'"></div>').append( image.click(function () 
				{
					$('div.img-b .img').each(function() {$(this).removeClass('active')});
					$(this).closest('div').addClass('active');
					$('input[name=active_userfile]').val($(this).attr('data-filename'));
				}));				
					
				var del_box = $('<div class="href-bl"></div>');					
					
				var elem_del =$('<span class="remove" href="">Удалить</span>').click(function()
				{

                    $.get('/add/ajax_remove_file', {filename : data.filename}, $.noop);
					
                    container.remove();
                    input.remove();
                    //self.uploaded_images--;
					uploaded_images_count--;
					if ($('div.img-b .img.active').length == 0)
					{
						if ($('div.img-b .img').length > 0)
						{
							$('div.img-b .img').first().addClass('active');
							$('input[name=active_userfile]').val($('div.img-b .img.active > img').attr('data-filename'));
						}
						else						
							$('input[name=active_userfile]').val('');						
					}
					
                    $('#addbtn').show();
		    
                    return false;
                });
								
				del_box.append(elem_del);											
				container.append(img_box);
				container.append(del_box);
				
                var input = $('<input>').attr({
                    type:"hidden",
                    name:"userfile[]",
                    value:data.filename
                });

                //container.append(elem_del);

                $('#add-block').prepend( container );

                $("#element_list").append( input );

                if ( MAX_PHOTOS == uploaded_images_count ) 
                    $('#addbtn').hide();
                	
            }
        });
		}
		
//Пока отключим		
//		$('img.hint').hover(
//		function()
//		{ 
//			hint_div = $('#'+$(this).attr('data-hint-box'));
//			hint_div.css({
//				position: "absolute",
//				left: ($(this).offset().left + $(this).width()) + "px",
//				top: $(this).offset().top + "px"
//			});
//			
//			hint_div.show(); 
//		}
//		,
//		function() 
//		{ 
//			hint_div = $('#'+$(this).attr('data-hint-box'));
//			hint_div.hide(); 
//		});
		
	 ApplyConditions(true);
	 
	$('input[type=checkbox][name^=param_]').change(function(){ApplyConditions(true);});
	$('select[name^=param_]').change(function(){ApplyConditions(true);});
	//$('(input:radio, input:checkbox)[data-param-name^=param_]').change(function(){ApplyConditions(true);});
	
	$("#title_adv").focus(function () {
        if ($("#title_adv").val() == '')
			FillCaption();
    });
	
	// load captcha for add advertisement
	if ($('#mini_registration_form #reg_captcha').length)
	{
		$('#mini_registration_form #reg_captcha').html(get_captcha('element_list'));
	}	
	
	// reload captcha button
	$('#element_list .captcha_reload').click(function(){
		$('#reg_captcha').html(get_captcha('element_list'));
		return false;
	});	
	
	// contacts verification
	
	requirejs(['modules/addlib'], function(){
	    var ControlContact = new ContactList();
	});
});



function AddFieldContact()
{	

	$('#add_form_informer').attr('count-load-contacts',$('#add_form_informer').attr('count-load-contacts')+1);
	item_id = $('#add_form_informer').attr('count-load-contacts');//$('#contacts2').find('select[id^=contact_type_select]').length+1;
	//item_id = parseInt($('#contacts2 > div').last().attr('data-item-id'))+1;
	if (isNaN(item_id))
		item_id = 1;
		
	template = $('#contact_item_template_id').get(0).outerHTML;
	template = replaceAll(template, "template_id", item_id);
	
	$('#contacts2').append(template);
	
	$('.contact-template .inp-cont').on('focus', '.inp', function(){$(this).closest('.inp').addClass('focus')})
	$('.contact-template .inp-cont').on('blur', '.inp', function(){$(this).closest('.inp').removeClass('focus')})	
	
	$('#contacts2 > div').last().show();
	
	$('#contacts2 .unputs_contact').first().attr('class','inputs_contact');
	
	mask_to_contact(item_id);
	
	max = $('#contacts').attr('data-max');
	item_count = $('#contacts2').find('select[id^=contact_type_select]').length+1;
	if (item_count >= max)
	{
		$('#miniform_show_button').hide();
	}

	var select = $('#contacts2').find('select').last();
			
				  $("#" + select.attr('id')).chosen({
												no_results_text: "Ничего не найдено", 
												allow_single_deselect: false})
										.change( function () { 
											change_type_contact(select.attr('id'));
										} );
					$("#" + select.attr('id') + "_chzn .chzn-search").hide();
		
		
}

function set_focus(val){
	$('#'+val).focus();
}

function isValidEmail (email, strict)
{
	 if ( !strict ) email = email.replace(/^\s+|\s+$/g, '');
	 return (/^([a-z0-9_\-]+\.)*[a-z0-9_\-]+@([a-z0-9][a-z0-9\-]*[a-z0-9]\.)+[a-z]{2,4}$/i).test(email);
}

function deactivateLi(li)
{
	$(li).removeClass('active'); 
	$('div.steps').find('ul[data-parent-id='+$(li).data("id")+']').each(function() 
		{ 
			$(this).css("display", "none"); 
			$(this).find('li.active').each(function() 
			{ 	
					deactivateLi($(this));
			});
		});
}

function load_cities_list(regionId)
{
	$('#city_selector').empty();
	html = '<option value="">Выберите...</option>';
	
	if (regionId)
	{
		$('#city_selector').empty();
		html = '<option value="">Выберите...</option>';
		
		$.ajax({
                url: '/add/ajax_get_cities/' + regionId,
                dataType: "json",
				async: false,
                success: function( data ) {
					for(i=0; i<data.length; i++) {
						html += '<option value="'+data[i].id+'">'+data[i].title+'</option>';
					}
				}
				});
	}
	$('#city_selector').html(html);
}


function CharLeft(obj)
{
var max_length = $(obj).attr('maxlength') ? $(obj).attr('maxlength') : 0;
if (max_length == 0) return;
var curr_length = $(obj).val().length;
var label_id = '#charleft_'+$(obj).attr('name');
if (curr_length == 0) 
	$(label_id).html('')
else   
	$(label_id).html('Осталось символов: '+String(max_length-curr_length));
}


function Submit(editMode)
{	
	$('.inputs_contact').each(function(index) {
		if  ($(this).val() == '' && $('.inputs_contact').length > 1){
			//$(this).parent().parent().remove();
			$(this).closest('.inp-add-cont.inp-add-cont').remove();
			$('#miniform_show_button').show();
		}
	});

		SaveObject();
		
		if (!$('#error_add_form').is(':visible'))
		{
			if ($('#contact_save_button').is(':visible') && $('#contact_value_input').val() != '')
			{
				$('#contact_save_button').click();
				item_count = $('#contacts').find('div[id^=contact_item_]').length;
				if (item_count == 0)
					return false;
			}
		
			jQuery(window).unbind("beforeunload");
			if (editMode === false)
			{
				if ($('#object_id').val())
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				document.location.href = '/detail/'+editMode;
				return false;
			}
		}
		else
		{
			var err_tags = $("div.input.error:visible").first().offset();
			if (err_tags != null){
				$(document).scrollTop($('div.input.error:visible').first().offset().top-100);
			}
		
			return false;
		}
	//}
}


function SaveObject()
{
	tinymce.triggerSave();
    var form = $('form#element_list');
    

	//TODO: сделать также инициализацию строки в боксе подсказки
	var error_fields = $('.input.star.error .alert-bl', form);
    error_fields.hide();
	$('.input.multiple_limit.error .alert-bl', form).hide();
    $('#error_add_form').hide();
	$('.input.star.error').removeClass('error');
	$('.input.multiple_limit.error').removeClass('error');

    $.ajax($('#save_object_url').val(), {
        type: "POST",
		async: false,
        data: $(form).serialize(),
        dataType: 'json',
        success: function(data){
        	if (data.code == 303) { // @todo bug пользователь не авторизован        		
        		$('#errors_on_page_text').html("При сохранении произошла непредвиденная ошибка");
        		$('#errors_on_page').show();
        	}
            if (data.error) {
            	var error_messages = [];
				$.each(data.error, function(i, row) {
					j = i;
					j = j.replace('_min', '');
                    j = j.replace('_max', '');

					$("#error_"+j+" div.cont p.text span").html(row);
					$("#error_"+j).show();					
					$("#error_"+j).parents('div.input.style2.star').addClass('error');
					$("#error_"+j).parents('div.input.style2.multiple_limit').addClass('error');
					error_messages.push(row);
				});
				$('#error_add_form').show();
				$('#errors_on_page_text').html("<p> Исправьте ошибки: </p><p>" + error_messages.join("; </br>")+"</p>");
				$('#errors_on_page').show();
			}
			else if (data.user_token)
			{
			    $.ajax('/ajax/auth_user_by_token/'+data.user_token, {
					async: false
				});
			}
			
			if (data.object_id)
			{
				$('#object_id').val(data.object_id);
			}
		}
    });
}




function ShowHidePopular(ref)
{
	if ($('div.list_element[data-ref='+ref+']').not('div.popular').first().is(":visible"))
	{
		$('div.list_element[data-ref='+ref+']').not('div.popular').hide();
		$('#expand_options_'+ref).text('Показать все');
	}
	else
	{
		$('div.list_element[data-ref='+ref+']').not('div.popular').show();
		$('#expand_options_'+ref).text('Скрыть');
	}
}

function CheckTheLimit(input)
{
	max_allowed = $(input).closest('div[data-max]').attr('data-max');
	name = $(input).attr('data-param-name');

	var checked = $("input[data-param-name="+name+"]:checked").size();

    if ( checked > max_allowed ) {
		$(input).attr("checked", false);
 
        alert("Максимальное количество значений, которые могут быть выбраны: " + max_allowed); 
		return false;
    }
	
	return true;
}

function GetChildrenAttribures(parent_id, parent_ref_value, checked)
{
	$('div[data-parent='+parent_id+'], select[data-parent='+parent_id+']').each(function(){$(this).empty()});

	if (!checked || parent_ref_value=='')
		return;
		
	$('div[data-parent='+parent_id+'], select[data-parent='+parent_id+']').each(function(){
		// AJAX-запрос
		url = '/add/ajax_get_attribute_element_list';
		
		// AJAX-запрос
		var params = {
			reference_id: $(this).attr('data-ref'),
			element_id  : parent_ref_value
		};
		
		var dataRecieved= function(elementToUpdate) {
			return function(data) {
				if($(elementToUpdate).is('select'))
				{
					$(elementToUpdate).empty();
					$(elementToUpdate).append('<option value=""></option>');
					for (i=0;i<data.length;i++)
					{
						$(elementToUpdate).append('<option value="'+data[i].id+'">'+data[i].title+'</option>');
					}
					$(elementToUpdate).trigger("liszt:updated");
				}
				if ($(elementToUpdate).is('div'))
				{
					$(elementToUpdate).empty();
					ref_id = $(elementToUpdate).attr('data-ref');
					max_value = $(elementToUpdate).attr('data-max');
					position = $(elementToUpdate).attr('data-position');
					type_value = $(elementToUpdate).attr('data-type');
					

					for (i=0;i<data.length;i++)
					{
						$elementHtml = '';
						class_value = position;
						style_value = '';

						if (data[i].is_popular == '1')
						{
							class_value += ' popular';
							
						}
						else
						{
							style_value = 'display: none;';
						}
							
						
						$elementHtml += '<div style="'+style_value+'" data-ref="'+ref_id+'" class="list_element '+class_value+'">';
						$elementHtml += '<input id="param_'+ref_id+'_'+i+'" type="'+type_value+'" data-seo-name="'+data[i].seo_name+'" data-param-name="param_'+ref_id+'" value="'+data[i].id+'"';
						if (max_value > 1) 
							$elementHtml += ' onChange="CheckTheLimit($(this)); UpdateListInput(\'param_'+ref_id+'\')"';
						$elementHtml += '><label for="param_'+ref_id+'_'+i+'">'+data[i].title+'</label></input></div>';

						$(elementToUpdate).append($elementHtml);
					}
					$(elementToUpdate).append('<input type="hidden" name="param_'+ref_id+'" value="">');
					$(elementToUpdate).append('<br><a id="expand_options_'+ref_id+'" href="javascript: ShowHidePopular('+ref_id+')">Показать все</a>');
				}
			};
		};

		$.post(
				url,
				params,
				dataRecieved($(this)),
				"json"
			);
	});
}


function replaceAll(str, str1, str2)
{
   var index = str.indexOf( str1 );
 
   while (index != -1)
   {
      str = str.replace(str1, str2);
 
      index = str.indexOf(str1);
   }
 
   return str;
}


function set_focus(val){
		$('#'+val).focus();
	}
	

function ShowAddVideoForm()
{
	$('#video_form').show();
	$('#add-video-line').hide();
	$('#video-error').hide();
	
}

function HideAddVideoForm()
{
	$('#add_video_form > textarea').val('');
	$('#add_video_form > div.add-video-error').hide();
	$('#video_form').hide();
	$('#add-video-line').show();
}

function SaveAddVideoForm()
{
	var video = $('#add_video_form .textarea > textarea').val();
	
	$("div#video-error").parents('div.input.style2').removeClass('error');	
	$.getJSON('/add/add_video',{video : video}, function(data) {
		if ( data.error ) 
		{
			$('#add_video_form > div#video-error div.cont p.text span').html(data.error)
			$('#add_video_form > div#video-error').show();
			$("div#video-error").parents('div.input.style2').addClass('error');
			return;
		}
		
		var a = $('<p class="mb20"><a href="#">Удалить</a></p>').click(function(){
                    $('#video_container .inp-cont').empty();
                    //$("#add_notice_video_button").show();
					$('#add-video-line').show();
					$('#add_video_form .textarea > textarea').val('');
                    
			// added NEW
			  ///video_height=$('#video_wrapper').height();
			  //video_height=video_height-iframe_height-20;
			  //$('#video_wrapper').height(video_height);
			//
			
			return false;
		});

		var input = $('<input type="hidden" name="video">').val(data.filename);
		var input2 = $('<input type="hidden" name="video_type">').val(data.type);

		$('#video_container .inp-cont').append(data.embed).append(a).append(input).append(input2);

		//$('#add_notice_video').find('.add-video-error').hide();


		//$("#add_notice_video_button").hide();
		
		// added NEW
		   //iframe_height=$('iframe').height();
		   //$('#video_wrapper').height(iframe_height+50);
		//$('iframe').css('margin-bottom','10px');
		//
		$('#video_form').hide();
    });
}

function UpdateListInput(name)
{
	value_for_all = '';
	$('input[data-param-name="'+name+'"]:checked').each(function()
	{
		if (value_for_all != '')
			value_for_all += ',';
		value_for_all += $(this).val();
	});
	$('input[name='+name+']').val(value_for_all);
}

function ApplyConditions(isOrCondition)
{
	var refs = [];
	
	$('input[type=checkbox][name^=param_]:checked').each(function()
	{
		var element = [];
		name = $(this).attr('name');
		element['ref_id'] = name.split('_')[1];
		element['value'] = 1;
		refs.push(element);
	});
	
	$('select[name^=param_]').each(function()
	{
		if ($(this).val() != '')
		{
			var element = [];
			name = $(this).attr('name');
			element['ref_id'] = name.split('_')[1];
			element['value'] = $(this).val();
			refs.push(element);
		}
	});
//  Отключаем(похоже уже не юзается)	
//	$('(input:radio, input:checkbox)[data-param-name^=param_]:checked').each(function()
//	{
//		var element = [];
//		name = $(this).attr('data-param-name');
//		element['ref_id'] = name.split('_')[1];
//		element['value'] = $(this).val();
//		refs.push(element);
//	});
	
	$('div[data-condition][data-condition!=""]').each(function()
	{
		if (isOrCondition)
			isShown = false;
		else
			isShown = true;
			
		conditions = $(this).attr('data-condition');
		pairs = conditions.split(',');

		for (i=0;i<pairs.length;i++)
		{
			tmp = pairs[i].split('_');
			ref = tmp[0];
			val = tmp[1];
			
			found = false;
			for (j=0;j<refs.length;j++)
			{
				if (refs[j]['ref_id'] == ref && refs[j]['value'] == val)
				{
					found = true;
				}
			}
			if (isOrCondition && found) isShown = true;
			if (!isOrCondition && !found) isShown = false;
		}

		if (isShown)
			$(this).show();
		else
		{
			$(this).hide();
			$(this).find('select').each(function () {$(this).find('option:selected').each(function(){this.selected=false;});});
			$(this).find('input').each(function () {$(this).prop('checked', false);$(this).val('');});
			$(this).find('textarea').each(function () {$(this).val('');});
			
		}
	});
}

function ShowMiniRegForm()
{
	$('#email_field').val('');
	$('#password_field').val('');

	$('#mini_authorization_form').hide();
	$('#mini_registration_form').show();
}

function ShowMiniAuthForm()
{
	$('#new_email_field').val('');
	
	$('#mini_authorization_form').show();
	$('#mini_registration_form').hide();
}


function FillCaption()
{
	template = $('#title_adv').attr('data-template');
	
	var n=template.match(/(\{.+?\})/g);
	
	if (n == null)
		return;
	
	var result = template
	for(i=0;i<n.length;i++)
	{
		element = n[i];
		seo_name = element.substring(1,element.length-1);
		
		value = '';
		
		$('input[data-seo-name="'+seo_name+'"]:checked').each(function() 
		{
			if (value != '')
				value += ', ';
			value += $('label[for="'+$(this).attr('id')+'"]').text();
		});
		
		$('select[data-seo-name="'+seo_name+'"] > option:selected').each(function() 
		{
			if (value != '')
				value += ', ';
			value += $(this).text();
		});
		$('input[type=text][data-seo-name="'+seo_name+'"]').each(function() 
		{
			if (value != '')
				value += ', ';
			value += $(this).val();
		});
		$('textarea[data-seo-name="'+seo_name+'"]').each(function() 
		{
			if (value != '')
				value += ', ';
			value += $(this).val();
		});
		
		
		result = result.replace(element,value);
	}
	var max = parseInt($("#title_adv").attr('maxlength'));
	if (result.length > max)
		result = result.substring(0,max);
	$('#title_adv').val(result);
}

