<?php if (count($links)) : ?>
			<article class="iinput-bl">
				<ul><li>
			<!--	<div class="input style2">
					<div class="inp-cont-bl">-->
					<?php foreach ($links as $link) : ?>
						<div class="iPage-alert-bl w100p">
							<div class="cont">
								<div class="img"></div>
								<p class="text">
									Вас приглашают присоедениться к компании (вы можете быть присоеденены только к одной компании)
									<?php if (trim($link->user->org_name)) : ?>
										<a href="<?=URL::site('users/'.$link->user->login)?>" target="_blank"><?=$link->user->org_name?></a>
									<?php else : ?>
										"Не названа"
									<?php endif ?>
								</p>
								<span class="btn-act apply user_link_approve" data-href="<?=URL::site('ajax/approve_user_link/'.$link->id)?>"></span>
								<span class="btn-act cansel user_link_decline" data-href="<?=URL::site('ajax/decline_user_link/'.$link->id)?>"></span>
							</div>
						</div>			
					<br />
					<?php endforeach ?>
			<!--		</div>
				</div>-->
				</li></ul>
			</article>
<?php endif ?>