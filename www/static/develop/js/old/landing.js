var _pricecontrol = null;
function pricecontrol(text, object_id, count)
{
	var s = this;
	
	s.object_id = object_id;
	s.attributes = {};
	s.textSearch  = text;
	s.price_page = 0;
	s.limit = 50;
	s.startCountRows = count;
	s.countAllRows = count;
	if (s.limit > count)
		s.countRows = count;
	else
		s.countRows = s.limit;


	s.price_prev = function(){
		if (s.price_page == 0)
			return;
		
		s.do_search(s.price_page-1, true);
	}

	s.price_next = function()
	{
		s.do_search(s.price_page+1, true);
	}

	s.paginate = function(page)
	{
		s.price_page = page;
		$(".price-for").text(page * s.limit);
		$(".price-to").text(page * s.limit + s.limit);
	}

	s.paginateControlsInit = function()
	{
		var pages_count = Math.ceil(s.countAllRows/s.limit)-1;

		if (s.price_page <= 0)
			$(".navi-left").hide();
		else
			$(".navi-left").show();

		if (s.price_page >= pages_count)
			$(".navi-right").hide();
		else
			$(".navi-right").show();

	}

	s.price_text_search = function()
	{
		s.clear_attributes();
		s.clear_hierarchy();

		s.textSearch = $("#pricesearch").val();
		s.do_search(0);
	}

	s.price_attribute_search = function()
	{
		s.clear_textsearch();
		s.clear_hierarchy();
		_.each($(".priceattribute"), function(item){
			s.attributes["attribute_"+$(item).attr("attribute")] = $(item).val();
		});
		s.do_search(0);
	}

	s.price_search_hierarchy = function(context, filter_id, attribute_id)
	{
		s.clear_textsearch();
		s.clear_attributes();

		s.hierarchy_context = context;
		s.hierarchy_filter_id = filter_id;
		s.hierarchy_attribute_id = attribute_id;

		s.do_search(0);
	}

	s.do_search = function (page, skipHierarchy)
	{
		s.paginate(page);

		var params = {	page : s.price_page,
						object_id : s.object_id,
						text : s.textSearch
					};

		params = _.extend(params, s.attributes);
		params = _.extend(params, {hierarchy_filter_id : s.hierarchy_filter_id, hierarchy_attribute_id : s.hierarchy_attribute_id});
		$.post( "/ajax/landing/price_navigate_object", params
			, function( response ) {
				if (response) 
					data = $.parseJSON(response);
				else return;

				if (data.code != "200")
					return;
				
				s.countRows = data.count_rows;
				s.countAllRows = data.count_all_rows;
				s.paginateControlsInit();

				var tmpl =  _.template($("#template-pricerow").html(), {pricerows : data.data});
		  		$(".pricerows_body").html(tmpl);

		  		if (data.filter_childs && !skipHierarchy)
		  		{
		  			if ($(s.hierarchy_context).parent().find(".level").length == 0 && data.filter_childs.length){
		  				var tmpl =  _.template($("#template-hierarchy-filter").html(), {filters : data.filter_childs});
		  				$(s.hierarchy_context).parent().append(tmpl);
		  			} else{
		  				$(s.hierarchy_context).parent().find(".level").remove();
		  			}
		  		}
		});
		
	}

	s.clear_textsearch = function(){
		s.textSearch = null;
		$("#pricesearch").val("");
	}

	s.clear_attributes = function(){
		s.attributes = {};
		_.each($(".priceattribute"), function(item){
			$(item).val("");
		});
	}

	s.clear_hierarchy = function(){
		s.hierarchy_filter_id = s.hierarchy_attribute_id = null;
		$(".pricelist-side").find(".active").removeClass("active");
	}

	s.price_clear = function(withData)
	{
		s.clear_textsearch();
		s.clear_attributes();
		s.clear_hierarchy();
		s.paginate(0);
		if (withData)
			s.do_search(0);
		s.paginateControlsInit();
	}

	s.paginateControlsInit();
}

$(document).ready(function() {	
	var text = $("#pricesearch").val();
	var object_id = $(".pricelistcontrol").attr("data-object-id");
	var count = $(".pricelistcontrol").attr("data-count");			
	_pricecontrol = new pricecontrol(text, object_id, count);
});