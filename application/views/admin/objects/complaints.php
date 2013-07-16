<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	<h3 id="myModalLabel">Жалобы на объявление</h3>
</div>
<div class="modal-body">
	<?php foreach ($object->complaints->order_by('created', 'desc')->find_all() as $complaint) : ?>
		<small>
		<?=date('d.m.Y H:i', strtotime($complaint->created))?>
		<b><?=$complaint->user->fullname?></b> 
		</small>
		<div class="alert alert-info">
			<b><?=$complaint->subject->title?></b><br />
			<?=$complaint->text?>
		</div>
	<?php endforeach; ?>
</div>
<div class="modal-footer">
	<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
</div>
