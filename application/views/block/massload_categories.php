<? foreach($categories as $key=>$value): ?>						
	<?php if (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) == '/user/massload_conformities/'.$key) : ?>
	<li><span class="noclickable"><i class="ico "></i><span><b>Настройка загрузки <?=$value?></b></span></span></li>
	<?php else : ?>
	<li><a href="/<?=URL::site('user/massload_conformities/'.$key)?>" class="clickable"><i class="ico "></i><span>Настройка "<?=$value?>"</span></a></li>
	<?php endif; ?>
<? endforeach; ?>