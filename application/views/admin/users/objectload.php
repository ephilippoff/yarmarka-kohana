 <script type="text/javascript">
 	var source = null;
    function exec() {
        var ta = document.getElementById('output');
        ta.value = "";
        var command =  document.getElementById('command').value;
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
<p>Command:<input id="command" type="text" style="width: 80%;" value="--filter=category=house"/></p>
<p>Output:<br/><textarea id="output" style="width: 80%; height: 25em;"></textarea></p>
<p><button type="button" onclick="exec();">start</button><button type="button" onclick="stop();">stop</button></p>

 <script type="text/javascript">
    function to_archive(id) {
    	$.post( "/khbackend/users/crontask_to_archive", {id:id}, function( data ) {
		  	console.log(data);
		  	$('#ct_'+id).hide();
		});
    }
    </script>
<p><h2>Задачи</h2>
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
			<td><?=$item->state?></td>
			<td><?=$item->created_on?></td>
			<td><?=$item->updated_on?></td>	
			<td>
				<span href="" class="icon-trash delete_article" onclick="to_archive(<?=$item->id?>);"></span>
			</td>		
		</tr>
	<?php endforeach; ?>
</table>
</p>
<p><h2>Загрузки</h2>
<table class="table table-hover table-condensed">
	<tr>
		<th>Id</th>
		<th>Id User</th>
		<th>Email</th>
		<th>Created</th>
		<th>Category</th>
		<th>Loaded</th>
		<th>Edited</th>
		<th>Errors</th>
		<th></th>
	</tr>
	<?php foreach ($objectloads as $item) : ?>
		<tr>			
			<td><?=$item->id?></td>
			<td><?=$item->user_id?></td>
			<td><?=$item->email?></td>
			<td><?=$item->created_on?></td>
			<td></td>	
			<td></td>
			<td></td>
			<td></td>
			<td>
				<span href="" class="icon-trash delete_article" onclick="to_archive(<?=$item->id?>);"></span>
			</td>		
		</tr>
		<?php foreach ($item->objfiles as $file) : ?>
			<tr style="border:0px;">			
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td><?=$file->category?></td>	
				<td></td>
				<td></td>
				<td></td>
				<td></td>		
			</tr>
		<?php endforeach; ?>
	<?php endforeach; ?>
</table>
</p>