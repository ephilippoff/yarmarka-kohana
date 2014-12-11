<div class="title"></div>

<form action="http://<?=Region::get_current_domain()?>/search" method="get" name="search-form" id="search-form">
<div class="btn-red btn-find" onClick="$('#search-form').submit()"><span>Найти</span></div>	
<!--noindex-->
<div class="seach-bl-fix">
	<div class="seach-bl">
		<?

			$city_id = Region::get_current_city();
			$region_id = Region::get_current_region();

		?>
		<div class="input-seach"><input type="text" name="k"  city="<?$city_id?>" region="<?=$region_id?>" id="search-input"  placeholder="Начните поиск. Например: Тойота Авенсис" autocomplete="off"/></div>
		<div class="search-popup fn-search-popup"></div>
	</div>
</div>
<!--/noindex-->
</form>
<script type="text/javascript" charset="utf-8">
	var search_timer = null;
	var search_popup_pos = null;
	var search_popup_text = null;
	function show_search_popup(text, city_id, region_id)
	{
		if (!text){
			$(".search-popup").html("");
			return;
		}

		$.post( "/ajax/global_search", {text:text, city_id:city_id, region_id:region_id}, function( response ) {
		  	var data = null;
		  	if (response)
		  		data = $.parseJSON(response);

		  	if (data.code == 200)
		  	{
		  		var params = {
		  			pricerows : data.pricerows,
		  			pricerows_found : data.pricerows_found,
		  			objects : data.objects,
		  			objects_found : data.objects_found
		  		};

		  		if (!data.pricerows.length && !data.objects.length){
		  			$(".search-popup").html("");
		  			return;
		  		}
		  		
		  		var tmpl =  _.template($("#template-search-popup").html(), params);
		  		$(".search-popup").html(tmpl).show();

		  		$( ".result-line" ).mouseout(function(e) {
				  $(this).removeClass("mark");
				});

				$( ".result-line" ).mouseover(function(e) {
				   $(".search-popup").find(".result-line").removeClass("mark");
				   $(this).addClass("mark");
				   if ($(this).hasClass("pricerow")){
						$( "#search-input" ).val(search_popup_text);
					} else {
						var text = $(this).find(".finded-title").text();
						if (text)
							$( "#search-input" ).val(text);
					}
				});

				$( ".result-line" ).click(function(e) {
					$('#search-form').submit();
				});

		  	} else {
		  		$(".search-popup").html("");
		  	}
		  
		});
	}

	function doSearch(text, city_id, region_id){
		search_popup_text = text;
		window.clearTimeout(search_timer);
		search_timer = setTimeout(function(){
	        show_search_popup(text, city_id, region_id);
	     }, 200);
	}

	function doMarkRow(keyCode)
	{
		//38 up 40 down

		if (keyCode != 38 && keyCode != 40)
			return;

		var rows = $(".search-popup").find(".result-line");
		var nowrow = rows.index($(".mark"));
		var pos = 0;
		if (keyCode == 38){
			if (nowrow < 0) {
				pos = rows.length-1;
			}
			else {
				pos = nowrow;
				pos--;
			}

			rows.removeClass("mark");
			rows.eq(pos).addClass("mark");

		}
		if (keyCode == 40){
			if (nowrow < 0) {
				pos = 0;
			}
			else {
				pos = nowrow;
				pos++;
			}
			rows.removeClass("mark");
			rows.eq(pos).addClass("mark");
		}

		if (rows.eq(pos).hasClass("pricerow")){
			$( "#search-input" ).val(search_popup_text);
		} else {
			var text = rows.eq(pos).find(".finded-title").text();
			if (text)
				$( "#search-input" ).val(text);
		}

	}

	$(document).ready(function() {
		$( "#search-input" ).keyup(function(e) {
			var text = $(e.target).val();
			var city_id = $(e.target).attr("city");
			var region_id = $(e.target).attr("region");
			var keyCode = e.keyCode;
			if (search_popup_text != text && keyCode != 38 && keyCode != 40)
				doSearch(text, city_id, region_id);
			doMarkRow(e.keyCode);
		});

		
	});
</script>
<script id="template-search-popup" type="text/template">
	<ul>
	<% if (objects.length) { %>
	<li class="finded-header"><p>Найдено <%=objects_found%> объявлений. Нажмите Enter чтобы посмотреть все</p></li>
	<% }; %>
	<% _.each(objects, function(item){ %>
		<li class="result-line">
			<span class="finded-title"><%=item.title %></span>
			<a href="/<%=item.category_url %>"><%=item.category_title %></a>
		</li>
	<% }); %>
	<% if (pricerows.length) { %>
		<li class="finded-header"><p>Найдено <%=pricerows_found%> позиций в прайс-листах</p></li>	
	<% }; %>		
	<% _.each(pricerows, function(item){ %>
		<li  class="result-line pricerow">
			<span class="finded-title"><%=item.description %></span>

			<% if (item.city_name){ %>
				<span class="city"><%=item.city_name %></span>
			<% } %>
			<span class="city mr20" style="color:gray;"><a href="/detail/<%=item.object_id %>"><%=item.title %></a></span>
		</li>
	<% }); %>
	</ul>
</script>
