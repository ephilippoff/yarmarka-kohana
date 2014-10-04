<p>
	<h2>Настройки - Кеш</h2>
	<ul>
	<? foreach ($tags as $name => $title): ?>
		<li><?=$title?> <a class="btn" href="/khbackend/settings/memcache_reset/<?=$name?>">Сбросить</a></li>
	<? endforeach; ?>
	<ul>
</p>