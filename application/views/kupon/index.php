<div class="kupon-print-cont">
	<div class="row mb30">
		<div class="logo fl"><img src="/images/black_logo.png"></div>
		<div class="fr"><img src="/images/kupon_barcode.png"></div>
	</div>
	<p class="tt-u ta-c fs20 fw-b mb5">Скидочный купон №<?=$kupon->id?></p>
	<p class="ta-c color-gray mb20">Предъявите этот купон на месте, чтобы получить услугу</p>
	<hr class="mb10">
	<p class="ta-c tt-u fs26 mb5">ПИН-КОД <?=$kupon->code?></p>
	<p class="ta-c tt-u color-gray mb15">Внимание! Не сообщайте пин-код до момента получения товара или услуги</p>
	<hr class="mb20">
	<p class="mb20 fs16 fw-b ta-c"><?=$kupon->count?> комплексная автомойка от компании &laquo;<?=strip_tags($object->contact)?>&raquo;</p>
	<hr class="mb10">
	<div class="row mb10">
		<div class="fl">
			<p class="fs18 fw-b mb3">Цена: <?=number_format((float)$kupon->price, 0, ',', ' ') ?> руб.</p>
			<p class="fs10 mb20">Обычная: <?=number_format((float)$attributes_values['old-price'], 0, ',', ' ') ?> руб. Экономия: <?=number_format((float)$attributes_values['economy'], 0, ',', ' ') ?> руб.</p>
			<?php 
				$contact_types = array(1 => 'Тел.', 2 => 'Тел.', 5 => 'Email');
				foreach ($object->get_contacts() as $contact) : ?>
					<p><?=$contact_types[$contact->contact_type_id]?>: <?=$contact->contact?></p>
			<?php endforeach; ?>			
				<p>Адрес: <?=strip_tags($attributes_values['adres-raion'])?></p>
			<p class="mb20"><?=strip_tags($attributes_values['adres-detail'])?></p>
			<p>Воспользоваться купоном можно до:</p>
			<p class="fw-b"><?=strip_tags($attributes_values['goden-do'])?></p>
		</div>
		<div class="fr">
			
		</div>
	</div>
	<hr>
	<p class="mb10">Описание услуги:</p>
	<div class="text-cont mb15">
		<?=$object->user_text?>
	</div>
	<hr class="mb15">
	<p class="ta-c fs16 color-gray">Служба поддержки Ярмарка-Скидки в Нижневартовске:</p>
	<p class="ta-c fs16 color-gray mb20">Тел. +7(909)555-44-33, email info@yarmarka.biz</p>
</div>