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

	<?=$plan->plan->title?> на <?=$plan->plan->count?> объявлений, истекает <span><?=$interval_str?></span></br>
<? endforeach;?>
</div>
<? endif; ?>
</br>
</br>
<div>
<? foreach($not_yet_payment as $item):?>
	В рубрике "<?=$item['title']?>" у вас - <?=$item['count']?> объявлений</br>
	Ваш текущий план : "<?=$item['current_plan']?>" не более - <?=$item['current_plan_count']?> объявлений.</br>
	Рекомендуем приобрести тарифный план согласно количества ваших объявлений</br>
	Объявления сверх лимита будут сняты автоматически.
<? endforeach;?>
</div>