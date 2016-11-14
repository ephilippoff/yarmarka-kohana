
<form class="form-horizontal" method="post" enctype="multipart/form-data">
    <div class="control-group <?=Arr::get($errors, 'attribute') ? 'error' : ''?>">
        <label class="control-label">Атрибут:</label>
        <div class="controls">
            <?=Form::select('attribute', $attributes, Arr::get($item, 'attribute'), array('style'=>'width:100%;')) ?>
            <span class="help-inline"><?=Arr::get($errors, 'attribute')?></span>
        </div>
    </div>

    <div class="control-group <?=Arr::get($errors, 'title') ? 'error' : ''?>">
        <label class="control-label">Заголовок:</label>
        <div class="controls">
            <input type="text" value="<?=Arr::get($item, 'title')?>" class="input-block-level" name="title" id="title"  />
            <span class="help-inline"><?=Arr::get($errors, 'title')?></span>
        </div>
    </div>

    <div class="control-group <?=Arr::get($errors, 'parent_element') ? 'error' : ''?>">
        <label class="control-label">Родитель:</label>
        <div class="controls">
            <?=Form::select('parent_element', $parent_elements, Arr::get($item, 'parent_element'), array('style'=>'width:100%;')) ?>
            <span class="help-inline"><?=Arr::get($errors, 'parent_element')?></span>
        </div>
    </div>

    <div class="control-group <?=Arr::get($errors, 'seo_name') ? 'error' : ''?>">
        <label class="control-label">СЕО имя:</label>
        <div class="controls">
            <input type="text" value="<?=Arr::get($item, 'seo_name')?>" class="input-block-level" name="seo_name" id="seo_name"  />
            <span class="help-inline"><?=Arr::get($errors, 'seo_name')?></span>
        </div>
    </div>

    <div class="control-group <?=Arr::get($errors, 'title2') ? 'error' : ''?>">
        <label class="control-label">title2:</label>
        <div class="controls">
            <input type="text" value="<?=Arr::get($item, 'title2')?>" class="input-block-level" name="title2" id="title2"  />
            <span class="help-inline"><?=Arr::get($errors, 'title2')?></span>
        </div>
    </div>

    <div class="control-group <?=Arr::get($errors, 'title3') ? 'error' : ''?>">
        <label class="control-label">title3:</label>
        <div class="controls">
            <input type="text" value="<?=Arr::get($item, 'title3')?>" class="input-block-level" name="title3" id="title3"  />
            <span class="help-inline"><?=Arr::get($errors, 'title3')?></span>
        </div>
    </div>


    <div class="control-group <?=Arr::get($errors, 'url') ? 'error' : ''?>">
        <label class="control-label">url:</label>
        <div class="controls">
            <input type="text" value="<?=Arr::get($item, 'url')?>" class="input-block-level" name="url" id="url"  />
            <span class="help-inline"><?=Arr::get($errors, 'url')?></span>
        </div>
    </div>

    <div class="control-group <?=Arr::get($errors, 'weight') ? 'error' : ''?>">
        <label class="control-label">weight:</label>
        <div class="controls">
            <input type="number" value="<?=Arr::get($item, 'weight', 0)?>" class="input-block-level" name="weight" id="weight"  />
            <span class="help-inline"><?=Arr::get($errors, 'weight')?></span>
        </div>
    </div>

    <div class="control-group <?=Arr::get($errors, 'is_popular') ? 'error' : ''?>">
        <label class="control-label">is_popular:</label>
        <div class="controls">
            <input type="number" value="<?=Arr::get($item, 'is_popular', 0)?>" class="input-block-level" name="is_popular" id="is_popular"  />
            <span class="help-inline"><?=Arr::get($errors, 'is_popular')?></span>
        </div>
    </div>

    



    <div class="control-group">
        <div class="controls">
            <button type="submit" class="btn">Сохранить</button>
            <!--<button type="reset"  class="btn">Reset</button>-->
        </div>
    </div>
    
    <div class="control-group">
        <label class="control-label"></label>
        <div class="controls">
            <a href="/khbackend/attributes/element_index" >Вернуться в список</a>
        </div>
    </div>  
</form>
