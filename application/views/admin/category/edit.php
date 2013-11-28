<form class="form-horizontal" method="post">
	<div class="control-group">
		<label class="control-label">Title</label>
		<div class="controls">
			<input type="text" name="title" class="input-xxlarge" value="<?=$category->title?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Business types</label>
		<div class="controls">
			<?=Form::select('business_types[]', $business_types, $selected, 
			array('id' => 'business_types', 'class' => 'input-xxlarge', 'multiple' => 'multiple', 'size' => 25))?>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn">Save</button>
		</div>
	</div>
</form>