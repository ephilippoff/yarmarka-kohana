 <?=HTML::script('http://yastatic.net/underscore/1.6.0/underscore-min.js')?>
  <?=HTML::script('js/adaptive/ajaxupload.js')?>
 <script type="text/javascript">
 	var source = null;
    function exec() {
        var ta = document.getElementById('output');
        ta.value = "";
        var command =  document.getElementById('command').value;
        if (!command)
        	return;
        source = new EventSource('/khbackend/users/objectload_shell?command='+command);
        source.addEventListener('message', function(e) {
        	console.log(e);
        	if (e.data =='stop')
        	{
        		source.close();
        	} else
            if (e.data !== '') {
               ta.value += e.data + '\n';
            }
        }, false);
    }

    function stop() {
    	source.close();
    }
    </script>
<p>Command:<input id="command" type="text" style="width: 80%;" value=""/></p>
<p>Output:<br/><textarea id="output" style="width: 80%; height: 25em;"></textarea></p>
<p><button type="button" onclick="exec();">start</button></p>

 <script type="text/javascript">
 	$(document).ready(function() {
	 	var self = this;
	    new AjaxUpload('fn-userfile-upload', {
	            action: '/ajax/massload/save_staticfile',
	            name: 'file',
	            data : {context :self},
	            autoSubmit: true,
	            onSubmit: function(filename, response){
	            	var self = this._settings.data.context;
			        self.category_id = $("#fn-category").val();
			        self.user_id = $("#fn-user").val();
			        this.setData({ context : self, category : self.category_id, user_id : self.user_id});
	            },
	            onComplete: function(filename, response){
	            	$(".staticfile_error").html("");
	            	$(".staticfile_success").html("");
	            	var data = null;
       				var self = this._settings.data.context; 
       				if (response) 
            			data = $.parseJSON(response);
            		if (data.error)
            			$(".staticfile_error").html(data.error);
            		if (data.objectload_id)
            			$(".staticfile_success").html("Файл сохранен:" + data.objectload_id);
            		console.log(data);
	            }
	       });
	});
   </script>

<p><h2>Статичный файл</h2>
		<select id="fn-category">
			<option value>--</option>
			<? foreach($categories as $key=>$value): ?>
				<option value="<?=$key?>"><?=$value["name"]?></option>
			<? endforeach; ?>
		</select>
		<input id="fn-user" type="text" value="327190"/>
		<button id="fn-userfile-upload">
			<div class="button blue">
				<span>Загрузить</span>
			</div>
			
		</button>	
		<div class="staticfile_error" style="color:red;"></div>
		<div class="staticfile_success" style="color:green;"></div>
</p>


 <script type="text/javascript">
    function to_archive(id) {
    	if (!confirm("В архив?")) {
			return;
		}
    	$.post( "/khbackend/users/crontask_to_archive", {id:id}, function( data ) {
		  	$('#ct_'+id).hide();
		});
    }
    function to_stop(id) {
    	if (!confirm("Остановить?")) {
			return;
		}
    	$.post( "/khbackend/users/crontask_to_stop", {id:id}, function( data ) {
		  	$('#ct_'+id).css("color","red");
		  	$('#ct_state_'+id).html("Остановлена");
		  	$('#ct_buttons_'+id).html("");
		});
    }
    </script>
<p><h2>Задачи</h2>
<a name="tasks"></a>
<table class="table table-hover table-condensed">
	<tr>
		<th>Id</th>
		<th>Name</th>
		<th>Command</th>
		<th>Comment</th>
		<th>State</th>
		<th>Created</th>
		<th>Updated</th>
	</tr>
	<?php foreach ($crontasks as $item) : ?>
		<?
			$color = '';
			if ($item->state == 1)
				$color = 'color:green';
			elseif ($item->state == 2)
				$color = 'color:gray';
			elseif ($item->state == 3)
				$color = 'color:red';
			elseif ($item->state == 5)
				$color = 'color:blue';
		?>
		<tr id="ct_<?=$item->id?>" style="<?=$color?>">			
			<td><?=$item->id?></td>
			<td><?=$item->name?></td>
			<td><?=$item->command?></td>
			<td><?=$item->comment?></td>
			<td id="ct_state_<?=$item->id?>"><?=$states[$item->state]?></td>
			<td><?=$item->created_on?></td>
			<td><?=$item->updated_on?></td>	
			<td id="ct_buttons_<?=$item->id?>">
				<? if ($item->state == 1):?>
					<span href="" class="icon-stop" onclick="to_stop(<?=$item->id?>);"></span>
				<? else: ?>
					<span href="" class="icon-trash" onclick="to_archive(<?=$item->id?>);"></span>
				<? endif; ?>
			</td>		
		</tr>
	<?php endforeach; ?>
