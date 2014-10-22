
<h2><?=$title?> (<?=$state?>)</h2>
<? if ($show_form): ?>
	<form method="post">
		<table class="table table-hover table-condensed" style="width:300px;">
			<tr>
				<th>Имя поля</th>
				<th>Название</th>
				<th>Тип</th>
			</tr>
		<? foreach($fields as $field):?>
			<tr>
				<?
					$title = $data->{$field.'_title'};
					$type = $data->{$field.'_type'};
				?>
				<td ><?=$field?></td>
				<td ><input name="<?=$field?>_title" type="text" value="<?=$title?>"/></td>
				<td >
					<select name="<?=$field?>_type">
						<? foreach ($type_fields as $name => $title) : ?>
							<option value="<?=$name?>" <? if ($type == $name) echo "selected" ?>><?=$title?></option>
						<? endforeach;?>
					</select>
				</td>
			</tr>
		<? endforeach;?>
		</table>
		<input type="submit" value="Сохранить настройки формы">
	</form>
<? endif; ?>
  <?=HTML::script('js/adaptive/ajaxupload.js')?>
 <script type="text/javascript">
	$(document).ready(function() {
	 	var self = this;
	 	var price_id = <?=$price_id?>;
		$(".pricerow_button").hover(function(event) {
		   var button = $(this).find(".priceimagebutton");
		   self.id = button.attr("data-id");
		   new AjaxUpload(button, {
		    action: '/ajax/massload/pricerow_loadimage',
            name: 'file',
            data : {context :self},
            autoSubmit: true,
		     onSubmit: function(filename, response) {
		        this.setData({ context : self, price_id: price_id, pricerow_id: self.id});
		     },
		     onComplete: function(file, response) {
		     	var data = null;
		        if (response) 
	        		data = $.parseJSON(response);

	        	if (data.error)
	        	{
            		$("#rowimage"+self.id).html(data.error);
            		return;
	        	}

	        	if (data.code == "200")
	        	{
	        		$("#rowimage"+self.id).html("<img src='"+data.filepath+"'>")
	        	}
	        	console.log(data);
		     }  
		   });
		});
	});

	function pricerow_delete(id)
	{
		if (!confirm("Остановить?")) {
			return;
		}
		var price_id = <?=$price_id?>;
		var pricerow_id = id;
    	$.post( "/ajax/massload/pricerow_delete", {id:id, price_id: price_id, pricerow_id: pricerow_id}, function( data ) {
		    if (data) 
	        	data = $.parseJSON(data);
		  	if (data.code == "200")
		  		$('#pricerow'+id).remove();
		});
	}
</script>

<table class="table table-hover table-condensed" style="width:100%">
	<tr>
		<? foreach($fields as $field):?>
			<?
				
				$field_name = $field;
				if ($fsetting->{$field.'_title'})
				{
					$field_name = $fsetting->{$field.'_title'}." (".$fsetting->{$field.'_type'}.")";
				}				
			?>
			

			<th><?=$field_name?></th>
			
		<? endforeach;?>
		<th>Загрузить</th>
		<th>Удалить</th>
	</tr>
	<? foreach($items as $item):?>
		<tr class="pricerow" id="pricerow<?=$item->id?>">
		<? foreach($fields as $field):?>
			<?
				$value = $item->{$field};	
			?>
			<td><?=$value?></td>
		<? endforeach;?>
			<td ppid="<?=$item->id?>" style="width:200px;">
				<button class="pricerow_button">
					<span class="priceimagebutton" data-id="<?=$item->id?>">
						Загрузить изображение
					</span>
				</button>
				<div id="rowimage<?=$item->id?>">
				<?
					if ($item->image)
					{
						$filepaths = Imageci::getSitePaths($item->image);
						echo "<img src='".$filepaths["120x90"]."'/>";
					}
				?>
				</div>
			</td>
			<td>
				<span href="" class="icon-trash" onclick="pricerow_delete(<?=$item->id?>);"></span>
			</td>
		</tr>
	<? endforeach;?>
</table>
