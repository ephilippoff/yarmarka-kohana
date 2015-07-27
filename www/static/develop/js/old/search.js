(function($) {
 var re = /([^&=]+)=?([^&]*)/g;
 var decodeRE = /\+/g;  // Regex for replacing addition symbol with a space
 var decode = function (str) {return decodeURIComponent( str.replace(decodeRE, " ") );};
 $.parseParams = function(query) {
 var params = {}, e;
 while ( e = re.exec(query) ) params[ decode(e[1]) ] = decode( e[2] );
 return params;
 };
 })(jQuery);

$(document).ready(function() {
   $('.str_wrap').liMarquee();
});

function change_search_view (view, obj) {
	var query = $.parseParams(window.location.search.replace('?', ''));

	query['view_type'] = view;
	window.location.search = $.param(query);

	return false;
}

function make_search(params) {
	var query = $.parseParams(window.location.search.replace('?', ''));

	var reserved_params = ['time_interval', 'source', 'photo', 'video'];

	var change_href		= false;
	var change_query	= false;


	var time_interval = 4;
	if (typeof(query.time_interval) != 'undefined') {
		var time_interval = query.time_interval;
	}

	var source = 0;
	if (typeof(query.source) != 'undefined') {
		var source = query.source;
	}

	var photo = 0;
	if (typeof(query.photo) != 'undefined') {
		var photo = query.photo;
	}

	var video = 0;
	if (typeof(query.video) != 'undefined') {
		var video = query.video;
	}

	// search by params
	if (params) {
		$.each(query, function(key){
			if ($.inArray(key, reserved_params) == -1) {
				delete query[key];
			}
		});

		$.each(params, function(key, ref){
			query[ref.seo_name] = ref.value;
		});
		query.search_by_params = 1;

		var url_array = $.url().attr('path').split('/');
		$.each(url_array, function(key, value){
			if (value == "" || /page_[0-9]+/.test(value)) {
				url_array.splice(key, 1);
				change_href = true;
			}
		});

		change_query = true;
	}

	// change time interval
	if (time_interval != $('[name=select-date]').val()) {
		query['time_interval'] = $('[name=select-date]').val();

		change_query = true;
	}
	// change source
	if (source != $('[name=select-source]').val()) {
		query['source'] = $('[name=select-source]').val();

		change_query = true;
	}
	// change with photo
	var new_photo = $('[name=photo]').is(':checked') ? 1 : 0;
	if (photo != new_photo) {
		query['photo'] = new_photo;

		change_query = true;
	}
	// change with video
	var new_video = $('[name=video]').is(':checked') ? 1 : 0;
	if (video != new_video) {
		query['video'] = new_video;

		change_query = true;
	}



	if (change_href) {
		window.location.href = '/'+url_array.join('/')+'?'+$.param(query);
	} else if (change_query) {
		window.location.search = $.param(query);
	}
}

