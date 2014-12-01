<script type="text/javascript" charset="utf-8">
	var _pricecontrol = null;
	function pricecontrol(text, object_id)
	{
		var s = this,
			price_page = 0,
			limit = 50,
			_text = text,
			_object_id = object_id,
			_attributes = {};

		s.price_prev = function(){
			if (price_page == 0)
				return;

			price_page--;
			s.price_navigate();
			s.forto();
		}

		s.price_next = function()
		{
			price_page++;
			s.price_navigate();
			s.forto();
		}

		s.forto = function()
		{
			$(".price-for").text(price_page*limit);
			$(".price-to").text(price_page*limit+limit);
		}

		s.price_search = function(clear)
		{
			_text = $("#pricesearch").val();
			_attributes = {};
			if (!_text) {
				_.each($(".priceattribute"), function(item){
					_attributes["attribute_"+$(item).attr("attribute")] = $(item).val();
				});
			} else 
			if (_text && clear) {
				_text = "";
				$("#pricesearch").val("");
				attributes = {};
				_.each($(".priceattribute"), function(item){
					_attributes["attribute_"+$(item).attr("attribute")] = $(item).val();
				});
			} else 
			if (_text && !clear) {
				attributes = {};
				_.each($(".priceattribute"), function(item){
					$(item).val("");
				});
			}
			price_page = 0;
			s.forto();
			s.price_navigate();
		}

		s.price_clear = function()
		{
			_text = "";
			_attributes = {};
			$("#pricesearch").val("");
			_.each($(".priceattribute"), function(item){
				$(item).val("");
			});
			price_page = 0;
			s.forto();
			s.price_navigate();
		}

		s.price_navigate = function ()
		{
			var params = {	page : price_page,
							object_id : _object_id,
							text : _text
						};

			params = _.extend(params, _attributes);
			$.post( "/ajax/price_navigate_object", params
				, function( response ) {
					if (response)
						$(".pricerows_body").html(response);

					if (!$(".pricerows_body").find("td").length){
						$(".pricerows_body").html("Не найдены");
						$(".pricelistcontrol2").hide();
					} else {
						$(".pricelistcontrol2").show();
					}
			});
		}
	}
	
	$(document).ready(function() {	
		var text = $("#pricesearch").val();
		var object_id = $(".pricelistcontrol").attr("data-object-id");			
		_pricecontrol = new pricecontrol(text, object_id);
	});
</script>
<p class="title"><?=$priceload->title?></p>
<p class="mb10 mt10"><?=$priceload->description?></p>

<div class="pricelist-cont">
	<? if ($pricerows_filters): ?>
		<? foreach($pricerows_filters as $fname => $filter):?>
			<select attribute="<?=$fname?>" class="priceattribute" onchange="_pricecontrol.price_search(true)">
				<option value="">-- <?=$filter[0]->attribute_title?>--</option>
				<? foreach($filter as $value):?>
					<option value="<?=$value->id?>"><?=$value->title?></option>
				<? endforeach; ?>
			</select>
		<? endforeach; ?>
	<? endif; ?>
	<div class="search-tool oh">	

		<span class="fl mr5 mt5 mb5">Поиск:</span> 
		<input id="pricesearch" class="pricesearch fl mr5 mb5" type="text"/>
		<input class="pricesearch button blue fl mr5 mb5" onclick="_pricecontrol.price_search()" type="submit" value="Найти"/>
		<input class="pricesearch button blue fl mr5 mb5" onclick="_pricecontrol.price_clear()" type="button" value="Сброс"/>
		<? if (count($pricerows) >= 49): ?>
			<div class="pricelistcontrol oh fr" data-object-id="<?=$object["id"]?>">

					<span onclick="_pricecontrol.price_prev();" class='navi-left' title='Назад'></span>
					<span class="fl ml10 mr10 lh2 label">с <span class="price-for">0</span> по <span  class="price-to">50</span></span>
					<span onclick="_pricecontrol.price_next()" class='navi-right' title='Дальше'></span>

			</div>
		<? endif; ?>

	</div>

<table class="bs-table bs-table-hover bs-table-condensed mt20 pricerows_body">
	<?php echo View::factory('landing/price/pricerow/pricerow_default', array(	"priceload" => $priceload, 
  																	"pricerows" => $pricerows));  ?>
</table>
<? if (count($pricerows) >= 49): ?>
	<div class="pricelistcontrol oh mt20 mb20" data-object-id="<?=$object["id"]?>">
		<div class="cont fr">
			<span onclick="_pricecontrol.price_prev();" class='navi-left' title='Назад'></span>
			<span class="fl ml10 mr10 lh2 label">Показаны предложения с <span class="price-for">0</span> по <span  class="price-to">50</span></span>
			<span onclick="_pricecontrol.price_next()" class='navi-right' title='Дальше'></span>
		</div>
	</div>
<? endif; ?>
</div>
