<!DOCTYPE html>
<html lang=ru>
<head>
<meta charset=utf-8>
<title></title>

</head>

<body  align="center">
    
<table align="center" cellspacing="0" cellpadding="0" border="0" width="800" style="color:#000;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 14px;border-collapse: collapse;">
   
        <tbody style="color:#000;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 14px;">
   
		<tr style="color:#000;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 14px;background:#ECF3F7;">
			<td width="180" style="padding: 10px 0 0 10px;"><img width="172" height="33" src="<?=URL::site('images/logo.png', 'http')?>" alt="Ярмарка" align="left" /></td>
			<td style="text-align: right;vertical-align: bottom;padding: 10px 10px 0px 0px;color:#616161;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 14px;"><span style="color:#D44234;font-size:24px;">yarmarka</span><span style="color:#5B6772;font-size:24px;">.biz</span></td>
		</tr>
		<tr style="color:#000;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 10px;background:#ECF3F7;">
			<td width="180" style="text-align: left;padding: 0px 0px 10px 10px;font-size: 11px;">сайт бесплатных объявлений</td>
			<td style="text-align: right;vertical-align: bottom;line-height: 2.6;color:#616161;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 14px;"></td>
		</tr>
			
		<tr><td colspan="2">&nbsp;</td></tr>   
   
		<tr>
			<td width="180"><img src="<?=URL::site('images/1_165.jpg', 'http')?>" /></td>
			<td>
				<table align="center" cellspacing="0" cellpadding="0" border="0" width="" style="color:#000;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;border-collapse: collapse;">
					<tbody>	
						<tr><td align="right" colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 12px;"><a style="color:#fff;padding:5px 10px;background-color:#D44234;text-decoration:none;" href="http://c.yarmarka.biz/add">Подать объявление</a></td></tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr><td align="center" colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;font-size:18px;font-weight:bold;">Здравствуйте!</td></tr>
						<tr><td style="line-height: 0;" colspan="2">&nbsp;<img height="20" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>	
			
			
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;">
				<?php if ($is_edit) : ?>Ваше объявление успешно изменено.<?php else : ?> Ваше объявление теперь на сайте!<?php endif;?>
			</td></tr>			
			
			<tr><td style="line-height: 0;" colspan="2">&nbsp;<img height="10" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;"><a style="color: #d44234;" href="<?=$object->get_url()?>">Посмотреть объявление</a></td></tr>
			
			<tr><td style="line-height: 0;" colspan="2">&nbsp;<img height="10" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>
	
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;"><a style="color: #d44234;" href="<?=CI::site('user/edit_ad/'.$object->id)?>">Редактировать объявление</a></td></tr>			
			<tr><td style="line-height: 0;" colspan="2">&nbsp;<img height="10" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>
			
			<?php if ($object->moder_state >= 0 OR $is_edit) : ?>
				<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">Рекомендуем воспользоваться сервисами, чтобы сделать объявление более эффективным.</td></tr>	
			<?php else: ?>
				<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;color:red;">Объявление будет опубликовано после предварительной модерации.</td></tr>	
			<?php endif; ?>
			<tr><td style="line-height: 0;" colspan="2">&nbsp;<img height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>			
			
						<tr><td style="line-height: 0;" colspan="2">&nbsp;<img height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>	
						<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">Спасибо, что воспользовались нашим сервисом!</td></tr>
			
						<tr><td colspan="2" style="line-height: 0;">&nbsp;<img src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D" height="27" alt="" title=""/></td></tr>
						<tr><td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;text-align: right;font-size: 12px;" colspan="2">С уважением, команда «Ярмарка-онлайн»</td></tr>			
			
						<tr><td colspan="2" style="line-height: 0;"><img src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D" height="9" alt="" title=""/></td></tr>
						<tr><td colspan="2" style="font-size: 9px;color: #6c6c6c;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;">Пожалуйста, не отвечайте на это письмо, т.к. указанный почтовый адрес используется только для рассылки уведомлений</td></tr>
					</tbody>
				</table>			
			</td>
		</tr>
   							
       </tbody>        
    </table>	
	
</body>  
</html>