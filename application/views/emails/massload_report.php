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
						
			
			<tr><td style="line-height: 0;" colspan="2">&nbsp;<img width="100%" height="20" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>										
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;text-align:left;font-size:20px;">Отчет по загрузке объявлений (<?=$info["org_name"]?>)<?=$objectload->created_on?></td></tr>
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
			<tr><td colspan="2" style="line-height: 0;">&nbsp;<img src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D" width="100%" height="27" alt="" title=""/></td></tr>
			<tr><td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;text-align: left" colspan="2">С уважением, команда «Ярмарка-онлайн»</td></tr>				
			
			<tr><td colspan="2" style="line-height: 0;"><img src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D" width="100%" height="9" alt="" title=""/></td></tr>
        	<tr><td colspan="2" style="font-size: 9px;color: #6c6c6c;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;">Пожалуйста, не отвечайте на это письмо, т.к. указанный почтовый адрес используется только для рассылки уведомлений</td></tr>
        </tbody>
        
    </table>
</body>  
</html>