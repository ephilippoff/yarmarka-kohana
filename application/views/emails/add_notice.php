<!DOCTYPE html>
<html lang=ru>
<head>
<meta charset=utf-8>
<title></title>

</head>

<body  align="center">
    <table align="center" cellspacing="0" cellpadding="0" border="0" width="640" style="color:#333333;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 12px;border-collapse: collapse;">
    
        <tbody style="color:#333333;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 12px;">
            
			<tr style="color:#333333;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 12px;">
				<td><img src="<?=URL::site('images/logo120x23.png', 'http')?>" alt="Ярмарка" align="left" /></td>
				<td style="text-align: right;vertical-align: bottom;line-height: 2.6;color:#616161;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 12px;">Газета бесплатных объявлений «Ярмарка»</td>
			</tr>
			<tr><td style="line-height: 0;" colspan="2">&nbsp;<img width="100%" height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>	
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 14px">Здравствуйте!</td></tr>
			<tr><td style="line-height: 0;" colspan="2">&nbsp;<img width="100%" height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>	
									
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;">
				<?php if ($is_edit) : ?>Ваше объявление успешно изменено.<?php else : ?> Поздравляем Вас с успешным размещением объявления на «Ярмарка-онлайн»!<?php endif;?>
			</td></tr>
			
			<tr><td style="line-height: 0;" colspan="2">&nbsp;<img width="100%" height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>				
					
			<?php if ($is_edit) : ?>							
				<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;">Теперь объявление выглядит так:</td></tr>			
			<?php endif;?>
									
			<tr><td colspan="2">
				
					<table cellspacing="0" cellpadding="0" border="0" width="100%" style="color:#333333;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size: 10px;background: #f1f4f7;line-height: 12px">
						<tr>
							<th colspan="5" style="border-bottom: 10px solid #f1f4f7;border-top: 6px solid #f1f4f7;font-size: 14px;line-height: 17px" align="left">&nbsp;<img src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D" width="13" height="12" alt="" title=""/> 
								<?=$object->title?>
							</th>
						</tr>
						<tr><td rowspan="5" width="23" style="border-bottom: 12px solid #f1f4f7"></td>							
							<td rowspan="5" width="96" style="background: #fff;border-bottom: 12px solid #f1f4f7">
								<?php if (($object->get_filename())) : 
									$paths = Imageci::getSitePaths($object->get_filename());								
									$image_path	= substr($paths['120x90'], 1, strlen($paths['120x90'])); ?>										
									<img width="96" src="<?=CI::site($image_path)?>">
								<?php endif; ?>
							</td>
							<td rowspan="5" width="8" style="border-bottom: 12px solid #f1f4f7"></td>
							<td style="font-size: 10px;border-bottom: 12px solid #f1f4f7;">
								<?=$object->full_text?>								
							</td>
							<td rowspan="5" width="10" style="border-bottom: 12px solid #f1f4f7"></td>
						</tr>
						<tr><td><span style="color:#808080">Контактные данные: </span>
							<?php foreach ($contacts as $contact) : ?>
								<?=$contact['type_name']?>: <?=$contact['value']?>
							<?php endforeach; ?>
						</td></tr>
						<tr><td><span style="color:#808080">Адрес: </span>
							<?=$city->title?>
							<?php if ($address) : ?>
								, <?=$address?>
							<?php endif ?>
						</td></tr>
						<tr><td><span style="color:#808080">Рубрика: </span><?=$category->title?></td></tr>
						<tr><td style="border-bottom: 12px solid #f1f4f7"><span style="color:#808080">&nbsp;</span></td></tr>
					</table>
				
				</td></tr>
			
			
			<tr><td style="line-height: 0;" colspan="2">&nbsp;<img width="100%" height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>	
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">
				Сообщаем, что Ваше объявление будет доступно для посетителей сайта до <?=date('d.m.Y', strtotime($object->date_expiration))?>. По окончании этого периода, Вы можете всегда возобновить публикацию объявления из  <a style="color: #4b759e;font-size: 12px" href="<?=URL::site('user/myads', 'http')?>">Личного Кабинета</a>.			
			</td></tr>
			<tr><td style="line-height: 0;" colspan="2">&nbsp;<img width="100%" height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>	
				
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">Рекомендуем воспользоваться сервисами, чтобы сделать объявление более эффективным.</td></tr>	
			<tr><td style="line-height: 0;" colspan="2">&nbsp;<img width="100%" height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>						
			
			<?php if (!$is_edit) : ?>					
				<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">Управляйте объявлением в своем <a style="color: #4b759e;font-size: 12px" href="<?=URL::site('user/myads', 'http')?>">Личном Кабинете</a>!</td></tr>				
				<tr><td style="line-height: 0;" colspan="2">&nbsp;<img width="100%" height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>	
			<?php endif;?>
			
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">Мы советуем Вам сохранить это письмо, потому что сcылки, приведенные ниже, помогут Вам будущем легко управлять Вашим объявлением.</td></tr>
			
			<tr><td colspan="2" style="line-height: 15px;">&nbsp;<img src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D" width="100%" height="15" alt="" title=""/></td></tr>
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-weight: bold;font-size: 10px;line-height: 10px;">Смотреть объявление</td></tr>
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;"><a style="color: #4b759e;font-size: 10px;font-size: 10px;line-height: 10px;" href="<?=$object->get_url()?>"><?=$object->get_url()?></a></td></tr>
			<tr><td colspan="2" style="line-height: 10px;">&nbsp;<img src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D" width="100%" height="10" alt="" title=""/></td></tr>
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-weight: bold;font-size: 10px;line-height: 10px;">Редактировать объявление</td></tr>
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;"><a style="color: #4b759e;font-size: 10px;font-size: 10px;line-height: 10px;" href="<?=CI::site('user/edit_ad/'.$object->id)?>"><?=CI::site('user/edit_ad/'.$object->id)?></a></td></tr>
			<tr><td colspan="2" style="line-height: 10px;">&nbsp;<img src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D" width="100%" height="10" alt="" title=""/></td></tr>			
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-weight: bold;font-size: 10px;line-height: 10px;">Управлять всеми своими объявлениями из личного кабинета:</td></tr>
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;"><a style="color: #4b759e;font-size: 10px;font-size: 10px;line-height: 10px;" href="<?=URL::site('user/myads', 'http')?>"><?=URL::site('user/myads', 'http')?></a></td></tr>
			
			<tr><td style="line-height: 0;" colspan="2">&nbsp;<img width="100%" height="12" title="" alt="" src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D"></td></tr>	
			<tr><td colspan="2" style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;line-height: 15px;">Спасибо, что воспользовались нашим сервисом!</td></tr>			
			
			<tr><td colspan="2" style="line-height: 0;">&nbsp;<img src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D" width="100%" height="27" alt="" title=""/></td></tr>
			<tr><td style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;text-align: right" colspan="2">С уважением, команда «Ярмарка-онлайн»</td></tr>				
			
			<tr><td colspan="2" style="line-height: 0;"><img src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw%3D%3D" width="100%" height="9" alt="" title=""/></td></tr>
        	<tr><td colspan="2" style="font-size: 9px;color: #6c6c6c;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;">Пожалуйста, не отвечайте на это письмо, т.к. указанный почтовый адрес используется только для рассылки уведомлений</td></tr>
        </tbody>
        
    </table>
</body>  
</html>