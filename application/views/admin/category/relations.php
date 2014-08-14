<style>
.striped > div:nth-child(odd){
	background-color: #f9f9f9;
}
div.row {
	border-bottom: 1px dotted gray;
}

div.row:hover {
	background-color: #f5f5f5;
}
</style>
<script>
$(document).ready(function() {

	$(document.body).on('click', '.show_sub_categories', function(e){
		e.preventDefault();
		$this = $(this);

		$.get('/khbackend/category/sub_categories/'+$(this).data('id'), {level:$(this).data('level')}, function(html) {
			$this.parents('.row').after(html);
			$this.hide();
			$this.parent().find('.hide_sub_categories').show();
		});
	});

	$(document.body).on('change', '#parent_relation', function(e){
		changeParent(this);
	});
});

function add(context)
{
	var category_id = $(context).data("category");
	$.get('/khbackend/category/relation_edit/'+category_id, {}, function(html) {
		$(html).insertAfter($(context).parent().parent());
	});

}

function changeParent(context)
{
	var relation_id = $(context).val();
	$.get('/khbackend/category/parent_element/'+relation_id, {}, function(html) {
		console.log(html);
		$(context).parent().next().html(html);
	});
}

function save(context)
{
	var container = $(context).parent().parent();
	var category = $(container).data("category");
	var params = {
			reference_id : $(container).find("#reference").val(),
			parent_id : $(container).find("#parent_relation").val(),
			parent_element_id : $(container).find("#parent_element").val(),
			category_id : category,
			options : $(container).find("#options").val(),
			custom : $(container).find("#custom").val(),
	}
	console.log(params);
	$.post('/ajax/admin/relation_save', params, function(code) {
		$(container).remove();
	});
}

function delete_rel(context)
{
	var container = $(context).parent().parent();
	var id = $(container).data("id");
console.log(id);
	$.post('/ajax/admin/relation_delete', {id : id}, function(code) {
		$(container).remove();
	});
}
</script>


<div class="row">
	<div class="span1"><b>#</b></div>
	<div class="span4"><b>Title</b></div>
</div>
<div class="striped">
<?php foreach ($categories as $category) : ?>
<div class="row">
	<div class="span1"><?=$category->id?></div>
	<div class="span4"><?=$category->title?></div>
	<div class="span2">
		<a href="" class="icon-pencil" onclick="add(this); return false;" data-category="<?=$category->id?>"></a>
	</div>	
</div>
		<? 
		$relations = ORM::factory('Attribute_Relation')
			->join('reference')
					->on('reference.id', '=', 'reference_id')
			->where("category_id","=",$category->id)
			->order_by("reference.weight")
			->order_by("attribute_relation.parent_id")
			->order_by("attribute_relation.parent_element_id")
			->find_all();
		foreach ($relations as $relation):
		?>
				<div class="row relation_<?=$relation->id?>" style="border-left: 1px solid black; margin-left: 60px;" data-id="<?=$relation->id?>">
					<div class="span1"><?=$relation->id?></div>
					<div class="span2"><?=$relation->reference_obj->attribute_obj->title?></div>
					<div class="span2">&nbsp;<?=$relation->parent_obj->reference_obj->attribute_obj->title?> (<?=$relation->parent_id?>)</div>
					<div class="span2">&nbsp;<?=$relation->attribute_element_obj->title?></div>
					<div class="span2"><?=$relation->options?></div>
					<div class="span2"><?=$relation->custom?></div>
					<div class="span1">
						<a href="" class="icon-pencil"></a>
						<a href="" class="icon-trash" onclick="delete_rel(this); return false;"></a>
					</div>
				</div>
		<? endforeach; ?>
<?php endforeach; ?>
</div>