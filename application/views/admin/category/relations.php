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

td{
	min-width:100px;
}
.icon.reference-up{background: url("/images/reference-up.gif") no-repeat; display: inline-block; width: 15px; cursor: pointer;}
.icon.reference-down{background: url("/images/reference-down.gif") no-repeat; display: inline-block; width: 15px; cursor: pointer;}
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
		$(context).parent().parent().next().find("select").parent().html(html);
	});
}

function save(context)
{
	var container = $(context).parent().parent().parent().parent();

	var category = $(context).data("category");
	console.log(context, category);
	var params = {
			reference_id : $(container).find("#reference").val(),
			parent_id : $(container).find("#parent_relation").val(),
			parent_element_id : $(container).find("#parent_element").val(),
			category_id : category,
			options : $(container).find("#options").val(),
			custom : $(container).find("#custom").val(),
			weight :$(container).find("#weight").val(),
			is_required : $(container).find("#is_required").is(':checked'),
	}
	console.log(params);
	$.post('/ajax/admin/relation_save', params, function(code) {
		$(container).remove();
	});
}

function update(context)
{

	var container = $(context).closest('tr.row');

	var id = $(context).data('id');

	var params = {
			id: id,
			reference_id : $(container).find("#reference").val(),
			parent_id : $(container).find("#parent_relation").val(),
			parent_element_id : $(container).find("#parent_element").val(),
			options : $(container).find("#options").val(),
			custom : $(container).find("#custom").val(),
			weight :$(container).find("#weight").val(),
			is_required : $(container).find("#is_required").is(':checked'),
	}

	$.post('/ajax/admin/relation_update', params, function(code) {
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

function edit_rel(context)
{
	var id = $(context).data("id");
	
	$.get('/khbackend/category/arelation_edit/'+id, {}, function(html) {
		$(html).insertAfter($(context).parent().parent());
	});	
	
}

function move_sort(id, direction)
{
	$.post('/khbackend/category/move_sort_relation', {id : id, direction : direction}, function(weight) {
		var relation = $('.fn-r'+id);
		var relation_prev = relation.prev();
		var relation_next = relation.next();
		
		relation.find('.fn-weight').html(weight);
		relation.data('weight', weight);
		relation.find('td').animate({opacity: "hide"}, 300).animate({opacity: "show"}, 300);
		
		//если вверх
		if (direction == -1)
		{
			if ( relation_prev.count != 0 && relation_prev.hasClass('fn-row') && relation.data('weight') < relation_prev.data('weight') )  			
				relation.insertBefore(relation_prev);			
		}	
		else //иначе вниз
		{
			if ( relation_next.count != 0 && relation_next.hasClass('fn-row') && relation.data('weight') > relation_next.data('weight'))  			
				relation.insertAfter(relation_next);		
		}
		
	}, 'json');
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
	<table style="border-left: 1px solid black; margin-left: 60px;"  data-category="<?=$category->id?>">
		<? 
		$relations = ORM::factory('Attribute_Relation')
			->join('reference')
					->on('reference.id', '=', 'reference_id')
			->where("category_id","=",$category->id)
			->order_by("attribute_relation.weight")
			->order_by("attribute_relation.parent_id")
			->order_by("attribute_relation.parent_element_id")
			->find_all();?>
		<? if (count($relations)>0): ?>
			<th>
			<td>
				#
			</td>
			<td>
				Атрибут
			</td>
			<td>
				Родитель
			</td>
			<td>
				Родительский элемент
			</td>
			<td>
				Options
			</td>
			<td>
				Custom
			</td>
			<td>
				Required
			</td>
			<td>
				Вес
			</td>
			<td>
				-		
			</td>
			<td>
				-		
			</td>			
			</th>
		<? endif; ?>
		<?
		foreach ($relations as $relation):
		?>
		<tr class="fn-r<?=$relation->id?> row fn-row relation_<?=$relation->id?>" data-id="<?=$relation->id?>" data-weight="<?=$relation->weight ?>" >
		<td>
			<?=$relation->id?>
		</td>
		<td>
			<?=$relation->reference_obj->attribute_obj->title?>
		</td>
		<td>
			<?=$relation->parent_obj->reference_obj->attribute_obj->title?> (<?=$relation->parent_id?>)
		</td>
		<td>
			<?=$relation->attribute_element_obj->title?>
		</td>
		<td>
			<?=$relation->options?>
		</td>
		<td>
			<?=$relation->custom?>
		</td>
		<td>
			<?=$relation->is_required?>
		</td>
		<td class="fn-weight">
			<?=$relation->weight?>
		</td>
		<td>
			<a data-id="<?=$relation->id?>" onclick="edit_rel(this); return false;" href="" class="icon-pencil"></a>
			<a href="" class="icon-trash" onclick="delete_rel(this); return false;"></a>
					
		</td>
		<td>
			<div onclick="move_sort(<?=$relation->id?>, -1);" class="icon reference-up">&nbsp;</div>
			<div onclick="move_sort(<?=$relation->id?>, 1);" class="icon reference-down">&nbsp;</div>
		</td>
		</tr>
				<?/*<div class="row relation_<?=$relation->id?>" style="border-left: 1px solid black; margin-left: 60px;" data-id="<?=$relation->id?>">
					<div class="span1"><?=$relation->id?></div>
					<div class="span2"><?=$relation->reference_obj->attribute_obj->title?></div>
					<div class="span2">&nbsp;<?=$relation->parent_obj->reference_obj->attribute_obj->title?> (<?=$relation->parent_id?>)</div>
					<div class="span2">&nbsp;<?=$relation->attribute_element_obj->title?></div>
					<div class="span2">Options:<?=$relation->options?></div>
					<div class="span2">Custom:<?=$relation->custom?></div>
					<div class="span2">Required:<?=$relation->is_required?></div>
					<div class="span2">Вес:<?=$relation->weight?></div>
					<div class="span1">
						<a href="" class="icon-pencil"></a>
						<a href="" class="icon-trash" onclick="delete_rel(this); return false;"></a>
					</div>
				</div> */?>
		<? endforeach; ?>
	</table>
<?php endforeach; ?>
</div>