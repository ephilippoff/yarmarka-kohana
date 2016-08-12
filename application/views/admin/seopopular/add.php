<script>
    function query() {

        var value = $('#query').val() ;

        if (!value)
            return;


        $.ajax({
            url:'/khbackend/seopopular/query',
            dataType: 'json',
            data: { 
                query: value
            },
            success: function (data) {

                $('.js-query-result').html("");

                data.cities.forEach( function(city, index) {
                    var err = (city.count < data.limit) ? "style='color:red;'" : "";

                    $('.js-query-result').append('<tr '+err+'><td>'+city.title+'</td><td>'+city.count+' объявлений</td><td></td></tr>');

                    if (!err) {
                        $('.js-hidden').append('<input name="cities[]" type="hidden" value="'+city.id+'">')
                        $('.js-hidden').append('<input name="counts[]" type="hidden" value="'+city.count+'">')
                    }

                });

                if (!data.cities.length) {
                    $('.js-query-result').html("не найдено");
                }
            }
        });

    };
</script>

<form class="form-horizontal" method="post" enctype="multipart/form-data">

    <div class="control-group <?=Arr::get($errors, 'query') ? 'error' : ''?>">
        <label class="control-label">Запрос:</label>
        <div class="controls">
            <input type="text" value="<?=htmlspecialchars(Arr::get($_POST, 'query'))?>" class="input-block-level" name="query" id="query"  />
            <span class="help-inline"><?=Arr::get($errors, 'query')?></span>
        </div>
    </div>

    <div  class="control-group">
        <div class=controls>
            <table class="js-query-result">
            </table>
        </div>
        
    </div>
    

    <div class="control-group">
        <div class="js-hidden">
        </div>
        <div class="controls">
            <button type="button" class="btn btn-warning" onclick="window.query()">Проверить</button>
            <button type="submit" class="btn">Сохранить</button>
            <!--<button type="reset"  class="btn">Reset</button>-->
        </div>
    </div>
    
    <div class="control-group">
        <label class="control-label"></label>
        <div class="controls">
            <p>
                <a href="/khbackend/seopopular/index" >Вернуться в список</a>
            </p>
        </div>
    </div>
</form>