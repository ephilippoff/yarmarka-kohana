<div class="btn-close-big"></div>
<div class="winner">
	<section class="main-cont">
		<div class="page404-bl">
				<header></header>
				<div class="m_poll">
					<div class="page404 shadow-top">
						<div class="page404-center">
							<div class="info404">
								<h2>Ошибка</h2>
								<p class="mess404">404</p>
								<p>Страница не найдена, возможно, она<br/>
								находится в другом месте или удалена.<br/>
								Я посмотрел рядом, и тоже ничего...</p>
								<p>Если вы уверены, что она должна здесь быть,<br/> 
								напишите нам через форму техподдержки.</p>
								<p>Через 30 секунд вы будете перенаправлены<br/> 
								на главную страницу сайта, для того чтобы<br/> 
								перейти сразу, нажмите на эту <a href="">ссылку</a><br/>
								или выберите рубрику.</p>	
								<br/><br/><br/> 
								<div class="spesial-bl">
									<p style="font-size: 16px;">Спонсор пропадающих страниц</p>
									<p style="font-size: 24px;">— «Бубен сисадмина»</p><br/><br/>
									<p style="font-size: 19px;padding-top: 2px;padding-bottom: 6px;">«Бубен сисадмина» </p>
									<p style="font-size: 17px;padding-bottom: 6px;margin-left: 11px;">— нет страниц нет проблем!</p>
								</div>                   				
							</div>
							<div class="ad-desk">
								<h2><span class="htext">Воспользуйтесь рубрикатором</span></h2>
								<ul>
									<?php foreach ($categories as $category) : ?>
									<li>
										<a href="<?=$category->get_url()?>">
											<div class="img hide320 hide640">
											<img src="<?=$category->get_small_icon()?>" alt="" /></div>
											<p class="text"><?=$category->title?></p>
											<div class="img hide1000"><img src="<?=$category->get_icon()?>" alt="" /></div>
										</a>
										<p class="href-bl">
											<?php foreach ($category->sub_categories->find_all() as $sub_category) : ?>
											<span class="el">
												<span class="comma">, </span>
												<a href="<?=$sub_category->get_url()?>"><?=$sub_category->title?></a>
											</span>
											<?php endforeach; ?>
									</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		
	</section>
</div><!--end content winner-->
