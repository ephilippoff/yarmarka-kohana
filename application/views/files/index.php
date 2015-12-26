<?php 

	$baseFields = '<input type="hidden" name="path" value="' . $currentRelativePath . '" />';
	if ($ckEditor !== NULL) {
		$baseFields .= '<input type="hidden" name="CKEditor" value="' . $ckEditor . '" />';
	}
	if ($ckEditorFuncNum !== NULL) {
		$baseFields .= '<input type="hidden" name="CKEditorFuncNum" value="' . $ckEditorFuncNum . '" />';
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Файлы и папки</title>
</head>
<body>
	<div>
		<?php if (!empty($up)) { ?>
			<a href="<?php echo $up; ?>">Вверх</a>&nbsp;|&nbsp;
		<?php } ?>
		<form method="POST" action="/files/createFolder" style="display:inline;">
			<?php echo $baseFields; ?>
			<input type="text" name="name" value="" />
			<input type="submit" value="Создать папку" />
		</form>
		&nbsp;|&nbsp;
		<form method="POST" action="/files/uploadFile" enctype="multipart/form-data" style="display:inline;">
			<?php echo $baseFields; ?>
			<input type="file" name="file" value="" />
			<input type="submit" value="Загрузить файл" />
		</form>
	</div>
	<table>
		<tr><th></th><th>Папки</th></tr>
		<?php foreach($items as $item) { ?>
		<?php if ($item['type'] != 'd') { continue; } ?>
		<tr>
			<td>
				<a href="<?php echo $item['href']; ?>"><?php echo $item['name']; ?></a>	
			</td>
		</tr>
		<?php } ?>
		<tr><th>Файлы</th></tr>
		<?php foreach($items as $item) { ?>
		<?php if ($item['type'] != 'f') { continue; } ?>
		<tr>
			<td>
				<?php if ($ckEditor !== NULL && $ckEditorFuncNum !== NULL) { ?>
					<a href="javascript: ok('<?php echo $item['relativePath']; ?>');"><?php echo $item['name']; ?></a>	
				<?php } else { ?>
					<a href="<?php echo $item['relativePath']; ?>"><?php echo $item['name']; ?></a>
				<?php } ?>
			</td>
		</tr>
		<?php } ?>
	</table>

	<?php if ($ckEditorFuncNum !== NULL) { ?>
	<script type="text/javascript">
		function ok(filePath) {
			window.opener.CKEDITOR.tools.callFunction( <?php echo $ckEditorFuncNum; ?>, filePath );
            window.close();
		}
	</script>
	<?php } ?>
</body>
</html>