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
			<p class="myinform">Компания</p>
		</div>

		<?php if (FALSE) : ?>
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
		<?php endif; ?>
	</li>
	<li>
		<div class="input style2">
			<label><span><i class="name">Логотип компании:</i></span></label>
			<div class="mylogo-bl">
				<form method="post" accept-charset="utf-8" enctype="multipart/form-data">
					<label class="filebutton">
						<?php if ($user->filename) : ?>
						<img src="<?=Uploads::get_file_path($user->filename, '272x203')?>" id="avatar_img" />
						<?php else : ?>
						<img src="<?=URL::site('images/mylogo.jpg')?>" id="avatar_img" />
						<?php endif; ?>
						<input type="file" name="avatar_input" class="avatar" id="avatar_input" />
					</label>
				</form>

				<span class="mydel" id="delete_avatar" <?php if ( ! $user->filename) echo "style='display:none;'" ?>></span>
			</div>

			<div class="alert-bl profile-alert">
				<div class="cont">
					<div class="img"></div>
					<div class="arr"></div>
					<p class="text"><span></span></p>
				</div>
			</div>
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
			<label><span><i class="name">Логин:</i></span></label>					                    			
			<p class="myinform profile-input-wrapper">
				<a href="" class="myhref profile-input" 
					data-name="login"><?=$user->login?></a>
			</p>

			<div class="alert-bl profile-alert">
				<div class="cont">
					<div class="img"></div>
					<div class="arr"></div>
					<p class="text"><span></span></p>
				</div>
			</div>
		</div>
	</li>
	<li>
		<div class="input style2">
			<label><span><i class="name">Название компании:</i></span></label>					                    			
			<p class="myinform profile-input-wrapper">
				<a href="" class="myhref profile-input" 
					data-name="org_name"><?=$user->org_name ? $user->org_name : 'Не указано'?></a>
			</p>

			<div class="alert-bl profile-alert">
				<div class="cont">
					<div class="img"></div>
					<div class="arr"></div>
					<p class="text"><span></span></p>
				</div>
			</div>
		</div>
	</li>
	<li>
		<div class="input style2">
			<label><span><i class="name">Адрес веб страницы:</i></span></label>					                    			
			<p class="myinform profile-input-wrapper">
				<a href="" class="myhref profile-input" 
					data-name="url"><?=$user->url ? $user->url : 'Не указано'?></a>
			</p>
		</div>
	</li>
	<li>
		<div class="input style2">
			<label><span><i class="name">Адрес вашей страницы :</i></span></label>					                    			
			<p class="myinform"><a href="" class="mylink user_page" target="_blank"><?=CI::site('users/'.$user->login)?></a>
				<div class="help-bl">
					<div class="baloon">
					</div>
					<span class="href fr mr12"><i class="ico"></i></span>
				</div>
			</p>
		</div>
	</li>
	<li id="about_edit" style="display:none">
		<div class="input style2">
			<label><span><i class="name">О компании:</i></span></label>					                    			
			<p class="myinform">
				<textarea id="about" name="about" cols="40" rows="8" class="tiny"><?=$user->about?></textarea>
			</p>
			<div class="profile-about-actions">
				<span class="btn-act apply about_save"></span>
				<span class="btn-act cansel about_cancel"></span>
			</div>
		</div>
	</li>
	<li id="about_text">
		<div class="input style2">
			<label><span><i class="name">О компании:</i></span></label>					                    			
			<div class="profile-text">
				<span class="about_text"><?=$user->about?></span>
			</div>
			<div class="profile-about-edit">
				<span class="btn-edit about_edit"></span>
			</div>
		</div>
	</li>
	<li id="address_text">
		<div class="input style2">

			<label><span><i class="name">Адрес вашей компании:</i></span></label>					                    			
			<p class="myinform">
				<?php if ( ! $user->user_city->loaded() AND ! $user->org_address) : ?>
				<a href="" class="myhref address_edit">Не указано</a>
				<?php else : ?>
				<a href="" class="myhref address_edit"><?=$user->user_city->loaded() ? $user->user_city->title.',' : ''?> <?=$user->org_address?></a>
				<?php endif; ?>
			</p>
		</div>
	</li>
	<li id="address_edit" style="display:none">
		<div class="input style2 myadress">
			<label><span><i class="name">Адрес вашей компании:</i></span></label>
			<div class="mybox">					                    			
				<div class="inp-cont-bl first">
					<div class="inp-cont">					                    					
						<select class="iselect " name="region_id" id="region_id">
						<?php foreach ($regions as $region) : ?>
							<option value="<?=$region->id?>" <?=$region_id == $region->id ? 'selected' : ''?>><?=$region->title?></option>
						<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="inp-cont-bl ">
					<div class="inp-cont">					                    					
						<select class="iselect " name="city_id" id="city_id">
						<?php foreach ($cities as $city) : ?>
							<option value="<?=$city->id?>" <?=$city_id == $city->id ? 'selected' : ''?>><?=$city->title?></option>
						<?php endforeach; ?>
						</select>
					</div>
				</div>
				<span class="btn-act apply city_save"></span>
				<span class="btn-act cansel city_cancel"></span>
				<div class="input cf">
					<div class="inp-cont-bl ">
						<div class="inp-cont mystreet-bl">
							<div class="inp"><input placeholder="Введите улицу" name="org_address" value="<?=$user->org_address?>" type="text" class="mystreet" /></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</li>
</ul>

