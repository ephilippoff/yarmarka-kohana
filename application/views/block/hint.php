<p class="title" aling = "center"><b><?=$hint->title?></b></p>
<div class="close-x" onclick="close_x(this)"></div>
<div class="descr"><?=$hint->description?></div>
<label>
    <input 
        class="info-tooltip-dont-show-more"
        type='checkbox'
        data-controller-character="<?=$hint->controller?>"
        onclick="add_hint_cookie($(this),'<?=$hint->identify?>');"/> Больше не показывать</label>
<div class="arr <?=$hint->position?>"><div>