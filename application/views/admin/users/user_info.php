<h1><?=$user->login?> (<?=$user->email?>)</h1>
<script>
	$(document).ready(function(){

		$('#select-role').change(function(){
			var user_id = <?=$user->id?>;
			var value = $(this).val();
			$.post( "/khbackend/users/change_role", {role:value, user_id: user_id}, function( ) {
			  	alert('Успешная смена роли')
			});
		});

	})
</script>
<div class="row"> 
	<div class="span12">
		<table class="table table-hover table-condensed">
			<tr>
				<td>id</td>
				<td><?=$user->id?></td>
			</tr>
			<tr>
				<td>email</td>
				<td><?=$user->email?></td>
			</tr>
			<tr>
				<td>Зарегистрирован</td>
				<td><?=$user->regdate?></td>
			</tr>
			<tr>
				<td>Был в последний раз</td>
				<td><?=$user->last_visit_date?></td>
			</tr>
			<tr style="<?if ($user->is_blocked == 1) echo 'color:red;'?>">
				<td>Состояние</td>
				<td><?=Kohana::$config->load("dictionaries.user_states.".$user->is_blocked)?>
				<?=". По причине ".$user->block_reason?></td>
			</tr>
			<tr>
				<td>Роль</td>
				<td>
				<select id="select-role">
					<? foreach ( Kohana::$config->load("dictionaries.user_role") as $key => $value): ?>
						<option value="<?=$key?>" <? if ($user->role == $key):?>selected<?endif;?> ><?=$value?></option>
					<? endforeach; ?>
				</select>
				</td>
			</tr>
			<? if ($user->linked_to_user): ?>
				<tr>
					<td>Связан с компанией</td>
					<td><?=$user->linked_to->id?> (<?=$user->linked_to->email?>) <?=$user->linked_to->org_name?></td>
				</tr>
			<? endif; ?>
			<tr>
				<td>Тип</td>
				<td><?=Kohana::$config->load("dictionaries.org_types.".$user->org_type)?></td>
			</tr>
			<? if ($user->estimate): ?>
				<tr>
					<td>Оценка заполнения информации</td>
					<td><?=Kohana::$config->load("dictionaries.estimate_for_org_info.".$user->estimate)?></td>
				</tr>
			<? endif; ?>
			<? if ($user->org_moderate): ?>
				<tr style="<?if ($user->org_moderate == 2) echo 'color:red;'?>">
					<td>Название</td>
					<td><?=Kohana::$config->load("dictionaries.org_moderate_states.".$user->org_moderate)?></td>
				</tr>
			<? endif; ?>
			<? if ($user->org_name): ?>
				<tr>
					<td>Название</td>
					<td><?=$user->org_name?></td>
				</tr>
			<? endif; ?>
			<? if ($user->org_full_name): ?>
				<tr>
					<td>Юр. название</td>
					<td><?=$user->org_full_name?></td>
				</tr>
			<? endif; ?>
			<? if ($user->org_inn): ?>
				<tr>
					<td>ИНН</td>
					<td><?=$user->org_inn?></td>
				</tr>
			<? endif; ?>
			<? if ($user->org_post_address): ?>
				<tr>
					<td>Адрес</td>
					<td><?=$user->org_post_address?></td>
				</tr>
			<? endif; ?>
			<? if ($user->org_phone): ?>
				<tr>
					<td>Телефон</td>
					<td><?=$user->org_phone?></td>
				</tr>
			<? endif; ?>
			
			<?
				$logo = NULL;
				if ($user->filename)
					$logo = Imageci::getSitePaths($user->filename);
			?>
			<? if ($logo): ?>
			<tr>
				
				<td>Лого</td>
				<td>					
					<img src='<?=$logo["120x90"]?>'/>
				</td>
			</tr>
			<? endif; ?>
			<tr>
				<td>Описание</td>
				<td>
					<div>
						<?=$user->about?>
					</div>
				</td>
			</tr>
			<tr>
				<td>IP</td>
				<td><?=$user->ip_addr?></td>
			</tr>

		</table>
	</div>
</div>
<p>
	<a class="link" href="#" onclick="$('#userinfo').toggle()">Показать всю информацию</a>
</p>
<div class="row" id="userinfo" style="display:none"> 

	<div class="span6">
	<table class="table table-hover table-condensed">
	<?php foreach (array_keys($user->list_columns()) as $column_name) : ?>
	<?php if ( ! in_array($column_name, array('passw', 'code'))) : ?>
	<tr>
		<td><?=$column_name?></td>
		<td><?=$user->$column_name?></td>
	</tr>
	<?php endif; ?>
	<?php endforeach; ?>
	</table>
	</div>

	<div class="span6">
	<table class="table table-hover table-condensed">
	<tr>
		<th>Access log:</th>
	</tr>
	<?php foreach ($user->access->limit(30)->order_by("id","desc")->find_all() as $access) : ?>
	<tr>
		<td><?=$access->ip?> <?=date('Y-m-d H:i:s', strtotime($access->date))?></td>
	</tr>
	<?php endforeach; ?>
	</table>
	</div>
</div>

<div class="row">
	<div class="span6">
	<table class="table table-hover table-condensed">
	<tr>
		<th>User ads:</th>
	</tr>
	<?php foreach ($user->objects->order_by('real_date_created', 'desc')->find_all() as $object) : ?>
	<tr>
		<td>
			<?
				$style = "green";
				if ($object->is_published == 0)
					$style = "red";
				if ($object->active == 0)
					$style = "gray";

			?>
			<div style="color:<?=$style?>">
			<small>#<b><?=$object->id?></b> <?=date('Y-m-d H:i', strtotime($object->real_date_created))?> </small>
			<a href="<?=CI::site('detail/'.$object->id)?>" target="_blank"><?=$object->title?></a>
			</div>
		</td>
	</tr>
	<?php endforeach; ?>
	</table>
	</div>

	<div class="span6">
	<table class="table table-hover table-condensed">
	<tr>
		<th>Date</th>
		<th>User invoices:</th>
		<th>State</th>
		<th>PayDate</th>
	</tr>
	<?php foreach ($user->invoices->success()->order_by('created_on', 'desc')->find_all() as $invoice) : ?>
	<tr>
		<td><?=date('Y-m-d H:i', strtotime($invoice->created_on))?></td>
		<td><?=$invoice->description?> <span class="badge"><?=$invoice->sum?>р</span></td>
		<td><?=$invoice->state?></td>
		<td><?=date('Y-m-d H:i', strtotime($invoice->payment_date))?></td>
	</tr>
	<?php endforeach; ?>
	</table>
	</div>
</div>

<div class="row">
<div class="span6">
	<table class="table table-hover table-condensed">
	<tr>
		<th>Access log:</th>
	</tr>
	<?php foreach ($user->access->limit(30)->order_by("id","desc")->find_all() as $access) : ?>
	<tr>
		<td><?=$access->ip?> <?=date('Y-m-d H:i:s', strtotime($access->date))?></td>
	</tr>
	<?php endforeach; ?>
	</table>
	</div>
</div>