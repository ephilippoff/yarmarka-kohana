<?php if ($contact->verified_user_id) : ?>
	<?php if ($contact->moderate == 0) : ?>
		<a href="<?=URL::site('khbackend/phones/confirm/'.$contact->id)?>" data-id="<?=$contact->id?>" class="moderate btn btn-success">Верифицировать</a>
	<?php endif ?>
<a href="<?=URL::site('khbackend/phones/decline/'.$contact->id)?>" data-id="<?=$contact->id?>" class="moderate btn btn-danger">Отменить верификацию</a>
<?php endif ?>

<?php if ($contact->blocked == 0) : ?>
<a href="<?=URL::site('khbackend/phones/block/'.$contact->id)?>" data-id="<?=$contact->id?>" class="moderate btn btn-warning">Блокировать</a>
<?php else : ?>
<a href="<?=URL::site('khbackend/phones/unblock/'.$contact->id)?>" data-id="<?=$contact->id?>" class="moderate btn btn-info">Разблокировать</a>
<?php endif ?>