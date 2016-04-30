<script>
	$(document).ready(function(){
		$('.js-item').click(function(e){
			var id = $(this).data('id');
			$('.js-subitem').addClass('hidden');
			$('.js-subitem'+id).removeClass('hidden');
		});
	});
</script>

<div class="row">
	
	<div class="span12">
		<h2>Информация об услугах в объявлении №<?=$object->id?></h2>
		<p class="lead"> <?=$object->title?> </p>
	</div>
</div>
<div class="row">
	
	<div class="span8">
		<p class="lead">Премиум</p>
		<table class="table table-hover">
			<? foreach ($premiums as $premium): ?>
				<tr>
					<th>Город</th>
					<th>Активировано раз</th>
					<th>Кол-во</th>
					<th>Истекает</th>
				</tr>
				<tr class="<? if (!isset($premium->expired) ):?>success<? endif; ?>">
					<td><?=$premium->city?></td>
					<td><?=$premium->count?></td>
					<td><?=$premium->activated?></td>
					<td><?=date('d.m.Y H:i', strtotime($premium->date_expiration) )?></td>
				</tr>
			<? endforeach; ?>
		</table>

		<p class="lead">Лидер</p>
		<table class="table table-hover">
			<? foreach ($liders as $lider): ?>
				<tr>
					<th>Город</th>
					<th>Категория</th>
					<th>Активировано раз</th>
					<th>Кол-во</th>
					<th>Истекает</th>
				</tr>
				<tr class="<? if (!isset($lider->expired)):?>success<? endif; ?>">
					<td><?=$lider->cities?></td>
					<td><?=$lider->categories?></td>
					<td><?=$lider->activated?></td>
					<td><?=$lider->count?></td>
					<td><?=date('d.m.Y H:i', strtotime($lider->date_expiration) )?></td>
				</tr>
			<? endforeach; ?>
		</table>

		<p class="lead">Подъемы</p>
		<table class="table table-hover">
			<? foreach ($ups as $up): ?>
				<tr>
					<th>Дата</th>
					<th>Кол-во</th>
					<th>Активировано раз</th>
				</tr>
				<tr class="">
					<td><?=date('d.m.Y H:i', strtotime($up->date_created) )?></td>
					<td><?=$up->activated?></td>
					<td><?=$up->count?></td>
				</tr>
			<? endforeach; ?>
		</table>

		<p class="lead">Заказы пользователя</p>
		<table class="table table-hover" >
			<tr>
					<th>Дата оплаты</th>
					<th>Состояние</th>
					<th>Сумма</th>
				</tr>
			<? foreach ($orders as $order): ?>
				
				<tr class="js-item <? if ($order->current):?>warning<? endif;?>" style="cursor:pointer" data-id="<?=$order->id?>">
					<td><?=date('d.m.Y H:i', strtotime($order->payment_date) )?></td>
					<td><?=$order->state_name?></td>
					<td><?=$order->sum?></td>
				</tr>
				<? foreach ($order->items as $item): ?>
					<tr class="js-subitem js-subitem<?=$order->id?> info hidden" style="font-size:0.8em;">
						<td colspan="2"><?=$item->params->service->quantity?> шт. <?=$item->params->service->title?> для <a href="/detail/<?=$item->params->object->id?>"><?=$item->params->object->title?></a></td>
						<td><?=$item->params->service->price?></td>
					</tr>
				<? endforeach; ?>
			<? endforeach; ?>
		</table>
	</div>
	<div class="span4">
		<p class="lead">Размещен в городах</p>
		<table class="table table-hover">
			<? foreach ($cities as $city): ?>
				
				<tr class="<? if ($city->main):?>success<? endif; ?>">
					<td><?=$city->title?></td>
				</tr>
			<? endforeach; ?>
		</table>
	</div>
</div>


