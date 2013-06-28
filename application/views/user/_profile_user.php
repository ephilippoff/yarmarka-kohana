<ul>
	<li id="org_type_edit" style="display:none">
		<div class="input style2 user-type">
			<label><span><i class="name">Тип пользователя:</i></span></label>
			<div class="inp-cont-bl ">
				<select class="iselect " name="org_type" id="org_type">
					<option value="1" <?=$user->org_type == 1 ? 'selected' : ''?>>Частное лицо</option>
					<option value="2" <?=$user->org_type == 2 ? 'selected' : ''?>>Компания</option>
				</select>
			</div>
			<span class="btn-act cansel org_type_cancel"></span>
		</div>
	</li>
	<li id="org_type_text">
		<div class="input style2">

			<label><span><i class="name">Тип пользователя:</i></span></label>					                    			
			<p class="myinform">
				<?php if ($user->org_type == 1) : ?>
				<a href="" class="myhref org_type_edit">Частное лицо</a>
				<?php else : ?>
				<a href="" class="myhref org_type_edit">Компания</a> 
				<?php endif; ?>
			</p>
			<?php if ($user->org_type == 2) : ?>
			<span class="ico-company"></span>
			<?php endif; ?>
		</div>
	</li>
	<li>
		<div class="input style2">
			<label><span><i class="name">E-mail:</i></span></label>
			<p class="myinform"><?=$user->email?></p>
		</div>
	</li>
	<li>
		<div class="input style2">
			<label><span><i class="name">Контактное лицо/ФИО:</i></span></label>					                    			
			<p class="myinform profile-input-wrapper">
				<a href="" class="myhref profile-input" 
					data-name="fullname"><?=trim($user->fullname) ? $user->fullname : 'Не указано'?></a>
			</p>
		</div>
	</li>
</ul>
