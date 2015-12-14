<div class="p_cont massload">
	<? if (count($categories)>0): ?>
		<div id="fn-main-cont" class="cont">


			<table class="table table-bordered table-striped">
				<tr>
					<th class="bold">#</th>
					<th class="bold" style="width:100px;">Дата</th>
					<th class="bold" style="width:150px;">Категория</th>
					<th class="bold">Статистика <br/>(нов/изм/неизм/ошибки(%)=все)</th>
					<th class="bold">Просмотр</th>
					<th class="bold">Состояние</th>
					<th></th>
				</tr>
				<?php foreach ($objectloads as $item) : ?>
					<tr id="ol_<?=$item->id?>" class="ol_<?=$item->id?>">			
						<td><?=$item->id?></td>
						<td><?=$item->created_on?></td>
						<td></td>	
						<td id="stat_<?=$item->id?>"><?=$item->statistic_str?></td>
						<td></td>
						<td id="ol_state_<?=$item->id?>">
							<?if (!$item->withcomment_state): ?>
								<?=$states[$item->state]?>
							<?else:?>
								<a style='color:red !important;' href='#' onclick="show_message('<?=$item->comment?>');return false;"><?=$states[$item->state]?> (?)</a>
							<?endif;?>
						</td>
						<td id="ol_button_<?=$item->id?>">
							<? if ($item->access_refresh): ?>
								<span class="icon-refresh" onclick="retest_ol(<?=$item->id?>);return false;" style="cursor:pointer;"></span>
							<? endif;?>
							<? if ($item->access_userdelete): ?>
								<span class="icon-trash" onclick="delete_ol(<?=$item->id?>);return false;" style="cursor:pointer;"></span>
							<? endif;?>
						</td>		
					</tr>
					<?php foreach ($item->objfiles as $file) : ?>
						<tr style="" class="ol_<?=$item->id?>">			
							<td></td>
							<td></td>
							<td><a target="_blank" href="http://c.yarmarka.biz/user/massload_conformities/<?=$file->category?>"><?=$config[$file->category]['name']?> (?)</a></td>	
							<td id="stat_<?=$file->id?>_<?=$file->category?>">
								<?=$file->statistic_str?>					
							</td>
							<td>
								<a href="/user/objectload_file_list/<?=$file->id?>" target="_blank">Все строки</a>, 
								<? if ($file->error_exists):?>
									<a href="/user/objectload_file_list/<?=$file->id?>?errors=1" target="_blank">Строки с ошибками</a>
								<? endif;?>													
							</td>
							<td></td>
							<td class="buttons_<?=$item->id?>">
								
							</td>		
						</tr>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</table>

			<? if (!$hidehelp):?>
				<p><h2>Настройка соответствий</h2>
					<div class="massload-controlsrow">
						<ul>
							<? foreach($categories as $key=>$value): ?>
								<li key="<?=$key?>"><a href="http://c.yarmarka.biz/user/massload_conformities/<?=$key?>"><?=$value?></a></li>
							<? endforeach; ?>
						</ul>	
					</div>
				</p>
				<p>
					<h2>Шаблоны</h2>
					<div class="massload-controlsrow">
						<ul>
							<? foreach($categories as $key=>$value): ?>
								<li key="<?=$key?>"><a href="<?=$categories_templates[$key]?>" target="_blank"><?=$value?>.xls</a></li>
							<? endforeach; ?>
						</ul>	
					</div>
				</p>
			<? endif;?>
		</div>


	<? else: ?>
		Услуга массовой загрузки не подключена.
	<? endif; ?>
</div>