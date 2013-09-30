<?php if ($user AND $user->linked_to->loaded()) : ?>
<ul>
	<li>
		<span>
			<div class="input style2">
				<label><span><i class="name">Привязан к компании:</i></span></label>
				<p class="myinform">
					<a href="<?=URL::site('users/'.$user->linked_to->login)?>"><?=$user->linked_to->org_name?></a>
					(<span class="red" id="remove_link">Удалить связь</span>)
				</p>
			</div>
			
		</span>
	</li>
</ul>
<?php endif ?>