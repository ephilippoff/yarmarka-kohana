<?php foreach ($links as $link) : ?>
<span>
	<a href="<?=URL::site('users/'.$link->linked_user->login)?>"><?=$link->linked_user->get_user_name()?></a>
	<span class="btn-act apply user_link_approve" data-href="<?=URL::site('ajax/approve_user_link/'.$link->id)?>"></span>
	<span class="btn-act cansel user_link_decline" data-href="<?=URL::site('ajax/decline_user_link/'.$link->id)?>"></span>
</span>
<br />
<?php endforeach ?>