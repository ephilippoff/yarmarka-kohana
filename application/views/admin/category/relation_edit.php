<div class="row relation_new" style="border-left: 1px solid black; margin-left: 60px;"  data-category="<?=$category_id?>">
<table>
<tr><td>Атрибут:</td><td>
		<? echo Form::select("reference", $references, NULL, 
							array('id' => 'reference')) ?> 
</tr></td>
<tr><td>Родитель:</td><td>
	<? echo Form::select("parent_relation", $relations, NULL, 
							array('id' => 'parent_relation')) ?> 
</tr></td>
<tr><td>Родительский элемент</td><td>
		<? echo Form::select("parent_element", array("--Родительский элемент--"), NULL, 
							array('id' => 'parent_element')) ?> 
</tr></td>
<tr><td>Options:</td><td>
		<input type="text" id="options">
</tr></td>
<tr><td>Custom:</td><td>
		<input type="text" id="custom">
</tr></td>
<tr><td>Обязательное</td><td>
		<input type="checkbox" id="is_required" name="is_required">
</tr></td>
<tr><td>Вес:</td><td>
		<input type="numeric" id="weight" value="0">
</tr></td>
<tr><td>Сохранить</td><td>
		<a href="" class="icon-pencil" onclick="save(this); return false;" data-category="<?=$category_id?>"></a>
</tr></td>
</table>
</div>