<ul>
	<li>
		<div class="input style2">
			<label><span><i class="name">Фото:</i></span></label>
			<div class="mylogo-bl">
				<form method="post" accept-charset="utf-8" enctype="multipart/form-data">
					<label class="filebutton">
						<?php if ($user->filename) : ?>
						<img src="<?=Uploads::get_file_path($user->filename, '125x83')?>" id="avatar_img" />
						<?php else : ?>
						<img src="<?=URL::site('images/mylogo.jpg')?>" id="avatar_img" />
						<?php endif; ?>
						<input type="file" name="avatar_input" class="avatar" id="avatar_input" />
					</label>
				</form>

				<span class="mydel" id="delete_avatar" <?php if ( ! $user->filename) echo "style='display:none;'" ?>></span>
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
			<p class="myinform"><?=$user->login?></p>
		</div>
	</li>
	<li>
		<div class="input style2 user-type">
			<label><span><i class="name">Тип пользователя:</i></span></label>
			<div class="inp-cont-bl ">
				<div class="inp-cont">					                    					
					<select class="iselect " name="org_type" id="org_type">
						<option value="1" <?=$user->org_type == 1 ? 'selected' : ''?>>Частное лицо</option>
						<option value="2" <?=$user->org_type == 2 ? 'selected' : ''?>>Компания</option>
					</select>
					<span class="inform">
						<span>Для частных лиц существуют ограничения на количество объявлений в рубрики: Легковые автомобили(5), Продажа квартир и комнат(3), Аренда квартир и комнат(5). Если Вам необходимо размещать больше объявлений, выберите "Компания"</span>
					</span>
					<div class="alert-bl">
						<div class="cont">
							<div class="img"></div>
							<div class="arr"></div>
							<p class="text"><span>Важно заполнить поле e-mail правильно, иначе вы не сможете активировать свой аккаунт и пользоваться многими преимуществами зарегистрированных... &nbsp;  <a href="">>>></a></span></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</li>
	<li>
		<div class="input style2">
			<label><span><i class="name">Контактное лицо/ФИО:</i></span></label>					                    			
			<p class="myinform profile-input-wrapper">
				<a href="" class="myhref profile-input" 
					data-name="fullname"><?=$user->fullname ? $user->fullname : 'Не указано'?></a>
			</p>
		</div>
	</li>
	<li>
		<div class="input style2">
			<label><span><i class="name">Телефон:</i></span></label>					                    			
			<p class="myinform profile-input-wrapper">
				<a href="" class="myhref profile-input" 
					data-name="phone"><?=$user->phone ? $user->phone : 'Не указано'?></a>
			</p>
		</div>
	</li>
	<li>
		<div class="input style2">
			<label><span><i class="name">Адрес вашей страницы :</i></span></label>					                    			
			<p class="myinform"><a href="" class="mylink" target="_blank"><?=CI::site('users/'.$user->login)?></a>
				<div class="help-bl">
					<div class="baloon">
						<div class="alert-bl">
							<div class="cont">
								<div class="img"></div><div class="arr"></div>							                							
								<p class="text"><span>Важно заполнить поле e-mail правильно, иначе вы не сможете активировать свой аккаунт следовательно лишитесь всяких фишек и плющек, а еще... &nbsp;  <a href="">>>></a></span></p>
							</div>
						</div>
					</div>
					<span class="href fr mr12"><i class="ico"></i></span>
				</div>
			</p>
		</div>
	</li>
	<li>
		<div class="input style2">
			<label><span><i class="name">О себе:</i></span></label>					                    			
			<p class="myinform">
				<textarea id="about" name="about" cols="40" rows="8" class="tiny"><?=$user->about?></textarea>
				<span class="btn-act apply about_save"></span>
			</p>
		</div>
	</li>
	<li>
		<div class="input style2 myadress">
			<label><span><i class="name">Адрес/Местоположение:</i></span></label>
			<div class="mybox">					                    			
				<div class="inp-cont-bl first">
					<div class="inp-cont">					                    					
						<select class="iselect " name="region_id" id="region_id">
						<?php foreach ($regions as $region) : ?>
							<option value="<?=$region->id?>" <?=$region_id == $region->id ? 'selected' : ''?>><?=$region->title?></option>
						<?php endforeach; ?>
						</select>
						<span class="inform">
							<span>Для частных лиц существуют ограничения на количество объявлений в рубрики: Легковые автомобили(5), Продажа квартир и комнат(3), Аренда квартир и комнат(5). Если Вам необходимо размещать больше объявлений, выберите "Компания"</span>
						</span>
						<div class="alert-bl">
							<div class="cont">
								<div class="img"></div>
								<div class="arr"></div>
								<p class="text"><span>Важно заполнить поле e-mail правильно, иначе вы не сможете активировать свой аккаунт и пользоваться многими преимуществами зарегистрированных... &nbsp;  <a href="">>>></a></span></p>
							</div>
						</div>
					</div>
				</div>
				<div class="inp-cont-bl ">
					<div class="inp-cont">					                    					
						<select class="iselect " name="city_id" id="city_id">
						<?php foreach ($cities as $city) : ?>
							<option value="<?=$city->id?>" <?=$city_id == $city->id ? 'selected' : ''?>><?=$city->title?></option>
						<?php endforeach; ?>
						</select>
						<span class="inform">
							<span>Для частных лиц существуют ограничения на количество объявлений в рубрики: Легковые автомобили(5), Продажа квартир и комнат(3), Аренда квартир и комнат(5). Если Вам необходимо размещать больше объявлений, выберите "Компания"</span>
						</span>
						<div class="alert-bl">
							<div class="cont">
								<div class="img"></div>
								<div class="arr"></div>
								<p class="text"><span>Важно заполнить поле e-mail правильно, иначе вы не сможете активировать свой аккаунт и пользоваться многими преимуществами зарегистрированных... &nbsp;  <a href="">>>></a></span></p>
							</div>
						</div>
					</div>
				</div>
				<span class="btn-act apply city_save"></span>
				<div class="input cf">
					<div class="inp-cont-bl ">
						<div class="inp-cont mystreet-bl">
							<div class="inp"><input placeholder="Введите улицу" type="text" class="mystreet" /></div>
							<span class="inform"><span>На ваш E-mail придет письмо с подтверждением регистрации</span></span>
							<div class="alert-bl"><div class="cont"><div class="img"></div><div class="arr"></div><p class="text"><span>Важно заполнить поле e-mail правильно, иначе вы не сможете активировать свой аккаунт и пользоваться многими преимуществами зарегистрированных... &nbsp;  <a href="">>>></a></span></p></div></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</li>
</ul>
