<?=HTML::script('bootstrap/datepicker/js/bootstrap-datepicker.js')?>
<?=HTML::style('bootstrap/datepicker/css/datepicker.css')?>
<style>.kohana {display: none;}</style>
	<div style="position:fixed;top:60px;left:20px;width:20%;">
		<form class="inline" method="GET" id="main_form">
			<div class="form-group">
				<label>Дата начала просмотра</label>
			</div>
			<div class="form-group">
				<input type="text" class="form-control" placeholder="От" name="date_start" value="<?php isset($_REQUEST['date_start']) ? htmlspecialchars($_REQUEST['date_start']) : ''; ?>" />
			</div>
			<div class="form-group">
				<input type="text" class="form-control" placeholder="До" name="date_end" value="<?php isset($_REQUEST['date_end']) ? htmlspecialchars($_REQUEST['date_end']) : ''; ?>" />
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-default">Применить</button>
			</div>

			<input type="hidden" name="page" value="<?php echo $data['page']; ?>" />
			<div class="pagination">
				<p class="text-info">
					Всего:
					<span class="badge"><?php echo $data['total_count']; ?></span>
				</p>
				<ul>
					<li class="<?php $data['page'] == 1 ? 'disabled' : ''; ?>">
						<?php if ($data['page'] == 1) { ?>
							<span>&lt;&lt;</span>
						<?php } else { ?>
							<a href="javascript:setPage(1);">&lt;&lt;</a>
						<?php } ?>
					</li>
					<li class="<?php $data['page'] == 1 ? 'disabled' : ''; ?>">
						<?php if ($data['page'] == 1) { ?>
							<span>&lt;</span>
						<?php } else { ?>
							<a href="javascript:setPage(<?php echo $data['page'] - 1; ?>);">&lt;</a>
						<?php } ?>
					</li>
					<?php
						$shouldBe = 3;
						$shouldBe--;
						$start = $data['page'] - floor($shouldBe / 2);
						$shouldBe -= floor($shouldBe / 2);
						if ($start < 1) {
							$shouldBe += (1 - $start);
							$start = 1;
						}
						$end = $data['page'] + $shouldBe;
						$shouldBe = 0;
						if ($end > $data['total_page']) {
							$shouldBe = $end - $data['total_page'];
							$end = $data['total_page'];
							if ($start - $shouldBe > 1) {
								$start -= $shouldBe;
							}
						}
					?>

					<?php for($i = $start; $i <= $end; $i++) { ?>
						<li class="<?php echo $i == $data['page'] ? 'active' : ''; ?>">
							<?php if ($i == $data['page']) { ?>
								<span><?php echo $i; ?></span>
							<?php } else { ?>
								<a href="javascript:setPage(<?php echo $i; ?>);">
									<?php echo $i; ?>
								</a>
							<?php } ?>
						</li>
					<?php } ?>

					<li class="<?php $data['page'] == $data['total_page'] ? 'disabled' : ''; ?>">
						<?php if ($data['page'] == $data['total_page']) { ?>
							<span>&gt;</span>
						<?php } else { ?>
							<a href="javascript:setPage(<?php echo $data['page'] + 1; ?>);">&gt;</a>
						<?php } ?>
					</li>
					<li class="<?php $data['page'] == $data['total_page'] ? 'disabled' : ''; ?>">
						<?php if ($data['page'] == $data['total_page']) { ?>
							<span>&gt;&gt;</span>
						<?php } else { ?>
							<a href="javascript:setPage(<?php echo $data['total_page']; ?>)">&gt;&gt;</a>
						<?php } ?>
					</li>
				</ul>
			</div>
		</form>
	</div>

	<div style="padding-left:20%;">
		<table class="table">
			<tr>
				<th>Дата захода</th>
				<th>Дата ухода</th>
				<th>Пользователь</th>
				<th>Название объявления</th>
				<th>#</th>
			</tr>

			<?php foreach($data['items'] as $item) { ?>
				<tr>
					<td><?php echo date('d.m.Y H:i:s', $item->date_start); ?></td>
					<td><?php echo date('d.m.Y H:i:s', $item->date_end); ?></td>
					<td><?php echo $item->user->email; ?></td>
					<td><?php echo $item->object->title; ?></td>
					<td><?php echo $item->object->id; ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>

<script type="text/javascript">
	$('[name=date_start],[name=date_end]').datepicker({
		format: 'yyyy-mm-dd'
	});
	$('#main_admin_container').removeClass('container').addClass('container-fluid');
	function setPage(x) {
		$('[name=page]').val(x);
		$('#main_form').trigger('submit');
	}
</script>