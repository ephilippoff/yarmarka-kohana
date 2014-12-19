<div class="winner page-addobj">
	<section class="main-cont subscriptions cabinet">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет - Запрос на привязку к компании</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">				
				<header><span class="title">
						Запрос на привязку к компании
				</span>
				</header>
				<div class="p_cont">
					<div class="p10" style="font-size:14px; padding:20px">
						<? if ($request_type == "inn"): ?>
							<p class="p10" style="width:400px">Вы указали ИНН, который уже раннее был подтвержден для другой учетной записи: </p>
						<? endif; ?>
						<? if ($request_type == "email"): ?>
							<p class="p10" style="width:400px">Подтвердите что вы хотите привязать учетную запись к этой компании: </p>
						<? endif; ?>
						<div style="border: 1px solid gray; width:300px;padding:10px;margin-left:10px">
							<p class="p10" style="width:400px"><b><?=$parentuser->org_name?></b></br> (<?=$parentuser->email?>) </p>
							<p class="p10" style="width:400px">
								<? $logo = Imageci::getSitePaths($parentuser->filename);?>
								<img src="<?=$logo["120x90"]?>">
							</p>
						</div>

						<? if (!$request->loaded()):?>
							<p class="p10" style="width:400px">
								Если это Ваша компания, нажмите на "Отправить" для того чтобы отправить запрос на привязку вашей учетной записи.							
							</p>
							<p class="p10" style="width:400px">
								<?
									$action = "/user/user_link_request?inn=".$inn;
									if ($request_type == "email"){
										$action = "/user/user_link_request?email=".$email;
									}
								?>
								<form method="POST" action="<?=$action?>">
									<input type="hidden" name="id" value="<?=$parentuser->id?>"/>
									<input class="button blue" style="weight:bold;color:white;" type="submit" value="Отправить"/>
								</form>
							</p>
						<? else: ?>
							<p class="p10" style="width:400px">
							 	Запрос на привязку вашей учетной записи отправлен. | <a href="/user/user_link_request?method=delete_request">Отменить</a>
							</p>
							<p class="p10" style="width:400px">
							 	Чтобы ускорить процесс привязки, попросите владельца основной учетной записи этой компании, зайти в раздел "Сотрудники" и нажать кнопку "Добавить", напротив вашего email.
							</p>
							
						<? endif; ?>
					</div>
				</div>
				<div class="clear"></div>
				
			</section>
		</div>	   
		  
	</section>
</div><!--end content winner-->