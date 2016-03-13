<!DOCTYPE html>
<html lang=ru>
<head>
<meta charset=utf-8>
<title></title>

</head>

<body style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 14px; margin: 0; padding: 0;">

	<div class="email_cont" style="max-width: 800px; margin: 0 auto; padding: 0px 15px;">
		<div class="header" style="background: #ECF3F7; padding: 10px;">
			<div class="logo" style="display: inline-block; font-size: 11px; width: 50%;">
				<img src="<?=URL::site('static/develop/production/images/logo.png', 'http')?>" alt="Ярмарка" style="width: 160px; height: 31px;"><br>
				Сайт бесплатных объявлений
			</div>
			<div class="site" style="display: inline-block; width: 49%; text-align: right; color: #D44234; text-decoration: none; font-size: 24px;" align="right">
				yarmarka<span style="color: #5B6772;">.biz</span>
			</div>
		</div>

		<div class="add" style="text-align: right; margin-top: 20px;" align="right">
			<a href="http://c.yarmarka.biz/add" target="_blank" style="color: #fff; text-decoration: none; background: #D44234; padding: 5px 10px;">Подать объявление</a>
		</div>

		<div class="content" style="text-align: center; margin-top: 30px;" align="center">
			<div class="img" style="float: left; width: 100%; text-align: center;" align="center">
				<img src="<?=URL::site('static/develop/production/images/2_165.jpg', 'http')?>">
			</div>
			<div class="text" style="text-align: left; line-height: 1.6; display: inline-block; width: 100%; max-width: 630px; margin-bottom: 15px;" align="left">
				<h2 style="font-size: 1.5em; display: block; text-align: center; margin-bottom: 15px;" align="center">Здравствуйте!</h2>
				Отчет по загрузке объявлений (<?=$org_name?>) <?=date( "d.m.Y H:i",strtotime($objectload->created_on))?> <br><br>
				
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
				<h3 style="font-size:1.2em; display:block; text-align:left; margin-bottom:10px; ">Категория: <?=$title?></h3>
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
				<? endforeach; ?>

				<a style="color: #d44234;" href="http://c.yarmarka.biz/user/objectload" target="_blank">Перейти к  интерфейсу массовой загрузки</a>	
				<div class="footer" style="text-align: right; font-size: 12px; margin: 10px 0;" align="right">С уважением, команда «Ярмарка-онлайн»</div>
				<span style="color: #6c6c6c; font-size: 9px;">Пожалуйста, не отвечайте на это письмо, т.к. указанный почтовый адрес используется только для рассылки уведомлений</span>
			</div>
		</div>
	</div>
	
</body>  
</html>