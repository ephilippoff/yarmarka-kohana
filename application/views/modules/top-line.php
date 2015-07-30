<div class="top-line module">
	<div class="row">
		<div class="col-md-2 col-sm-push-0 col-xs-6">
			<div class="logo">
				<a href="http://<?= Region::get_current_domain() ?>"><img src="<?= URL::site('images/logo.png') ?>" alt=""></a>
			</div>
		</div>
		<div class="col-md-2 col-md-push-8 col-xs-6">
			<?php if (!array_key_exists("HTTP_FROM", $_SERVER)) : ?>
				<div id="add-advert" class="button bg-color-crimson add-advert"><span onclick="window.location = '<?= CI::site('add') ?>'">Подать объявление</span></div>
			<?php endif ?>
		</div>
		<div class="clearfix visible-xs visible-sm"></div>
		<div class="col-md-4 col-md-pull-2">
			<!--/noindex-->
			<div class="search-cont">

				<div class="seach-bl">														
					<div class="input-seach">
						<span class="info">У нас более 100 тысяч предложений товаров, услуг и вакансий</span>
						<br>
						<form action="/search" method="get" name="search-form" id="search-form">
							<input class="search placeholder" name="k" id="search-input" value="" type="text" placeholder="Начните поиск. Например: Тойота Авенсис" autocomplete="off">
						</form>
					</div>										
					<div class="search-popup fn-search-popup border-color-crimson"></div>										
				</div>

			</div>
			<!--/noindex-->
		</div>
		<div class="col-md-4 col-md-pull-2">
			<a href="/" target="_blank">
				<img class="banner" src="/static/develop/images/banner1.gif" width="1280" height="100" alt="" title="" border="0">
			</a>						
		</div>	
	</div>										
</div>