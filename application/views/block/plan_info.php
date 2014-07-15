<? if (count($user_plans) > 0): ?>
	<div>
	Ваши текущие тарифные планы:<br/>
	<? foreach($user_plans as $plan):?>
		<? 
			$current_date = new DateTime();
			$date_expiration = new DateTime($plan->date_expiration);
			$interval = date_diff($current_date, $date_expiration);
			$interval_str = $plan->date_expiration;
			if ((int) ($interval->format('%d')) < 3)
				$interval_str = "через ".$interval->format('%d дн. %H:%I:%S')
		?>
		<?=Message::get('plan', 'plan_expired', array("name" =>$plan->plan->title, 
													  "count" => $plan->plan->count, 
													  "about" => $interval_str) ); ?>
	<? endforeach;?>
	</div>
<? endif; ?>
</br>
</br>
<div>

<? foreach($not_yet_payment as $item):?>
	<?=Message::get('plan', 'count_adverts_in_category', array("category" =>$item->title, 
													 		 	"count" => $item->count)); ?></br>

	<?=Message::get('plan', 'current_plan', array(	"name" =>$item->current_plan, 
													"count" => $item->current_plan_count)); ?></br>
													
	Рекомендуем приобрести тарифный план согласно количества ваших объявлений</br>
	Объявления сверх лимита будут сняты автоматически.

<? endforeach;?>
</div>