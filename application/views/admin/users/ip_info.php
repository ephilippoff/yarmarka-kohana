<h1><?=$ip?></h1>

<?php if ($ipblock->loaded()) : ?>
<div class="alert">
	Ip <strong><?=$ipblock->ip?></strong> is banned until <?=$ipblock->expiration_date?>.<br />
	Reason: <i><?=$ipblock->text?></i>
</div>
<?php endif; ?>

<form method="post" accept-charset="utf-8" class="form-inline">
Block ip:
<input type="text" name="reason" value="" placeholder="Reason">
<div class="input-append">
	<select name="period" id="period">
		<option value="1">1 day</option>
		<option value="3">3 days</option>
		<option value="7">week</option>
		<option value="">forever</option>
	</select>
	<input type="submit" class="btn btn-primary" type="button">Block</button>
</div>
</form>

<div class="row">

<div class="span6">
	<table class="table table-hover table-condensed">
	<tr>
		<th>Users:</th>
	</tr>
	<?php foreach ($users as $user) : ?>
	<tr>
		<td>
			<a href="<?=URL::site('khbackend/users/user_info/'.$user->id)?>" onClick="return popup(this);">
				<?=$user->login?> <?=$user->email?> <?=$user->fullname?>
			</a>
		</td>
	</tr>
	<?php endforeach; ?>
	</table>
</div>

<div class="span6">
	<table class="table table-hover table-condensed">
	<tr>
		<th>Objects:</th>
	</tr>
	<?php foreach ($objects as $object) : ?>
	<tr>
		<td>
			<small>#<b><?=$object->id?></b> <?=date('Y-m-d H:i', strtotime($object->real_date_created))?> </small>
			<a href="<?=CI::site('detail/'.$object->id)?>" target="_blank">
				<?=$object->title?>
			</a>
		</td>
	</tr>
	<?php endforeach; ?>
	</table>
</div>

</div>
