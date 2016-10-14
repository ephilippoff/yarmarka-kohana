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

    <div class="input-prepend">
     <span class="add-on">Данные</span>
     <?=Form::select('type', 
                                $types, 
                                NULL, 
                                array( 'class'=>'span2')
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
        <span class="add-on">Regdate</i></span>
        <input type="text" class="input-small dp" placeholder="date from" name="from" value="">
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
</form>

<div class="st-container">
</div>


<script>
    
    var graph = null;

    function query(filters, cb) {
        $.get('/khbackend/reklama/statistic_data', filters, cb);
    }

    function serializeForm() {
        var f = {};
        $('.js-form').serializeArray().map(function(item){ f[item.name] = item.value; });
        return f;
    }

    function submitForm (e) {
        e.preventDefault();

        var filters = serializeForm();

        d3.selectAll("svg").remove();
        graph = new Graph( filters.period );
       

        

        query(filters, response);
    }

    function response(data) {
        var data = JSON.parse(data);

        

        Object.keys(data.data).forEach(function(name){

            graph.setParseDateFunc(data.period)
            graph.drawSet(name, data.data[name]);

        });

    }

    var Graph = function() {
        this.containerClass = '.st-container';

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

    

    $('.js-submit').click(submitForm);

</script>