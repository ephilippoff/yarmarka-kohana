<?php if ($user AND $user->linked_to->loaded()) : ?>
<ul>
	<li>
		<span>
			<div class="input style2">
				<label><span><i class="name">Привязан к компании:</i></span></label>
				<div class="inp-cont-bl">
					<a href="<?=URL::site('users/'.$user->linked_to->login)?>"><?=$user->linked_to->org_name?></a>
				</div>
			</div>
			<span class="btn-act cansel" id="remove_link"></span>
		</span>
	</li>
</ul>
<?php endif ?>