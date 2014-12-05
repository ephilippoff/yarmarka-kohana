<div class="pricelist-cont">
	<div class="search-tool oh">
			<div class="pricelistcontrol oh fr" data-object-id="<?=$object["id"]?>" data-count="<?=$pricerows_count?>">
					<span onclick="_pricecontrol.price_prev();" class='navi-left' title='Назад'></span>
					<span class="fl ml10 mr10 lh2 label">с <span class="price-for">0</span> по <span  class="price-to">50</span></span>
					<span onclick="_pricecontrol.price_next()" class='navi-right' title='Дальше'></span>
			</div>
	</div>
	<div class="pricelist-wrapper">
		<div class="pricelist-side">
			<p class="title">Категории</p>
			<?php echo View::factory('landing/price/hierarchy/hierarchy_default', array(	"hierarchy_filters" => $hierarchy_filters, "level0" => TRUE) );  ?>
			<? if ($simple_filters): ?>
			<? foreach($simple_filters as $fname => $filter):?>
				<div >
					<select attribute="<?=$fname?>" class="priceattribute" onchange="_pricecontrol.price_attribute_search()">
						<option value="">-- <?=$filter[0]->attribute_title?>--</option>
						<? foreach($filter as $value):?>
							<option value="<?=$value->id?>"><?=$value->title?></option>
						<? endforeach; ?>
					</select>
				</div>
			<? endforeach; ?>
			<script id="template-hierarchy-filter" type="text/template">
					<div class="level">
						<ul>
							<% _.each(filters, function(filter){ %>
									<li>
										+ <span class="link" onclick="_pricecontrol.price_search_hierarchy(this, <%=filter.id %>,<%=filter.attribute_id %>)"><%=filter.title %> (<%=filter.count %>)</span>
									</li>
							<% }); %>							
						</ul>
					</div>
			</script>
			<div>
				<span class="fl mr5 mt5 mb5">Поиск:</span> 
				<input id="pricesearch" class="pricesearch fl mr5 mb5" type="text"/>
				<input class="pricesearch button blue fl mr5 mb5" onclick="_pricecontrol.price_text_search()" type="submit" value="Найти"/>
				<input class="pricesearch button blue fl mr5 mb5" onclick="_pricecontrol.price_clear(true)" type="button" value="Сброс"/>
			</div>
		<? endif; ?>
		</div>
		<div class="pricelist-content">
			<p class="title"><?=$priceload->title?></p>
			<p class="mb10 mt10"><?=$priceload->description?></p>
			<table class="bs-table bs-table-hover bs-table-condensed mt20 pricerows_body">
				<tr>
					<? foreach($columns as $column): ?>
							<th><?=$column?></th>
					<? endforeach; ?>
					<th>Описание</th>
					<th style="width:80px;">Цена</th>				
				</tr>

				<?php echo View::factory('landing/price/pricerow/pricerow_default', array("pricerows" => $pricerows));  ?>
			  
			</table>
			<script id="template-pricerow" type="text/template">
					<tr>
						<? foreach($columns as $column): ?>
								<th><?=$column?></th>
						<? endforeach; ?>
						<th>Описание</th>
						<th style="width:80px;">Цена</th>				
					</tr>
					<% _.each(pricerows, function(pricerow){ %>
						<tr>
							<% _.each(pricerow.values, function(item){ %>
								<td><%=item%></td>
							<% }); %>
							<td><%=pricerow.description %></td>
							<td><%=pricerow.price %> р</td>
						</tr>
					<% }); %>
			</script>
		</div>
	</div>
	<div class="pricelistcontrol oh mt20 mb20" data-object-id="<?=$object["id"]?>">
		<div class="cont fr">
			<span onclick="_pricecontrol.price_prev();" class='navi-left' title='Назад'></span>
			<span class="fl ml10 mr10 lh2 label">Показаны предложения с <span class="price-for">0</span> по <span  class="price-to">50</span></span>
			<span onclick="_pricecontrol.price_next()" class='navi-right' title='Дальше'></span>
		</div>
	</div>
</div>
