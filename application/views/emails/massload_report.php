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
			<td width="180"><img src="<?=URL::site('images/2_165.jpg', 'http')?>" /></td>
			<td>
				<table align="center" cellspacing="0" cellpadding="0" border="0" width="" style="color:#000;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;border-collapse: collapse;">
					<tbody>	
						<tr><td align="right" colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 12px;"><a style="color:#fff;padding:5px 10px;background-color:#D44234;text-decoration:none;" href="http://c.yarmarka.biz/add">Подать объявление</a></td></tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr><td align="center" colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;font-size:18px;font-weight:bold;">Здравствуйте!</td></tr>
						<tr><td style="line-height: 0;" colspan="2">&nbsp;<img height="20" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>	
			
			
<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;text-align:left;font-size:20px;">Отчет по загрузке объявлений (<?=$org_name?>) <?=date( "d.m.Y H:i",strtotime($objectload->created_on))?></td></tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<? foreach($category_stat as $name=>$info): ?>	
				<? 
					$stat = $info["stat"];
					$title = $info["title"];
					$fileid = $info["id"];
				 	$new = $stat->loaded - $stat->edited; 		
					$withservice_err_ids =  array();
					if ($stat->withservice_err_ids)
				 		$withservice_err_ids = explode(",",$stat->withservice_err_ids); 
				 ?>			
				<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;text-align:left;font-size:20px;">Категория: <?=$title?></td></tr>
				<tr>
					<td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;text-align:left;">
						<ul>
							<li class="p3">Новые: <?=$new?></li>
							<li class="p3">Были отредактированы: <?=$stat->edited?></li>
							<li class="p3">Не изменились: <?=$stat->nochange?></li>
							<li class="p3"><a href="http://c.yarmarka.biz/user/objectload_file_list/<?=$fileid?>?errors=1">С ошибками: <?=$stat->error?></a></li>
							
							<? if ($stat->premium): ?>
								<li class="p3">Премиум услуги применены к <?=$stat->premium?> объявлениям</li>
							<? endif; ?>
							
							<? if (count($withservice_err_ids)): ?>
								<li style="color:red;">Премиум не применился (т.к. содержат ошибки):
									<? foreach ($withservice_err_ids as $prem_id):?>
										<a href="http://c.yarmarka.biz/user/objectload_file_list/<?=$fileid?>#<?=$prem_id?>"><?=$prem_id?></a>,
									<? endforeach; ?>
								</li>
							<? endif; ?>

						</ul>
					</td>
				</tr>
			<? endforeach; ?>
				
			<tr>
				<td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;text-align:left;">	
				<a style="color: #d44234;" href="http://c.yarmarka.biz/user/objectload" target="_blank">Перейти к  интерфейсу массовой загрузки</a>
				</td>
			</tr>			
			
			
			
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