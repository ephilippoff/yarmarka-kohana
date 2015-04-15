<?=HTML::script('bootstrap/datepicker/js/bootstrap-datepicker.js')?>
<?=HTML::style('bootstrap/datepicker/css/datepicker.css')?>

<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
	// enable datepicker
	$('.dp').datepicker({
		format:	'yyyy-mm-dd'
	}).on('changeDate', function(){
		$(this).datepicker('hide');
	});	
});


</script>

<div class="reklama-linkstat">
	<div class="row">
		<div class="span5">
			<table class="table table-condensed table-striped">
				<thead>
					<tr>
						<th>Название</th>
						<th>Значение</th>
					</tr>
				</thead>
				<tr>
					<td>id</td>
					<td><?=$banner->id?></td>
				</tr>
				<tr>
					<td>Заголовок</td>
					<td><?=$banner->category->title?></td>
				</tr>
				<tr>
					<td>Города</td>
					<td><?=trim($cities,', ')?></td>
				</tr>				
				<tr>
					<td>Баннер</td>
					<td>
						<?php if (is_file(DOCROOT.'uploads/banners/menu/'.$banner->image)) : ?>
							<img style="max-width:150px" src="<?='/uploads/banners/menu/'.$banner->image?>" />							
						<?php endif;?>				
					</td>
				</tr>
				<tr>
					<td>Статус</td>
					<td>
						<?=$states[$banner->state]?>				
					</td>
				</tr>								
				<tr>
					<td>Дата старта</td>
					<td><?=$banner->date_start?></td>
				</tr>
				<tr>
					<td>Дата окончания</td>
					<td><?=$banner->date_expired?></td>
				</tr>		
			</table>			
		</div>
	</div>
	
	<div class="row">
		<div class="span5">
			<form class="form-inline" method="get" role="form">
				<div class="control-group only2" >		
					<label class="control-label">От</label>								
					<input type="text" class="input-small dp" placeholder="Дата от" name="date_start" value="<?=$date_start?>">
					<label class="control-label">до</label>								
					<input type="text" class="input-small form-control dp" placeholder="Дата до" name="date_end" value="<?=$date_end?>">
					<button type="submit" class="btn btn-default">Выбрать</button>
				</div>				
			</form>			
		</div>
	</div>
	
	<div class="row">
		<div class="span5">
			<table class="table table-condensed table-striped">
				<thead>
					<tr>
						<th>Дата</th>
						<th>Количество кликов</th>
					</tr>
				</thead>				
				<?php foreach ($stats as $value) : ?>					
						<tr>
							<td><?=date('d.m.Y', strtotime($value->date))?></td>
							<td><?=$value->clicks_count?></td>
						</tr>			
				<?php endforeach; ?>						
			</table>			
		</div>
	</div>	
	
</div>