<div class="winner page-addobj">
	<section class="main-cont subscriptions cabinet">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет - Учетные записи сотрудников</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">				
				<header><span class="title">
						Учетные записи сотрудников, которые могут подавать объявления от имени Вашей компании
				</span>
				</header>
				<div class="p_cont">
					<div class="fl100  pt16 pb15">

						<? foreach ($users as $childuser):?>
							<div class="smallcont">
								<div class="labelcont">
									<label><span><?=$childuser->id?></span></label>
								</div>
								<div class="fieldscont">										
									<div class="">
										<div class="inp-cont">
											<div class="pt4">
												<? if (trim($childuser->fullname)): ?>
													<?=$childuser->fullname?> 
												<? else: ?>
													<Пользователь не указал имя>
												<? endif; ?>
												(<a href="/users/<?=$childuser->id?>"><?=$childuser->email?></a>) | 
												<a href="/user/employers?delete=<?=$childuser->id?>">Удалить</a>
											</div>
										</div>
									</div>									
								</div>
							</div>
						<? endforeach; ?>

						<div class="smallcont">
							<div class="labelcont">
								<label><span>Email</span></label>
							</div>
							<div class="fieldscont">										
								<div class="">
									<div class="inp-cont <? if ($error){ echo 'error';} ?>">
										<form action="/user/employers" method="POST" id="search_employer">
											<input type="text" style="width:250px" name="email"/>
											<div onclick="$('#search_employer').submit()" class="button blue"><span>Добавить</span></div>
											<? if ($error): ?>
												<span class="inform">
													<span><?=$error?></span>
												</span>
											<? elseif ($is_post AND !$error):?>
												<span class="inform">
													<span>Сотрудник добавлен</span>
												</span>
											<? endif;?>
											<span class="inform">
												<span>Впишите email вашего сотрудника, нажмите кнопку "Добавить". </br>
												Эти пользователи смогут добавлять объявления от имени Вашей компании.</br>
												Учетная запись сотрудника должна быть с типом "Частное лицо"</span>
											</span>
											
											
										</form>
							  		</div>
								</div>
							</div>									
						</div>
					</div>
				</div>
				<div class="clear"></div>
			</section>
		</div>	   

	</section>
</div><!--end content winner-->