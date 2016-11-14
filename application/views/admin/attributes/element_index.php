<a href="/khbackend/attributes/element_add" style="margin-bottom: 20px;display: inline-block;">Добавить Элемент</a>


<form class="form-inline" method="get" name="test">

    <div class="input-prepend">
     <span class="add-on">Атрибут</span>
     <?=Form::select('attribute', 
                                $attributes, 
                                Arr::get($_GET, 'attribute') , 
                                array( 'class'=>'span2')
                            ) ?>
    </div>

    <div class="input-prepend">
     <span class="add-on">Родительский элемент</span>
     <?=Form::select('parent_element', 
                                $parent_elements, 
                                Arr::get($_GET, 'parent_element'), 
                                array( 'class'=>'span2')
                            ) ?>
    </div>
    <input type="submit" value="Filter" class="btn btn-primary">
    <input type="reset" value="Clear" class="btn">
</form>

<table class="table table-hover table-condensed articles">
<tr>
    <th>id</th>
    <th>attribute</th>
    <th>title</th>
    <th>weight</th>
    <th>is_popular</th>
    <th>parent_element</th>
    <th>seo_name</th>
    <th>title2</th>
    <th>title3</th>
    <th>url</th>
    <th></th>
</tr>
<?php foreach ($list as $item) : ?>
<tr>
    <td><?=$item->id?></td>
    <td><?=$item->attribute_obj->title?> (<?=$item->attribute_obj->seo_name?>)</td>
    <td><?=$item->title?></td>
    <td><?=$item->weight?></td>
    <td><?=$item->is_popular?></td>
    <td><?=$item->parent_obj->title?></td>
    <td><?=$item->seo_name?></td>
    <td><?=$item->title2?></td>
    <td><?=$item->title3?></td>
    <td><?=$item->url?></td>
    <td>
        <a href="<?=URL::site('khbackend/attributes/element_add/'.$item->id)?>" class="icon-pencil"></a>
        <a href="<?=URL::site('khbackend/attributes/element_delete/'.$item->id)?>" class="icon-trash"></a>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php if ($pagination->total_pages > 1) : ?>
<div class="row">
    <div class="span10"><?=$pagination?></div>
    <div class="span2" style="padding-top: 55px;">
        <span class="text-info">Limit:</span>
        <?php foreach (array(50, 100, 150) as $l) : ?>
            <?php if ($l == $limit) : ?>
                <span class="badge badge-info"><?=$l?></span>
            <?php else : ?>
                <a href="#" class="btn-mini" onClick="add_to_query('limit', <?=$l?>)"><?=$l?></a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>