function perform_windows_search(src) {

    var query = $.parseParams(window.location.search.replace('?', ''));

    var newDomain = '';
    var href = window.location.href;
    href = href.split("?")[0];
    var pathname = href.split('/');
    pathname.splice(0,3);

    var source_element = $(src).attr('id');
    //Сменена ли география поиска
    var is_new_geo = false;
    var search_region = 0;
    var search_city = 0;

    //Выявление источника инициализации поиска
    //Список в панели фильтров
    if (source_element == 'city-list')
    {
        search_city   = $('#filter_box select#city-list').val();
        search_region = $('#filter_box select#region-list').val();
        //Костылек для подгонки под имеющуюся логику
        if (search_city != 0) search_region = 0;

        is_new_geo = true;
    }


    if (is_new_geo)
    {
        var newRegionSeoName = pathname[0];

        $.ajax({
            url: '/search/get_location_name',
            type: 'POST',
            data: {region_id:search_region, city_id:search_city},
            dataType: "json",
            async: false,
            success: function( data, status ) {
                if (data.seo_name)
                    newRegionSeoName = data.seo_name;
            },
        });

        $.ajax({
            url: '/indexpage/ajax_set_location',
            data: {region_id:search_region, city_id:search_city},
            dataType: "json",
            async: false,
            success: function( data, status ) {

            },
        });

        if (newRegionSeoName != pathname[0])
        {
            pathname[0] = newRegionSeoName;

            $.ajax({
                url: '/indexpage/ajax_check_region_subdomain',
                type: 'POST',
                data: {city_id:search_city},
                dataType: "json",
                async: false,
                success: function( data, status ) {
                    if (data.url)
                        newDomain = data.url;
                },
            });
        }
    }

    var source = $('#options_window').find("input[name='source'][type='radio']:checked").val();
    if (source == undefined)
        source = 0;
    if (source != 0)
        query['source'] = source;
    else
        delete query['source'];

    var org_type = $('#options_window').find("input[name='org_type'][type='radio']:checked").val();
    if (org_type == undefined)
        org_type = 0;
    if (org_type != 0)
        query['org_type'] = org_type;
    else
        delete query['org_type'];

    if ($('#options_window').find("input[name='photo']").is(':checked'))
        query['photo'] = 1;
    else
        delete query['photo'];

    if ($('#options_window').find("input[name='video']").is(':checked'))
        query['video'] = 1;
    else
        delete query['video'];

    var names = new Array();
    a = 0;
    $('#options_window').find('*[data-name]').each(function()
    {
        name = $(this).attr('data-name');


        if ($.inArray(name, names) == -1)
        {
            names[a] = name;
            a++;
        }

    });

    for(i=0;i<names.length;i++)
    {
        delete query[names[i]];
        delete query[names[i]+'[min]'];
        delete query[names[i]+'[max]'];
    }

    a = 0;
    arrRef=new Array();
    $('#options_window').find('*[data-name]').each(function()
    {
        if ($(this).is(':checked') == true && ($(this).attr('type')=='radio' || $(this).attr('type')=='checkbox'))
        {
            arrRef[a]={};
            arrRef[a].id=$(this).attr('data-name');
            arrRef[a].value=$(this).attr('data-value');
            arrRef[a].type = $(this).closest('div').attr('rtype');
            if (arrRef[a].value == undefined)
                arrRef[a].value = 1;

            a++;
        }
        if ($(this).val() != '' && $(this).val() != undefined && ($(this).attr('type')=='text'))
        {
            arrRef[a]={};
            arrRef[a].id=$(this).attr('data-name');
            arrRef[a].value=$(this).val();
            arrRef[a].type = $(this).closest('div').attr('rtype');

            if (arrRef[a].type == 'integer' || arrRef[a].type == 'numeric')
            {
                if (arrRef[a].id.indexOf('[min]') == -1 && arrRef[a].id.indexOf('[max]') == -1)
                {
                    arrRef[a].id = arrRef[a].id+'[min]';

                    a++;
                    arrRef[a]={};
                    arrRef[a].id=$(this).attr('data-name')+'[max]';
                    arrRef[a].value=$(this).val();
                    arrRef[a].type = $(this).closest('div').attr('rtype');
                }
            }
            a++;
        }
    });

    for(i=0;i<arrRef.length;i++)
    {
        if (query[arrRef[i].id] != undefined && query[arrRef[i].id] != '')
            query[arrRef[i].id] = query[arrRef[i].id]+"_"+arrRef[i].value;
        else
            query[arrRef[i].id] = arrRef[i].value;
    }

    query['search_by_params'] = 1;

    if (newDomain != '') {
        $.each(pathname, function(key, value){
            if (/page_[0-9]+/.test(value)) {
                pathname.splice(key, 1);
            }
        });
        window.location.href = newDomain + '/'+pathname.join('/') + '?' + $.param(query);
    }
    else {
        window.location = '/'+pathname.join('/') + '?' + $.param(query);
    }
}

