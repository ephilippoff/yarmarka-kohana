<div class="winner">
	<section class="main-cont subscriptions cabinet">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">
				<header><span class="title">Заказы</span></header>
				<div class="p_cont">
					<table>
						<tr>
							<th>Наименование</th>
							<th>Состояние</th>
							<th>Сумма</th>
						</tr>
					<? foreach ($orders as $order): ?>
						<tr>
							<td><a href="/cart/order/<?=$order->id?>">Заказ #<?=$order->id?> от <?=$order->created?></a></td> 
							<td>
								<?=$getState($order->state)?> 
								<? if ($order->state == 3): ?>
									<?=$order->payment_date?>
								<? endif; ?>
							</td> 
							<td><?=$order->sum?></td> 
						</tr>
						<tr style="display:none;" class="info_<?=$order->id?>">
							<td colspan="3">
								<? foreach ($orderItems[$order->id] as $orderItem): ?>
									<?=$orderItem->title?>
									<?=$orderItem->price * $orderItem->quantity."р"?>
								<? endforeach ?>
							</td>
						</tr>
					<? endforeach; ?>
					</table>


				</div>
			</section>
		</div>	   
		  
	</section>
</div><!--end content winner-->
