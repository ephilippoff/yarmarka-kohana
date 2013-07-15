<?php if ($direction == 'asc') : ?>
<th class="dropup">
<?php else : ?>
<th class="dropdown">
<?php endif; ?>
	<?php if ($direction == 'asc') : ?>
	<a href="#" onClick="return order('<?=$field_name?>', 'desc');">
	<?php else : ?>
	<a href="#" onClick="return order('<?=$field_name?>', 'asc');">
	<?php endif; ?>
	<?=$name?>
	<?php if ($sort_by == $field_name) : ?>
	<span class="caret"></span>
	<?php endif; ?>
	</a>
</th>