function perform_windows_search(src) {
debugger;
    var query = $.parseParams(window.location.search.replace('?', ''));

    var newDomain = '';
    var href = window.location.href;
    href = href.split("?")[0];
    var pathname = href.split('/');
    pathname.splice(0,3);

    var source_element = $(src).attr('id');
    //Сменена ли география поиска
    var is_new_geo = false;
    var search_region = 0;
    var search_city = 0;

    //Выявление источника инициализации поиска
    //Список в панели фильтров
    if (source_element == 'city-list')
    {
        search_city   = $('#filter_box select#city-list').val();
        search_region = $('#filter_box select#region-list').val();
        //Костылек для подгонки под имеющуюся логику
        if (search_city != 0) search_region = 0;

        is_new_geo = true;
    }


    if (is_new_geo)
    {
        var newRegionSeoName = pathname[0];

        $.ajax({
            url: '/search/get_location_name',
            type: 'POST',
            data: {region_id:search_region, city_id:search_city},
            dataType: "json",
            async: false,
            success: function( data, status ) {
                if (data.seo_name)
                    newRegionSeoName = data.seo_name;
            },
        });

        $.ajax({
            url: '/indexpage/ajax_set_location',
            data: {region_id:search_region, city_id:search_city},
            dataType: "json",
            async: false,
            success: function( data, status ) {

            },
        });

        if (newRegionSeoName != pathname[0])
        {
            pathname[0] = newRegionSeoName;

            $.ajax({
                url: '/indexpage/ajax_check_region_subdomain',
                type: 'POST',
                data: {city_id:search_city},
                dataType: "json",
                async: false,
                success: function( data, status ) {
                    if (data.url)
                        newDomain = data.url;
                },
            });
        }
    }

    var source = $('#options_window').find("input[name='source'][type='radio']:checked").val();
    if (source == undefined)
        source = 0;
    if (source != 0)
        query['source'] = source;
    else
        delete query['source'];

    var org_type = $('#options_window').find("input[name='org_type'][type='radio']:checked").val();
    if (org_type == undefined)
        org_type = 0;
    if (org_type != 0)
        query['org_type'] = org_type;
    else
        delete query['org_type'];

    if ($('#options_window').find("input[name='photo']").is(':checked'))
        query['photo'] = 1;
    else
        delete query['photo'];

    if ($('#options_window').find("input[name='video']").is(':checked'))
        query['video'] = 1;
    else
        delete query['video'];

    var names = new Array();
    a = 0;
    $('#options_window').find('*[data-name]').each(function()
    {
        name = $(this).attr('data-name');


        if ($.inArray(name, names) == -1)
        {
            names[a] = name;
            a++;
        }

    });

    for(i=0;i<names.length;i++)
    {
        delete query[names[i]];
        delete query[names[i]+'[min]'];
        delete query[names[i]+'[max]'];
    }

    a = 0;
    arrRef=new Array();
    $('#options_window').find('*[data-name]').each(function()
    {
        if ($(this).is(':checked') == true && ($(this).attr('type')=='radio' || $(this).attr('type')=='checkbox'))
        {
            arrRef[a]={};
            arrRef[a].id=$(this).attr('data-name');
            arrRef[a].value=$(this).attr('data-value');
            arrRef[a].type = $(this).closest('div').attr('rtype');
            if (arrRef[a].value == undefined)
                arrRef[a].value = 1;

            a++;
        }
        if ($(this).val() != '' && $(this).val() != undefined && ($(this).attr('type')=='text'))
        {
            arrRef[a]={};
            arrRef[a].id=$(this).attr('data-name');
            arrRef[a].value=$(this).val();
            arrRef[a].type = $(this).closest('div').attr('rtype');

            if (arrRef[a].type == 'integer' || arrRef[a].type == 'numeric')
            {
                if (arrRef[a].id.indexOf('[min]') == -1 && arrRef[a].id.indexOf('[max]') == -1)
                {
                    arrRef[a].id = arrRef[a].id+'[min]';

                    a++;
                    arrRef[a]={};
                    arrRef[a].id=$(this).attr('data-name')+'[max]';
                    arrRef[a].value=$(this).val();
                    arrRef[a].type = $(this).closest('div').attr('rtype');
                }
            }
            a++;
        }
    });

    for(i=0;i<arrRef.length;i++)
    {
        if (query[arrRef[i].id] != undefined && query[arrRef[i].id] != '')
            query[arrRef[i].id] = query[arrRef[i].id]+"_"+arrRef[i].value;
        else
            query[arrRef[i].id] = arrRef[i].value;
    }

    query['search_by_params'] = 1;

    if (newDomain != '') {
        $.each(pathname, function(key, value){
            if (/page_[0-9]+/.test(value)) {
                pathname.splice(key, 1);
            }
        });
        window.location.href = newDomain + '/'+pathname.join('/') + '?' + $.param(query);
    }
    else {
        window.location = '/'+pathname.join('/') + '?' + $.param(query);
    }
}

