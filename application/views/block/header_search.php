<div class="title"></div>
<div class="btn-red btn-find" onClick="$('#search-form').submit()"><span>Найти</span></div>
<form action="/search" method="get" name="search-form" id="search-form">
<div class="seach-bl-fix">
	<div class="seach-bl">
		<select class="cusel-plagin" name="" id="sd1" onChange="$('#search-form').attr('action', this.value)">
			<option value="search">Все категории</option>
			<option class="line" value=""></option>
			<?php foreach ($categories as $category) : ?>
			<option value="<?=$category->get_url()?>"><?=$category->title?></option>
			<?php endforeach; ?>
		</select>
		<div class="input-seach"><input type="text"></div>
	</div>
</div>
</form>
