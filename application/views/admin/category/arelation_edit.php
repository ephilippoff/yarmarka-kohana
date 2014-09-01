<tr class="row"><td colspan="9">
<div>
<table>
<tr><td>Атрибут:</td><td>
		<? echo Form::select("reference", $references, $arel->reference_id, 
							array('id' => 'reference')) ?> 
</tr></td>
<tr><td>Родитель:</td><td>
	<? echo Form::select("parent_relation", $relations, $arel->parent_id, 
							array('id' => 'parent_relation')) ?> 
</tr></td>
<tr><td>Родительский элемент</td><td>
		<? echo Form::select("parent_element", array("--Родительский элемент--"), $arel->parent_element_id, 
							array('id' => 'parent_element')) ?> 
</tr></td>
<tr><td>Options:</td><td>
		<input type="text" id="options" value="<?=$arel->options?>" >
</tr></td>
<tr><td>Custom:</td><td>
		<input type="text" id="custom" value="<?=$arel->custom?>">
</tr></td>
<tr><td>Обязательное</td><td>
		<input type="checkbox" id="is_required" name="is_required" <?php if ($arel->is_required) echo "checked" ?> >
</tr></td>
<tr><td>Вес:</td><td>
		<input type="numeric" id="weight" value="<?=$arel->weight?>" >
</tr></td>
<tr><td>Сохранить</td><td>
		<span href="" class="icon-pencil" onclick="update(this); return false;" data-id="<?=$arel->id?>"></span>
</tr></td>
</table>
</div>
</td></tr>		