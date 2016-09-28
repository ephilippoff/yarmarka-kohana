<script src="http://yandex.st/underscore/1.6.0/underscore-min.js"></script>
<script src="http://yandex.st/backbone/1.1.2/backbone-min.js"></script>
<script src="/static/develop/js/old/massload_set.js"></script>


<style>
    .massload {padding: 20px; }
    .massload .massload-controlsrow {}
    .massload .massload-hint {color:#999;font-size: 11px; padding:0px; padding-left:10px;}
    .massload div{padding:5px;}

    .massload .massload-category select {width:400px;height:25px;font-size:14px;}
    .massload .massload-button-load div {height:15px;}
    .massload .massload-textarea p {border: 1px solid #2370a6; width:100%; height:400px;overflow: scroll; padding:5px;}
    .massload .massload-conformities li {float:left; padding-left:10px;}

    .massload .massload-textarea .green span {color:green;}
    .massload .massload-textarea .error1 {color:#f9dada;}
    .massload .massload-textarea .background-gray b {background:#ccc;height: 20px;}

    .massload .button{border-radius:0px;color:#fff;}
    .massload .button span{color:#fff;}
    .massload .button.blue{background-color: #2370a6;}
    .massload .button.blue:hover{background-color: #3c96d6;}
    .massload .button.blue:active{top:1px;}
    .massload .button.red{background-color: #e05e46;}
    .massload .button.red:hover{background-color: #ec825f;}
    .massload .button.red:active{top:1px;}

    .massload .massload-head {font-size:14px;}
    .massload .massload-table input {border:1px solid #999; height:20px;}
    .massload .massload-table .massload-td-left {width:200px;}

    .massload .massload-table .saved {background:#c6edd5;}
    .massload .massload-table .unsaved {background:#f9dada;}

    .massload input[type="text"] {
        height: 30px;
    }
</style>
<div class="massload">
            <? 
                        $user = Auth::instance()->get_user();
                        if ($user->role == 1):
                    ?>
                        <div class="massload-controlsrow massload-checkbox-ingnore-errors">
                            <input type="text" id="fn-user" value="<?=$end_user_id?>" disabled/>
                            <label for="fn-ignore_errors">ID пользователя
                            </label>
                        </div>
                    <? endif; ?>
            <? if (count($categories)>0): ?>
                <? foreach($forms as $category=>$items): ?>
                        <div class="massload-controlsrow massload-head">
                            <b><?=$categories[$category]?></b>
                        </div>
                        <? foreach($forms[$category] as $type=>$values): ?>
                            <div class="massload-controlsrow massload-subhead">
                                <b>Поле:</b> <?=$values[0]["name"]?> 
                                    (имя : <?=$type?>; 
                                        макс. размер : <?=$cfg["fields"][$type]["maxlength"]?>;
                                            обязательное : <?=($cfg["fields"][$type]["required"] ? 'Да' : 'Нет')?>)
                                
                            </div>
                            <div class="massload-controlsrow massload-table">
                            <table>
                            <? foreach($values as $value=>$conformity): ?>
                                <? if ($value === 0) continue;?>
                                <tr class="fn-row">
                                    <td align=right class="massload-td-left"><?=$value?>  = </td>
                                    <td>
                                        <input class="fn-conformity" type="text" value="<?=$conformity?>" data-ml="<?=$category?>" data-value="<?=$value?>" data-type="<?=$type?>"/>
                                    </td>
                                    <td>
                                        <div class="button blue">   
                                            <span class="fn-save">Сохранить</span>
                                        </div>
                                    </td>
                                    <td>    
                                        <div class="button red">
                                            <span class="fn-delete">Удалить</span>
                                        </div>
                                    </td>
                                </tr>
                            <? endforeach; ?>
                            </table>
                            </div>
                        <? endforeach; ?>
                <? endforeach; ?>

            <? else: ?>
                Услуга массовой загрузки не подключена.
            <? endif; ?>
</div>