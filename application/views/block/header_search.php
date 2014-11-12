<div class="title"></div>

<form action="http://<?=Region::get_current_domain()?>/search" method="get" name="search-form" id="search-form">
<div class="btn-red btn-find" onClick="$('#search-form').submit()"><span>Найти</span></div>	
<!--noindex-->
<div class="seach-bl-fix">
	<div class="seach-bl">
		<div class="input-seach"><input type="text" name="k"></div>
	</div>
</div>
<!--/noindex-->
</form>
