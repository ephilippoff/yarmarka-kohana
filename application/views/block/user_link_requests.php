<?php if ($links) : ?>
<article class="iinput-bl">
	<ul><li>
	<div class="input style2">
		<label><span><i class="name">Запросы на добавление в штат компании:</i></span></label>
		<div class="inp-cont-bl">
		<?php foreach ($links as $link) : ?>
		<span>
			<a href="<?=URL::site('users/'.$link->user->login)?>"><?=$link->user->org_name?></a>
			<span class="btn-act apply user_link_approve" data-href="<?=URL::site('ajax/approve_user_link/'.$link->id)?>"></span>
			<span class="btn-act cansel user_link_decline" data-href="<?=URL::site('ajax/decline_user_link/'.$link->id)?>"></span>
		</span>
		<br />
		<?php endforeach ?>
		</div>
	</div>
	</ul></li>
</article>
<?php endif ?>