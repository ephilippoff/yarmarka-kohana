<script type="text/javascript">
	function setFollowMe(id)
	{
		$.post( "/khbackend/phones/setfollowme", {id:id}, function( data ) {
			console.log(data);
		},"json");
	}

	function getFollowMe(id)
	{
		$.post( "/khbackend/phones/getfollowme", {id:id}, function( data ) {
			console.log(data);
		},"json");
	}
</script>
<form class="form-horizontal" method="post" enctype="multipart/form-data">
	<div class="control-group <?=Arr::get($errors, 'contact') ? 'error' : ''?>">
		<label class="control-label">Телефон</label>
		<div class="controls">
			<input type="text" value="<?=Arr::get($_POST, 'contact')?>" class="input-block-level" name="contact" id="contact"  />
			<span class="help-inline"><?=Arr::get($errors, 'contact')?></span>
		</div>
	</div>
	<div class="control-group <?=Arr::get($errors, 'sid_id') ? 'error' : ''?>">
		<label class="control-label">Sid</label>
		<div class="controls">
			<select name="sid_id" id="sid_id"  >
			<? foreach ($sids as $value) : ?>
				<<option value ="<?=$value?>" <? if (Arr::get($_POST, 'sid_id') == $value) echo "selected"; ?>><?=$value?></option>
			<? endforeach; ?>
			</select>
			<span class="help-inline"><?=Arr::get($errors, 'sid_id')?></span>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Сохранить</button>
			<!--<button type="reset"  class="btn">Reset</button>-->
		</div>
	</div>
	
</form>
<table class="table table-hover table-condensed promo">
	<tr>
		<th>Id</th>
		<th>contact</th>
		<th>sid</th>
		<th>active</th>

		<th></th>
	</tr>
	<?php foreach ($list as $item) : ?>
		<tr>			
			<td><?=$item->id?></td>
			<td><?=$item->contact->contact_clear?></td>
			<td><?=$item->sid_id?></td>
			<td><?=$item->active?></td>
			<td>
				<button onclick="setFollowMe(<?=$item->id?>);">follow</button>
				<button onclick="getFollowMe(<?=$item->id?>);">get</button>

			</td>						
		</tr>
	<?php endforeach; ?>
</table>