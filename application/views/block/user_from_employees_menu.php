<?php foreach ($users as $user) : ?>
	<?php if ($user->count_company_objects(Auth::instance()->get_user()->id)) : ?>
		<?php if (Request::initial()->action() == 'from_employees' AND Request::initial()->param('id') == $user->id) : ?>
		<span class="noclickable"><b><i class="ico "></i><span><?=$user->get_user_name()?></span></b></span>
		<?php else : ?>
		<a href="<?=URL::site('user/from_employees/'.$user->id)?>" class="clickable"><i class="ico "></i><span><?=$user->get_user_name()?></span></a>
		<?php endif; ?>
	<?php endif ?>
<?php endforeach ?>