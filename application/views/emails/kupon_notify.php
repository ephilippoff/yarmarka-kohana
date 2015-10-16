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
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr><td align="center" colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;font-size:18px;font-weight:bold;"><?=$title?></td></tr>
						<tr><td style="line-height: 0;" colspan="2">&nbsp;<img height="20" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>	
			
			<tr><td style="line-height: 0;" colspan="2">&nbsp;<img height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>
			
						<tr><td style="line-height: 0;" colspan="2">&nbsp;<img height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>	
						<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">
							<p style="padding:10px;">Количество приобретенных купонов: <?=count($kupons)?>.</p>
							<? if ($for_supplier == TRUE): ?>
								<a href="http://yarmarka.biz/detail/<?=$object_id?>"><?=$title?></a>
							<? else: ?>
								<p style="padding:10px;display:inline-block;line-height:20px;"><?=$title?>: 
									<? foreach ($kupons as $kupon) : ?>
										<span><a href="http://yarmarka.biz/kupon/print/<?=$kupon->id?>?key=<?=$key?>">№<?=Text::format_kupon_number(Model_Kupon::decrypt_number($kupon->number))?></a></span>,
									<? endforeach; ?>
								</p>
								<p style="padding:10px;">Вам необходимо предъявить эти номера, либо печатную версию купонов поставщику товара/услуги</p>
							<? endif;?>
						</td></tr>
						<tr><td colspan="2" style="line-height: 0;"><img src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D" height="9" alt="" title=""/></td></tr>
						<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">
							<? if ($for_supplier == TRUE): ?>
								<p style="padding:10px;">Контакты покупателя</p>
								<table>
									<tr><td>Email:</td><td><?=$delivery->email?></td></tr>
									<tr><td>Имя:</td><td><?=$delivery->name?></td></tr>
									<tr><td>Телефон:</td><td><?=$delivery->phone?></td></tr>
								</table>
								<p style="padding:10px;">Информация об акции</p>
								<table>
									<tr><td>Продано:</td><td><?=$sold_balance?></td></tr>
									<tr><td>Остаток:</td><td><?=$avail_balance?></td></tr>
								</table>
							<? else: ?>
								<p style="padding:10px;"><a href="http://yarmarka.biz/cart/order/<?=$order->id?>">Перейти к заказу</a></p>
							<? endif;?>
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