<!DOCTYPE html>
<html lang=ru>
<head>
<meta charset=utf-8>
<title></title>

</head>

<body  align="center">

 <table align="center" cellspacing="0" cellpadding="0" border="0" width="800" style="color:#000;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 14px;border-collapse: collapse;">
   
        <tbody style="color:#000;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 14px;">
   
		<tr><td colspan="2">&nbsp;</td></tr>   
   
		<tr>
			<td width="180"><img src="<?=URL::site('images/2_165.jpg', 'http')?>" /></td>
			<td>
				<table align="center" cellspacing="0" cellpadding="0" border="0" width="" style="color:#000;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;border-collapse: collapse;">
					<tbody>	
						<tr><td align="right" colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 12px;"><a style="color:#fff;padding:5px 10px;background-color:#D44234;text-decoration:none;" href="http://c.yarmarka.biz/add">Подать объявление</a></td></tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr><td align="center" colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;font-size:18px;font-weight:bold;">Оплачена услуга на сайте!</td></tr>
						<tr><td style="line-height: 0;" colspan="2">&nbsp;<img height="20" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>	
			
			<tr><td align="left" colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">№ Заказа <?=$order->id?></td></tr>
			<tr><td align="left" colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">Дата оплаты <?=date('d.m.Y H:i:s', strtotime($order->payment_date))?></td></tr>

			<tr><td colspan="2" style="border:1px solid #d9d9d9">
					<table  cellspacing="0" cellpadding="0" border="0" width="100%" style="color:#333333;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 12px;text-align: center;">
						<tr style="font-size: 11px;color: #616161;"><th width="10">&nbsp;</th><th style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-weight: normal;border-bottom: 9px solid #FFFFFF;    border-top: 6px solid #FFFFFF;" align="left" style="">Услуга</th><th style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-weight: normal;border-bottom: 9px solid #FFFFFF;    border-top: 6px solid #FFFFFF;">Параметры</th><th width="90" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-weight: normal;border-bottom: 9px solid #FFFFFF;    border-top: 6px solid #FFFFFF;">Цена</th><th width="90" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-weight: normal;border-bottom: 9px solid #FFFFFF;    border-top: 6px solid #FFFFFF;">Сумма</th></tr>
						<?php foreach ($orderItems as $orderItem) : ?>				
							<tr>
								<td width="10" style="border-bottom: 9px solid #FFFFFF;    border-top: 6px solid #FFFFFF;">&nbsp;</td>
								<td align="left" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;border-bottom: 9px solid #FFFFFF;border-top: 6px solid #FFFFFF;border-bottom: 9px solid #FFFFFF;    border-top: 6px solid #FFFFFF;"><a href="http://c.yarmarka.biz/detail/<?=$orderItem->object->id?>"><?=$orderItem->title?></a></td>
								<td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;border-bottom: 9px solid #FFFFFF;    border-top: 6px solid #FFFFFF;"><?=$orderItem->service->description?></td>
								<td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;border-bottom: 9px solid #FFFFFF;    border-top: 6px solid #FFFFFF;"><?=number_format($orderItem->service->price)?> р.</td>
								<td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;border-bottom: 9px solid #FFFFFF;    border-top: 6px solid #FFFFFF;"><?=number_format($orderItem->service->price_total)?> р.</td>
							</tr>						
						<?php endforeach; ?>						
						<tr><td width="10" style="background: #f9f9f9;border-bottom: 9px solid #f9f9f9;border-top: 9px solid #f9f9f9;">&nbsp;</td><td style="background: #f9f9f9;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;border-bottom: 9px solid #f9f9f9;border-top: 9px solid #f9f9f9;" align="left">Сумма счета:</td><td style="background: #f9f9f9;border-bottom: 9px solid #f9f9f9;border-top: 9px solid #f9f9f9;"></td><td></td><td style="background: #f9f9f9;font-weight: bold;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;border-bottom: 9px solid #f9f9f9;border-top: 9px solid #f9f9f9;border-bottom: 9px solid #f9f9f9;    border-top: 6px solid #f9f9f9;"><?=number_format($order->sum)?> р.</td><td  style="background: #f9f9f9;border-bottom: 9px solid #f9f9f9;    border-top: 6px solid #f9f9f9;">&nbsp;</td></tr>
					</table>
				</td>
			</tr>
			<tr><td style="line-height: 0;" colspan="2">&nbsp;<img height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>
			
						<tr><td style="line-height: 0;" colspan="2">&nbsp;<img height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>	
						<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;"><?=$object->full_text?></td></tr>
						<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;"> E-mail: <a href="http://c.yarmarka.biz/khbackend/users/user_info/480385"><?=$user->email?></a></td></tr>
						<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;"> Контакты объявления:
							<?  foreach ($contacts->getprepared_all() as $contact): ?>
								<?=$contact->contact_clear?>,
							<? endforeach; ?>
						</td></tr>
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