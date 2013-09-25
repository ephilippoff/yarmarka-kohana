<?php if (count($links)) : ?>
	<ul><li>
	<div class="input style2">
		<div class="inp-cont-bl">
		<?php foreach ($links as $link) : ?>
		<span>
			Вас приглашают присоедениться к компании (вы можете быть присоеденены только к одной компании)
			<?php if (trim($link->user->org_name)) : ?>
				<a href="<?=URL::site('users/'.$link->user->login)?>" target="_blank"><?=$link->user->org_name?></a>
			<?php else : ?>
				"Не названа"
			<?php endif ?>
			<span class="btn-act apply user_link_approve" data-href="<?=URL::site('ajax/approve_user_link/'.$link->id)?>"></span>
			<span class="btn-act cansel user_link_decline" data-href="<?=URL::site('ajax/decline_user_link/'.$link->id)?>"></span>
		</span>
		<br />
		<?php endforeach ?>
		</div>
	</div>
	</ul></li>
<?php endif ?>