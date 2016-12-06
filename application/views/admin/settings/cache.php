<p>
	<h2>Настройки - Кеш</h2>
	<ul>
	<? foreach ($tags as $name => $title): ?>
		<li><?=$title?> <a class="btn" href="/khbackend/settings/memcache_reset/<?=$name?>">Сбросить</a></li>
	<? endforeach; ?>
	</ul>
	<script>
		$(document).ready(function(){

			$(".js-clearall").click(function(e){
				e.preventDefault();
				var $el = $(e.currentTarget);
				$.get('/khbackend/category/clear_cache_all/', {}, function(result) {
					console.log(result)
				});
			});

			$(".js-clear-search-url").click(function(e){
				e.preventDefault();
				var $el = $(e.currentTarget);
				$.get('/khbackend/category/clear_search_url/', {}, function(result) {
					console.log(result)
				});
			});

			$(".js-clear-seo").click(function(e){
				e.preventDefault();
				var $el = $(e.currentTarget);
				$.get('/khbackend/category/clear_search_seo/', {}, function(result) {
					console.log(result)
				});
			});

			$(".js-fixcompanies").click(function(e){
				e.preventDefault();
				var $el = $(e.currentTarget);
				$.get('/khbackend/settings/fix_companies/', {}, function(result) {
					console.log(result)
				});
			});

		});
		
	</script>
	<h2>Настройки - Кеш - новый</h2>
	<ul>
	<li><a href="#" class="js-clearall">Сбросить общий кеш</a></li>
	<li><a href="#" class="js-clear-search-url">Сбросить кеш поисковых страниц, включай счетчики</a></li>
	<li><a href="#" class="js-clear-seo">Сбросить сео заголовки футеры и проч. такие штуки</a></li>
	</ul>
	<h2>Настройки - Другие</h2>
	<ul>
		<li><a href="#" class="js-fixcompanies">Сбросить Модерацию компаний с багом</a></li>
	</ul>
</p>