<div class="winner">
	<section class="main-cont subscriptions cabinet">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')->set('categories', $categories);?>
			<section class="p_room-inner">
				<header><span class="title">Настройка соответствий для массовой загрузки объявлений</span></header>
				<div class="p_cont massload">
					
					<? if (count($categories)>0): ?>
						<? foreach($forms as $category=>$items): ?>
								<div class="massload-controlsrow massload-head">
									<b><?=$categories[$category]?></b>
								</div>
								<? foreach($forms[$category] as $type=>$values): ?>
									<div class="massload-controlsrow massload-subhead">
										<b>Аттрибут:</b> <?=$values[0]["name"]?> (<?=$type?>)
									</div>
									<div class="massload-controlsrow massload-table">
									<table>
									<? foreach($values as $value=>$conformity): ?>
										<? if ($value === 0) continue;?>
										<tr class="fn-row">
											<td align=right class="massload-td-left"><?=$value?>  = </td>
											<td>
												<input class="fn-conformity" type="text" value="<?=$conformity?>" data-ml="<?=$category?>" data-value="<?=$value?>" data-type="<?=$type?>"/>
											</td>
											<td>
												<div class="button blue">	
													<span class="fn-save">Сохранить</span>
												</div>
											</td>
											<td>	
												<div class="button red">
													<span class="fn-delete">Удалить</span>
												</div>
											</td>
										</tr>
									<? endforeach; ?>
									</table>
									</div>
								<? endforeach; ?>
						<? endforeach; ?>

					<? else: ?>
						Услуга массовой загрузки не подключена.
					<? endif; ?>
				</div>
				<div class="clear"></div>
				<br />
				
			</section>
		</div>	   
		  
	</section>
</div><!--end content winner-->
