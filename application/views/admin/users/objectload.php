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

<p><h2>Загрузить статичный файл с объявлениями</h2>
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

    function refresh_statistic(objectload_id, email){
    	if (!objectload_id)
    		return;

    	$.post( "/khbackend/users/objectload_refresh_statistic", {id:objectload_id, email:email}, function( data ) {
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

    function set_comment(id)
    {
    	$("#comment_container").attr("data-objectload-id", id);
    	$("#comment_container").show();
    	$("#comment_text").val("");

    }

    </script>
<p>
<h2>
	Загрузки Объявлений
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
		<th>Stat (нов/изм/неизм/ош=все)</th>
		<th>Log</th>
		<th>Control</th>
	</tr>
	<?php foreach ($objectloads as $item) : ?>
		<tr class="ol_<?=$item->id?>">			
			<td><?=$item->id?></td>
			<td><?=$item->user_id?></td>
			<td><a href="<?=Url::site('khbackend/users/user_info/'.$item->user_id)?>" target="_blank"><?=$item->email?></a></td>
			<td><?=$item->created_on?></td>
			<td></td>
			<td>
				<? if ($item->state <> 1): ?>
					<?=$states_ol[$item->state]?>
				<? else: ?>
					<a href="#comment" onclick="set_comment(<?=$item->id?>);" style="color:red">На мод. -> отклонить<span href="" class="icon-trash"></span></a>
				<? endif; ?>
			</td>	
			<td id="stat_<?=$item->id?>"><?=$item->statistic_str?></td>
			<td></td>
			<td>
				<span href="" class="icon-refresh" onclick="refresh_statistic(<?=$item->id?>, 0)"></span>
				<span href="" class="icon-envelope" onclick="refresh_statistic(<?=$item->id?>, 1)"></span>
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

<?php if ($pagination_ol->total_pages > 1) : ?>
	<div class="row">
		<div class="span10"><?=$pagination_ol?></div>
	</div>
<?php endif; ?>


<script type="text/javascript">
	function false_moderate() {
		console.log("sdfsd");
		var olid = $("#comment_container").data("objectload-id");
		var text = $("#comment").val();
		console.log(olid, text);
		if (!olid || !text)
			return;

    	$.post( "/khbackend/users/objectload_false_moderation", {id:olid, text:text}, function( data ) {
    		console
		 	if (data.code == 200){
		 		$("#comment_container").attr("data-objectload-id", false);
				$("#comment_container").hide();
				location.reload();
		 	}
		});

		
    }
</script>
<div id="comment_container" style="display:none;">
<p>Причина отклонения загрузки:<br/><textarea id="comment" style="width: 80%; height: 15em;"></textarea></p>
<button type="button" onclick="false_moderate();">Отклонить загрузку</button>
</div>
</p>

 <script type="text/javascript">
 	$(document).ready(function() {
	 	var self = this;
	    new AjaxUpload('fn-price-upload', {
	            action: '/ajax/massload/save_pricefile',
	            name: 'file',
	            data : {context :self},
	            autoSubmit: true,
	            onSubmit: function(filename, response){
	            	var self = this._settings.data.context;
			        self.title = $("#fn-title-price").val();
			        self.user_id = $("#fn-user-price").val();
			        this.setData({ context : self, user_id : self.user_id, title: self.title});
	            },
	            onComplete: function(filename, response){
	            	$(".pricefile_error").html("");
	            	$(".pricefile_success").html("");
	            	var data = null;
       				var self = this._settings.data.context; 
       				if (response) 
            			data = $.parseJSON(response);
            		if (data.error)
            			$(".pricefile_error").html(data.error);
            		if (data.priceload_id)
            			$(".pricefile_success").html("Файл сохранен:" + data.priceload_id);
            		console.log(data);
	            }
	       });
	});
   </script>

<p><h2>Загрузить прайс</h2>
		<input id="fn-title-price" type="text" value="Прайс-лист"/>
		<input id="fn-user-price" type="text" value="327190"/>
		<button id="fn-price-upload">
			<div class="button blue">
				<span>Загрузить</span>
			</div>
			
		</button>	
		<div class="pricefile_error" style="color:red;"></div>
		<div class="pricefile_success" style="color:green;"></div>
</p>

<script type="text/javascript">
 	

 	function price_decline(id){
 		$("#pricecomment_container").attr("data-priceload-id", id);
    	$("#pricecomment_container").show();
    	$("#pricecomment_text").val("");

 	}

    function delete_pl(id)
    {
    	if (!confirm("Удалить?")) {
			return;
		}
    	$.post( "/ajax/massload/priceload_delete", {id:id}, function( data ) {
		  	$('.pl_'+id).hide();
		});

    }

    function pricestate_send(id, state) {
    	var text = null;
    	if (!id){
			olid = $("#pricecomment_container").data("priceload-id");
			text = $("#pricecomment_text").val();

			if (!olid || !text)
				return;
    	}
		else {
			olid = id;
		}		

    	$.post( "/khbackend/users/priceload_set_state", {id:olid, state : state, text:text}, function( data ) {
		 	if (data.code == 200){
		 		$("#pricecomment_container").attr("data-priceload-id", false);
				$("#pricecomment_container").hide();
				location.reload();
		 	}
		});
		if (state == 2)
    		$('.pl_'+id).css("background","green");
    	if (state == 3)
    		$('.pl_'+id).css("background","red");
		
    }

    $(document).ready(function() {
	 	var self = this;
		$(".priceload").hover(function(event) {
		   var button = $(this).find(".priceloadbutton");
		   self.id = button.attr("data-id");

		   new AjaxUpload(button, {
		    action: '/ajax/massload/priceload_toindex',
            name: 'file',
            data : {context :self},
            autoSubmit: true,
		     onSubmit: function(filename, response) {
		        this.setData({ context : self, priceload_id: self.id});
		     },
		     onComplete: function(file, response) {
		     	var data = null;
		        if (response) 
	        		data = $.parseJSON(response);
	        	console.log(data);
	        	location.reload();
		     }  
		   });
		});
	});

	function priceload_selftoindex(id) {
		if (!id)
			return;
		$.post( "/ajax/massload/priceload_selftoindex", {id:id}, function( data ) {
		 	if (data.code == 200){
		 		console.log(data);
				//location.reload();
		 	}
		});
	}

    </script>

<h2>
	Загрузки Прайсов
	<? /*if ($qstate):?>
		(отфильтрвоано по '<?=$states_ol[$qstate]?>')
	<? endif;*/?>
</h2>
<p>
	<a href="/khbackend/users/objectload#priceloads">все</a> /
	<a href="/khbackend/users/objectload?state=1#priceloads">на модерации</a> /
	<a href="/khbackend/users/objectload?state=99#priceloads">ошибка</a> /
	<a href="/khbackend/users/objectload?state=5#priceloads">выполнено</a>
</p>
<a name="priceloads"></a>
<table class="table table-hover table-condensed">
	<tr>
		<th>Id</th>
		<th>Id User</th>
		<th>Email</th>
		<th>Created</th>
		<th>Файл</th>
		<th>Title</th>
		<th>State</th>
		<th>Edit</th>
		<th>Control</th>
	</tr>
	<?php foreach ($priceloads as $item) : ?>
		<tr class="pl_<?=$item->id?> priceload">			
			<td><?=$item->id?></td>
			<td><?=$item->user_id?></td>
			<td><a href="<?=Url::site('khbackend/users/user_info/'.$item->user_id)?>" target="_blank"><?=$item->user->email?></a></td>
			<td><?=$item->created_on?></td>
			<td><a href="/<?=$item->filepath_original?>">файл</a></td>
			<td><?=$item->title?></td>
			<td>
				<? if ($item->state <> 1): ?>
					<?=$states_ol[$item->state]?></br>
				<? else: ?>
					<a href="#comment" onclick="price_decline(<?=$item->id?>);" style="color:red">Отклонить<span href="" class="icon-trash"></span></a></br>
					<a href="#comment" onclick="pricestate_send(<?=$item->id?>,2);" style="color:green">Одобрить<span href="" class="icon-ok"></span></a></br>			
				<? endif; ?>
				<a data-id="<?=$item->id?>" class="priceloadbutton" href="" style="color:purple">Загрузить в индекс (с диска)<span href="" class="icon-refresh"></span></a></br>
				<a href="#comment" onclick="priceload_selftoindex(<?=$item->id?>);" style="color:brown">Загрузить в индекс (этот файл)<span href="" class="icon-refresh"></span></a>
			</td>	
			
			<td>
				<? if ($item->table_name): ?>
					<a href="/user/pricelist/<?=$item->id?>" target="_blank">ред</a>, 
					<? /*if ($file->error_exists):?>
						<a href="/user/objectload_file_list/<?=$file->id?>?errors=1" target="_blank">только ошибки</a>
					<? endif;*/?>
				<? endif; ?>
			</td>
			<td>
				<span href="" class="icon-trash" onclick="delete_pl(<?=$item->id?>);"></span>
			</td>		
		</tr>
	<?php endforeach; ?>
</table>

<?php if ($pagination_pl->total_pages > 1) : ?>
	<div class="row">
		<div class="span10"><?=$pagination_pl?></div>
	</div>
<?php endif; ?>

<div id="pricecomment_container" style="display:none;">
<p>Причина отклонения загрузки:<br/><textarea id="pricecomment_text" style="width: 80%; height: 15em;"></textarea></p>
<button type="button" onclick="pricestate_send(null, 3);">Отклонить прайс</button>
</div>
