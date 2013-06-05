<ul>
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
				</div>
			</div>
		</div>
	</li>
	<li>	
		<div class="input style2">
			<label><span><i class="name">Логин:</i></span></label>
			<p class="myinform"><?=$user->login?></p>
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
</ul>
