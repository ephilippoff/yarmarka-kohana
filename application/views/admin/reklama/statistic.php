<?=HTML::script('bootstrap/datepicker/js/bootstrap-datepicker.js')?>
<?=HTML::style('bootstrap/datepicker/css/datepicker.css')?>
<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
    // enable datepicker
    $('.dp').datepicker({
        format: 'yyyy-mm-dd'
    }).on('changeDate', function(){
        $(this).datepicker('hide');
    });

});
</script>
<style>
    .mt15 {
        margin-top: 15px;
    }

    .mr10 {
        margin-right:10px;
    }

    .st-container {
        width:100%;
        height:800px;
        font: 12px Arial;
    }

    path { 
        stroke: steelblue;
        stroke-width: 2;
        fill: none;
    }

    .line {
        stroke: red;
    }

    .axis path,
    .axis line {
        fill: none;
        stroke: grey;
        stroke-width: 1;
        shape-rendering: crispEdges;
    }
</style>
<!-- <script src="https://d3js.org/d3.v4.min.js"></script> -->
<script src="https://d3js.org/d3.v3.min.js"></script>


<form class="form-inline js-form">
    <div>
        <div class="input-prepend">
             <span class="add-on">Данные</span>
             <?=Form::select('type', 
                                        $types, 
                                        NULL, 
                                        array( 'class'=>'span2 js-type')
                                    ) ?>
        </div>

         <div class="input-prepend">
             <span class="add-on">Период</span>
             <?=Form::select('period', 
                                        $periods, 
                                        'month  ', 
                                        array( 'class'=>'span2')
                                    ) ?>
        </div>
        <div class="input-prepend">
            <span class="add-on">Дата от</i></span>
            <input type="text" class="input-small dp" placeholder="date from" name="from" value="2016-08-01">
        </div>
         <div class="input-prepend">
        <span class="add-on">Город</span>
         <?=Form::select('city', 
                                    $cities, 
                                    NULL, 
                                    array( 'class'=>'span2')
                                ) ?>
        </div>
        <input type="submit" name="" value="Filter" class="btn btn-primary js-submit">
        <input type="reset" name="" value="Clear" class="btn">
    </div>
    <div class="mt15 js-categories hidden">
        <div class="input-prepend mr10">
            <span class="add-on js-category-add">+</span>
        </div>
         <div class="input-prepend input-append js-category-template">
             <span class="add-on">Категория</span>

            <select name="category" class="span2 js-category">
                <option value="0"> ---  </option>

                 <? foreach($category_list as $key=> $item) : ?>   
                    <optgroup label="<?=$key?>">
                        <? foreach($item as $id=>$title) : ?>
                            <?php if (!in_array($id, array(42,156,72))): ?>
                                <option value="<?=$id?>"><?=$title?></option>
                            <?php endif ?>
                        <? endforeach; ?>
                    </optgroup>
                 <? endforeach; ?>

            </select>
           

            <span class="add-on js-category-remove">-</span>
           
        </div>
    </div>
</form>

<div class="st-container">
</div>


<script>
    
    

    function query(filters, cb) {
        $.get('/khbackend/reklama/statistic_data', filters, cb);
    }

    function serializeForm() {
        var f = {};
        $('.js-form').serializeArray().map(function(item){ 
            if (f[item.name]) {
                if (typeof f[item.name] == 'object') {
                    f[item.name].push(item.value)
                } else {
                    var tmp = f[item.name];

                    f[item.name] = [tmp, item.value];
                }
               
            } else {
                f[item.name] = item.value;
            } 
        });
        return f;
    }

    function addCategory(e) {
        var container = $(e.target).parent().parent();
        var newCategorySelect = $(e.target).parent().next().clone();

        $(container).append(newCategorySelect);

        $('.js-category-remove').off();
        $('.js-category-remove').click(removeCategory);
    }

    function removeCategory(e) {
        var categoriesLength = $('.js-category-template').length;
        if ( categoriesLength <= 1  || categoriesLength >=10) return;
        $(e.target).parent().remove();
    }

    function submitForm (e) {
        e.preventDefault();

        var filters = serializeForm();

        query(filters, response);
    }

    function response(data) {

        var data = JSON.parse(data);

        d3.selectAll("svg").remove();
        
        $('.st-container').attr('style', 'height:' + (800 * Object.keys(data.data).length) + 'px;');

        Object.keys(data.data).forEach(function(name){

            var newDiv = document.createElement("div");
            $('.st-container').append(newDiv);


            var graph = new Graph( newDiv );
            graph.setParseDateFunc(data.period);
            
            graph.drawSet(name, data.data[name]);

        });


    }

    var Graph = function(containerClass) {
        this.containerClass = containerClass;

        var margin = this.margin = {top: 30, right: 20, bottom: 30, left: 50};
        
        this.width = 1200 - margin.left - margin.right;
        this.height = 700 - margin.top - margin.bottom;

        // Set the ranges
        var x = this.x =  d3.time.scale().range([0, this.width]);
        var y = this.y = d3.scale.linear().range([this.height, 0]);

        // Define the axes
        this.xAxis = d3.svg.axis().scale(x)
            .orient("bottom").ticks(10);

        this.yAxis = d3.svg.axis().scale(y)
            .orient("left").ticks(15);

        // Define the line
        this.valueline = d3.svg.line()
            .x(function(d) { return x(d.period); })
            .y(function(d) { return y(d.count); });

        this.svg = d3.select(this.containerClass)
                        .append("svg")
                            .attr("width", this.width + this.margin.left + this.margin.right)
                            .attr("height", this.height + this.margin.top + this.margin.bottom)
                        .append("g")
                            .attr("transform", 
                                  "translate(" + this.margin.left + "," + this.margin.top + ")");
    };

    Graph.prototype.setParseDateFunc = function(period) {
        
        var format = '%Y';

        switch (period) {
            case 'year':
                format = '%Y';
            break;
            case 'month':
                format ='%Y-%m';
            break;
            case 'week':
                format ='%Y-%W';
            break;
            case 'day':
                format = '%Y-%m-%d';
            break;
        }
        
        this.parseDate = d3.time.format(format).parse;
    };

    Graph.prototype.drawSet = function(name, rows) {

        console.log(name,rows)
        var s = this;

        rows.forEach(function(d) {
            d.period = s.parseDate(d.period);
            d.count = +d.count;
        });

        // Scale the range of the data
        this.x.domain(d3.extent(rows, function(d) { return d.period; }));
        this.y.domain([0, d3.max(rows, function(d) { return d.count; })]);



        // Add the valueline path.
        this.svg.append("path")
            .attr("class", "line")
            .attr("d", this.valueline(rows));

        // Add the X Axis
        this.svg.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + this.height + ")")
            .call(this.xAxis);

        // Add the Y Axis
        this.svg.append("g")
            .attr("class", "y axis")
            .call(this.yAxis);

        

    };

    $('.js-type').change(function(){
        if ( $(this).val() == 'objects' ) {
            $('.js-categories').removeClass('hidden');
        } else {
            $('.js-categories').addClass('hidden');
        }
    });

    $('.js-categories').removeClass('hidden');
    $('.js-category-add').click(addCategory);
    $('.js-category-remove').click(removeCategory);

    $('.js-submit').click(submitForm);

</script>