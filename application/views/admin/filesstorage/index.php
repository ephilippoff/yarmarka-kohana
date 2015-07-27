<?php	
	//Параметры для uri сортировок и параметры для uri фильтра
	$params = $params_for_filter = array();
	//Запоминаем сортировку для фильтра
	if (!empty($sort_by) and !empty($sort))
	{
		$params_for_filter['sort_by'] = $sort_by; 
		$params_for_filter['sort'] = $sort;		
	}	
	
	$directory = DOCROOT.kohana::$config->load('filesstorage.path');
?>

<script type="text/javascript">
	$(document).ready(function(){
		$('.fn-delete').click(function(e){
			if (!confirm('Вы уверены, что файл нужно удалить?'))
				e.preventDefault();
		});	
	});		
</script>

<table class="table table-condensed table-hover table-filesstorage promo">
	<tr>
		<th>
			Id<br>
			<?php if ($sort_by == 'id' and $sort == 'desc') : ?>
				<a class="sort" href="/<?=Request::current()->uri().URL::query(array_merge($params, array('sort_by' => 'id', 'sort' => 'asc')), false) ?>">(по возр.)</a>
			<?php else : ?>
				<a class="sort" href="/<?=Request::current()->uri().URL::query(array_merge($params, array('sort_by' => 'id', 'sort' => 'desc')), false) ?>">(по убыв.)</a>
			<?php endif;?>
		</th>
		<th>Thumb</th>
		<th>Название</th>
		<th>Заголовок</th>
		<th>Описание</th>
		<th>
			Дата загрузки<br>
			<?php if ($sort_by == 'date_created' and $sort == 'desc') : ?>
				<a class="sort" href="/<?=Request::current()->uri().URL::query(array_merge($params, array('sort_by' => 'date_created', 'sort' => 'asc')), false) ?>">(по возр.)</a>
			<?php else : ?>
				<a class="sort" href="/<?=Request::current()->uri().URL::query(array_merge($params, array('sort_by' => 'date_created', 'sort' => 'desc')), false) ?>">(по убыв.)</a>
			<?php endif;?>			
		</th>
		<th></th>
	</tr>

	<?php foreach ($files_list as $file_row) : ?>
		<tr>
			<td><?=$file_row->id?></td>
			<td class="td-thumb">
				<?php if (is_file($directory.$file_row->filename)) : ?>
				<?php	
							list($filename, $ext) = explode('.', $file_row->filename);
							if (in_array($ext, kohana::$config->load('filesstorage.extensions.images'))) : ?>				
								<a target="_blank" href="/<?=kohana::$config->load('filesstorage.path').$file_row->filename?>"><img src="/<?=kohana::$config->load('filesstorage.path').$filename.'_100x75.'.$ext?>" alt="" /></a>
							<?php else : ?>
								<span class="file-ext"><?=$ext?></span>
							<?php endif;?>
				<?php else : ?>
						<p class="label label-important">Файл не найден</p>
				<?php endif;?>
			</td>
			<td>
				<?=$file_row->filename?>
				<br>
				<input class="file-path input-xlarge" value="/<?=kohana::$config->load('filesstorage.path').$file_row->filename?>">
			</td>
			<td><?=strip_tags($file_row->title)?></td>
			<td><?=strip_tags($file_row->description)?></td>
			<td><?=$file_row->date_created?></td>
			<td>
				<a href="<?=URL::site('khbackend/filesstorage/delete/'.$file_row->id)?>" class="icon-trash delete_file fn-delete"></a>
				<span class="copy" data-url="/<?=kohana::$config->load('filesstorage.path').$file_row->filename?>" title="Копировать адрес" class="icon-file fn-copy-url"></span>
			</td>
		</tr>
	<?php endforeach;?>
	
</table>

<?php if ($pagination->total_pages > 1) : ?>
<div class="row">
	<div class="span10"><?=$pagination?></div>
	<div class="span2" style="padding-top: 55px;">
		<span class="text-info">Limit:</span>
		<?php foreach (array(50, 100, 150) as $l) : ?>
			<?php if ($l == $limit) : ?>
				<span class="badge badge-info"><?=$l?></span>
			<?php else : ?>
				<a href="#" class="btn-mini" onClick="add_to_query('limit', <?=$l?>)"><?=$l?></a>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>

<hr>

<div class="row-fluid">
	<h2>Загрузить новый файл</h2>
</div>
<div class="control-group only2" >		
	<?php if ($errors === 0) : ?>
		<p class="alert alert-success" class="bg-success">Файл загружен</p>
	<?php elseif ($errors === 1) : ?>
		<p class="alert alert-danger">Возникли ошибки, файл не загружен. Проверьте, был ли выбран файл и подходит ли его тип под требования для загрузки.</p>
	<?php endif;?>
		
	<form action="/khbackend/filesstorage/add" class="navbar-form navbar-left" enctype="multipart/form-data" method="post">
		<div class="form-group">
			<label for="only_active" class="control-label">
				<input id="filename" type="file" class="input-small" placeholder="" name="filename">
				<input type="text" name="title" placeholder="Название">
				<input class="input-xxlarge" type="text" name="description" placeholder="Описание">
				<input type="submit" value="Добавить">
			</label>			
		</div>

	</form>		
</div>
<div class="row-fluid mb50">
	Допустимы типы файлов: <?=  implode(', ', array_merge(kohana::$config->load('filesstorage.extensions.images'), kohana::$config->load('filesstorage.extensions.docs')))?>
</div>