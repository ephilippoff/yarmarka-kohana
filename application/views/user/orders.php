<div class="winner">
	<section class="main-cont subscriptions cabinet">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner orders-cont">
				<header><span class="title">Заказы</span></header>		
					<table class="orders-table table">
						<tr>
							<th class="col1">Наименование</th>
							<th class="col2">Состояние</th>
							<th class="col3">Сумма</th>
						</tr>
					<? foreach ($orders as $order): ?>
						<tr>
							<td class="col1"><a href="/cart/order/<?=$order->id?>">Заказ #<?=$order->id?> от <?=date('d.m.Y', strtotime($order->created))?></a></td> 
							<td class="col2">
								<?=$getState($order->state)?> 
								<? if ($order->state == 3): ?>
									<?=$order->payment_date?>
								<? endif; ?>
							</td> 
							<td class="col3"><?=number_format($order->sum, 0, null, ' ')?> р.</td> 
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

			</section>
		</div>	   
		  
	</section>
</div><!--end content winner-->
