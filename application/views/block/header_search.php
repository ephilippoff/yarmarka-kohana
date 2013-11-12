<div class="title"></div>

<form action="http://<?=Region::get_current_domain()?>/search" method="get" name="search-form" id="search-form">
<div class="btn-red btn-find" onClick="$('#search-form').submit()"><span>Найти</span></div>	
<div class="seach-bl-fix">
	<div class="seach-bl">
		<select class="cusel-plagin" name="" id="sd1" onChange="$('#search-form').attr('action', this.value)">
			<option value="search">Все категории</option>
			<option class="line" value=""></option>
			<?php foreach ($categories as $category) : ?>
			<option value="<?=$category->get_url()?>"><?=$category->title?></option>
			<?php endforeach; ?>
		</select>
		<div class="input-seach"><input type="text" name="k"></div>
	</div>
</div>
</form>