function set_sort(src)
{
    window.location = $(src).val();
}

function favorites(is_favor, id_object)
{
	objRef={};
	objRef.action="change_favorites";
	objRef.favor=is_favor;
	if(is_favor == 1){
	   text="в избранное";
	}
	else{
	   text="в избранном";
	}
	objRef.id_object=id_object;
	searchParams=JSON.stringify(objRef);
	if (true){
	  $.post('/search/ajax','params='+searchParams,function(data){
		 if(data=="add_ok"){
				$('#favor_'+id_object).removeClass('on off');
				$('#favor_'+id_object).addClass('on');
				$('#favor_'+id_object).attr("onclick", "favorites(1, "+id_object+")");				
			}

		 if(data=="del_ok"){
				$('#favor_'+id_object).removeClass('on off');
				$('#favor_'+id_object).addClass('off');
				$('#favor_'+id_object).attr("onclick", "favorites(0, "+id_object+")");
		 }
		 
		 $('#favor_'+id_object).html(text);
		 
	  });
   } 
   return false;
}

$(document).ready(function() {
	
	$('#sub_form').submit(function(){
		$('#sub_error').hide();
		$.post($(this).attr('action'), $(this).serialize(), function(json){
			if(json.code == 200) {
				$('#sub_table').hide();
				$('#sub_success').html(json.html);
				$('#sub_success').show();
				
			} else if (json.code == 400) {
				$('#sub_table').hide();
				$('#sub_error2').show();
			} else {
				$('#sub_table').hide();
				$('#sub_error').show();
			}
		}, 'json')
		return false;
	});//$('#sub_form').submit(function()
	
	
	$('.btn-white#sub_link').click(function(){
		$.post('/search/ajax_check_subscriptions', {link:$('#link').val()}, function(json){
			if (json.code == 401) {
				$('#sub_table').hide();
				$('#sub_success').hide();
				$('#sub_error').show();
				$('#sub_error2').hide();
			} else if (json.code == 402) {				    
				$('#sub_table').hide();
				$('#sub_success').hide();
				$('#sub_error').hide();
				$('#sub_error2').show();					
			}
			else if (json.code == 400) {
				//$('#auth_link').click();
				$('.btn-white.enter').click();				
			} else {
				$('#sub_table').show();
				$('#sub_success').hide();
				$('#sub_error').hide();
				$('#sub_error2').hide();					
			}

		}, 'json');

		$('.unsub').fadeOut();
		$('.sub-to-add').fadeIn();
		$('.popup-layer').fadeIn();
	});


	
	$(".unsub_link").click(subscribes);	

    if ($('#units').length) {
        $('#units').load($('#units').data('url'));
    }

	
});//$(document).ready(function()

function subscribes() {
		$.post('/search/ajax_user_subscriptions', {}, function(json){
			$('#unsub_form').html(json.html);				
		}, 'json');

	$('.sub-to-add').fadeOut();	
	$('.unsub').fadeIn();
	$('.popup-layer').fadeIn();			
}

function unsubscribe(obj) {
	$.get($(obj).attr('href'), {}, function(json){
		if (json.code == 200) {
			$(obj).parent().parent().remove();
		}
	}, 'json');

	return false;
}