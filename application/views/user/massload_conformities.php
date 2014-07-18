<div class="winner">
	<section class="main-cont subscriptions cabinet">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')->set('categories', $categories);?>
			<section class="p_room-inner">
				<header><span class="title">Настройка соответствий для массовой загрузки объявлений</span></header>
				<div class="p_cont myadd mysub">
					<? if (count($categories)>0): ?>
						<? foreach($forms as $category=>$items): ?>
									<?=$categories[$category]?></br>
									<? foreach($forms[$category] as $type=>$values): ?>
										<?=$values[0]["name"]?></div></br>
										<table>
										<? foreach($values as $value=>$conformity): ?>
											<? if ($value === 0) continue;?>
											<tr class="fn-row">
												<td align=right><?=$value?>  = </td>
												<td>
													<input class="fn-conformity" type="text" value="<?=$conformity?>" data-ml="<?=$category?>" data-value="<?=$value?>" data-type="<?=$type?>" style="border:1px solid;"/>
												</td>
												<td>	
													<span class="fn-save">Сохранить</span>
												</td>
												<td>	
													<span class="fn-delete">Удалить</span>
												</td>
											</tr>
										<? endforeach; ?>
										</table>
										</br></br>
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