</table>
</p>

 <script type="text/javascript">
    function set_command_line(objectload_id, user_id, category, notloaded, witherror) {
    	var filter = "";
    	var filters = [];
    	if (category)
    		filters.push('category='+category);

    	if (notloaded)
    		filters.push('notloaded=1');

    	if (witherror)
    		filters.push('witherror=1');

    	if (filters.length>0)
    		filter = ' --filter='+filters.join(",");

    	var _params = "";
    	var params = [];
    	if (objectload_id)
    		params.push("--objectload_id="+objectload_id);

    	if (user_id)
    		params.push("--user_id="+user_id);

    	if (params.length>0)
    		_params = params.join(" ");

    	$('#command').val(_params+" "+filter);
    	$('#command').css("background","lightgreen");
    	window.scrollTo(0, 0);
    	return 
    }

    function refresh_statistic(objectload_id){
    	if (!objectload_id)
    		return;

    	$.post( "/khbackend/users/objectload_refresh_statistic", {id:objectload_id}, function( data ) {
		  	console.log(data);
		  	if (data.common){
		  		$('#stat_'+objectload_id).html(data.common);
		  		_.each($('.buttons_'+objectload_id), function(item){
		  			$(item).html("");
		  		});
		  	}
		  	if (data.sub)
		  		_.each(data.sub, function(item, key){
		  			$('#stat_'+key).html(item);
		  			console.log(key);
		  		});
		},"json");
    }

    function delete_ol(id)
    {
    	if (!confirm("Удалить?")) {
			return;
		}
    	$.post( "/ajax/massload/objectload_delete", {id:id}, function( data ) {
		  	$('.ol_'+id).hide();
		});

    }

    </script>
<p>
<h2>
	Загрузки
	<? if ($qstate):?>
		(отфильтрвоано по '<?=$states_ol[$qstate]?>')
	<? endif;?>
</h2>
<p>
	<a href="/khbackend/users/objectload#objectloads">все</a> /
	<a href="/khbackend/users/objectload?state=1#objectloads">на модерации</a> /
	<a href="/khbackend/users/objectload?state=99#objectloads">ошибка</a> /
	<a href="/khbackend/users/objectload?state=5#objectloads">выполнено</a>
</p>
<a name="objectloads"></a>
<table class="table table-hover table-condensed">
	<tr>
		<th>Id</th>
		<th>Id User</th>
		<th>Email</th>
		<th>Created</th>
		<th>Category</th>
		<th>State</th>
		<th>Stat (нов./ред./все/ош.)</th>
		<th>Log</th>
		<th>Control</th>
	</tr>
	<?php foreach ($objectloads as $item) : ?>
		<tr class="ol_<?=$item->id?>">			
			<td><?=$item->id?></td>
			<td><?=$item->user_id?></td>
			<td><?=$item->email?></td>
			<td><?=$item->created_on?></td>
			<td></td>
			<td><?=$states_ol[$item->state]?></td>	
			<td id="stat_<?=$item->id?>"><?=$item->statistic_str?></td>
			<td></td>
			<td>
				<span href="" class="icon-refresh" onclick="refresh_statistic(<?=$item->id?>)"></span>
				<span href="" class="icon-retweet" onclick="set_command_line(<?=$item->id?>, <?=$item->user_id?>, null)"></span>
				<span href="" class="icon-trash" onclick="delete_ol(<?=$item->id?>);"></span>
			</td>		
		</tr>
		<?php foreach ($item->objfiles as $file) : ?>
			<tr style="border:0px;"  class="ol_<?=$item->id?>">			
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td><a target="_blank" href="/user/massload_conformities/<?=$file->category?>/<?=$item->user_id?>"><?=$file->category?></a></td>	
				<td></td>
				<td id="stat_<?=$file->id?>_<?=$file->category?>">
					<?=$file->statistic_str?>					
				</td>
				<td>
					<a href="/user/objectload_file_list/<?=$file->id?>" target="_blank">все</a>, 
					<? if ($file->error_exists):?>
						<a href="/user/objectload_file_list/<?=$file->id?>?errors=1" target="_blank">только ошибки</a>
					<? endif;?>
						
				</td>
				<td class="buttons_<?=$item->id?>">
					<span href="" class="icon-retweet" onclick="set_command_line(<?=$item->id?>, <?=$item->user_id?>, '<?=$file->category?>')"></span>
					<? if ($file->notloaded_records_exists): ?>
						<span href="" class="icon-forward" onclick="set_command_line(<?=$item->id?>, <?=$item->user_id?>, '<?=$file->category?>',1)"></span>
					<? endif;?>
					<? if ($file->error_exists): ?>
						<span href="" class="icon-remove-sign" onclick="set_command_line(<?=$item->id?>, <?=$item->user_id?>, '<?=$file->category?>',null,1)"></span>
					<? endif;?>
				</td>		
			</tr>
		<?php endforeach; ?>
	<?php endforeach; ?>
</table>
</p>