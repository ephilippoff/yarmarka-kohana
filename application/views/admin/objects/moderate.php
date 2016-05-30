 <?=HTML::script('bootstrap/datepicker/js/bootstrap-datepicker.js')?>
<?=HTML::style('bootstrap/datepicker/css/datepicker.css')?>
<?=HTML::script('/static/develop/js/old/nicEdit.js')?>
 <style>

        * {

        }

        ul {
            list-style: none;
            margin: 0;
            padding:0;
        }

        .content {
            width: 1280px;
            margin:0 auto;
        }

        ul.photos li {
            display: inline-block;
        }

        ul.photos img {
            max-width: 80px;
        }

        ul.filters li {
            display: inline-block;
            margin-right: 10px;
        }

        ul.filters input {
           
        }

        .fontsize-middle {
            font-size: 0.9em;
        }

        .gray {
            color:gray;
        }

        .orange {
            color: orange;
        }
        
        .control {
            text-align: center;
        }

        .control button {
            margin:0 10px;
        }

        .next-ad {
            opacity: 0.2;
            margin-top: 40px;
        }

        .mt20 {
            margin-top: 20px;
        }

        .ml20 {
            margin-left: 20px;
        }

        .is_ok {
        	background: rgba(52, 173, 52, 0.2);
        }

        .is_edit {
        	background: rgba(251, 180, 80, 0.48);
        }

        .is_banned {
        	background: rgba(216, 81, 81, 0.48);
        }


    </style>
<div style="400px;">
	<section class="app"></section>
</div>
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">asdad</div>
<?=HTML::script('static/develop/moderate-ui-prod/libs.js')?>
<?=HTML::script('static/develop/moderate-ui-prod/app.js')?>
