<div class="row relation_new" style="border-left: 1px solid black; margin-left: 60px;"  data-category="<?=$category_id?>">
	<div class="span3">
	<? echo Form::select("reference", $references, NULL, 
							array('id' => 'reference')) ?> 
	</div>
	<div class="span3">
	<? echo Form::select("parent_relation", $relations, NULL, 
							array('id' => 'parent_relation')) ?> 
	</div>
	<div class="span3">
		<? echo Form::select("parent_element", array("--Родительский элемент--"), NULL, 
							array('id' => 'parent_element')) ?> 
	</div>
	<div class="span2">
		<input type="text" id="options">
	</div>
	<div class="span2">
		<input type="text" id="custom">
	</div>
	<div class="span1">&nbsp;
		<a href="" class="icon-pencil" onclick="save(this); return false;"></a>
	</div>
</div>