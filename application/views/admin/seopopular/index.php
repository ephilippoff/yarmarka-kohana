<a href="/khbackend/seopopular/add" style="margin-bottom: 20px;display: inline-block;">Добавить поплярный запрос</a>

<table class="table table-hover table-condensed articles">
<tr>
    <th>id</th>
    <th>Запрос</th>
    <th>Город</th>
    <th>Количество объявлений</th>
    <th></th>
</tr>
<?php foreach ($list as $item) : ?>
<tr>
    <td><?=$item->id?></td>
    <td><?=htmlspecialchars($item->query)?></td>
    <td><?=$item->city_title?> (<?=$item->city_id?>)</td>
    <td><?=htmlspecialchars($item->count)?></td>
    <td>
        <a href="<?=URL::site('khbackend/seopopular/delete/'.$item->id)?>" class="icon-trash"></a>
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