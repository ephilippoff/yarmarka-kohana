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
			<td width="280" style="padding: 10px 0 0 10px;"><img width="172" height="33" src="http://c.yarmarka.biz/images/logo.png" alt="Ярмарка" align="left" /></td>
			<td style="text-align: right;vertical-align: bottom;padding: 10px 10px 0px 0px;color:#616161;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 14px;"><span style="color:#D44234;font-size:24px;">yarmarka</span><span style="color:#5B6772;font-size:24px;">.biz</span></td>
		</tr>
		<tr style="color:#000;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 10px;background:#ECF3F7;">
			<td width="280" style="text-align: left;padding: 0px 0px 10px 10px;font-size: 11px;">сайт бесплатных объявлений</td>
			<td style="text-align: right;vertical-align: bottom;line-height: 2.6;color:#616161;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 14px;"></td>
		</tr>
			
		<tr><td colspan="2">&nbsp;</td></tr>   
   
		<tr>
			<td width="280"><img src="http://c.yarmarka.biz/images/7_165.jpg" /></td>
			<td>
				<table align="center" cellspacing="0" cellpadding="0" border="0" width="" style="color:#000;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;border-collapse: collapse;">
					<tbody>	
						<tr><td align="right" colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 12px;"><a style="color:#fff;padding:5px 10px;background-color:#D44234;text-decoration:none;" href="http://c.yarmarka.biz/add">Подать объявление</a></td></tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr><td align="center" colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;font-size:18px;font-weight:bold;">Здравствуйте!</td></tr>
						<tr><td style="line-height: 0;" colspan="2">&nbsp;<img height="20" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>	
			

						<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">Ваши объявления перемещены в архив:</td></tr>
						<?php foreach ($objects as $obj): ?>
						<tr>
							<td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 35px;">
								
									<a href="<?php echo $domain; ?>/detail/<?php echo $obj['id']; ?>" style="color: #d44234;"> <?php echo $obj['title']; ?> </a><br>
								
							</td>
						</tr>
						<?php endforeach; ?>
						
						<?php if (!$is_new): ?>
						<tr><td style="line-height: 0;" colspan="2">&nbsp;<img height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>			
						<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">Вы можете возобновить объявления в любое время в <a style="color: #d44234;" href="http://c.yarmarka.biz/user/myads">личном кабинете</a>.</td></tr>
									
						<tr><td style="line-height: 0;" colspan="2">&nbsp;<img height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>					
						<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">Воспользуйтесь так же дополнительными сервисами в <a style="color: #d44234;" href="http://c.yarmarka.biz/user/myads">личном кабинете</a> для повышения эффективности объявлений.</td></tr>				
						
						<?php else: ?>

						<tr><td style="line-height: 0;" colspan="2">&nbsp;<img height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>			
						<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">Вы можете возобновить объявления в любое время в <a style="color: #d44234;" href="<?php echo $is_new; ?>/user">личном кабинете</a>.</td></tr>
									
						<tr><td style="line-height: 0;" colspan="2">&nbsp;<img height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>					
						<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">Воспользуйтесь так же дополнительными сервисами в <a style="color: #d44234;" href="<?php echo $is_new; ?>/user">личном кабинете</a> для повышения эффективности объявлений.</td></tr>				
						<?php endif; ?>


			
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
