<!DOCTYPE html>
<html lang=ru>
<head>
<meta charset=utf-8>
<title></title>

</head>

<body  align="center">
    <table align="center" cellspacing="0" cellpadding="0" border="0" width="800" style="color:#333333;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 14px;border-collapse: collapse;">
    
        <tbody style="color:#333333;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 14px;">
            
			<tr style="color:#333333;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 12px;background:#ECF3F7;">
				<td style="padding: 10px 0 0 10px;"><img width="172" height="33" src="<?=URL::site('images/logo.png', 'http')?>" alt="Ярмарка" align="left" /></td>
				<td style="text-align: right;vertical-align: bottom;padding: 10px 10px 0px 0px;color:#616161;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 12px;"><span style="color:#D44234;font-size:24px;">yarmarka</span><span style="color:#5B6772;font-size:24px;">.biz</span></td>
			</tr>
			<tr style="color:#333333;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 10px;background:#ECF3F7;">
				<td style="text-align: left;padding: 0px 0px 10px 10px;font-size: 11px;">сайт бесплатных объявлений</td>
				<td style="text-align: right;vertical-align: bottom;line-height: 2.6;color:#616161;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 12px;"></td>
			</tr>			
			<tr><td colspan="2">&nbsp;</td></tr>
									
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;font-size:26px;">Здравствуйте!</td></tr>
			<tr><td style="line-height: 0;" colspan="2">&nbsp;<img width="100%" height="20" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>
			
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">Добро пожаловать на «Ярмарка-онлайн»!</td></tr>
			<tr><td style="line-height: 0;" colspan="2">&nbsp;<img width="100%" height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">Вы создали на сайте "Ярмарка-онлайн" объявление. Чтобы его увидели посетители сайта, необходимо пройти по этой ссылке:<br>
		<a style="color: #4b759e;" href="<?=CI::site('account_verification/'.$activation_code)?>?oid=<?=$object_id; ?>">
			<span class="s3"><?=CI::site('account_verification/'.$activation_code, 'http')?>?oid=<?=$object_id; ?></span>
		</a><br />
			Если приведенная ссылка не открывается, скопируйте ее и вставьте в адресную строку браузера.</td></tr>
			<tr><td style="line-height: 0;" colspan="2">&nbsp;<img width="100%" height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">Мы так же создали для Вас аккаунт на сайте "Ярмарка-онлайн".</td></tr>				
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">Ваш пароль: <b> <?=$Password; ?> </b>. Для безопасности рекомендуем сменить пароль в <a style="color: #4b759e;" href='<?=URL::site('user/profile', 'http')?>'> личном кабинете </a></td></tr>
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">Если Вы не регистрировались на «Ярмарка-онлайн», ничего не делайте или просто удалите это письмо - аккаунт создан не будет.</td></tr>
					
			<tr><td colspan="2" style="line-height: 0;">&nbsp;<img src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D" width="100%" height="27" alt="" title=""/></td></tr>
			<tr><td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;text-align: left" colspan="2">С уважением, команда «Ярмарка-онлайн»</td></tr>			
						
			<tr><td colspan="2" style="line-height: 0;"><img src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D" width="100%" height="9" alt="" title=""/></td></tr>
        	<tr><td colspan="2" style="font-size: 9px;color: #6c6c6c;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;">Пожалуйста, не отвечайте на это письмо, т.к. указанный почтовый адрес используется только для рассылки уведомлений</td></tr>
        </tbody>
        
    </table>
</body>  
</html>
