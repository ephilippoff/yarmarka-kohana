<?=HTML::style('bootstrap/css/customize.bootstrap.css')?>
<?//=HTML::style('bootstrap/css/bootstrap-responsive.min.css')?>
<script type="text/javascript">
$(document).ready(function() {
	$('#islide_services').click();
})	
</script>
<div class="winner">
	<section class="main-cont subscriptions cabinet">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">				
				<header><span class="title">
						Выгрузка объявлений
				</span>					
				</header>
				<div class="p_cont massload">
					<div id="fn-main-cont" class="cont">
						<? if ($already_agree): ?>
							<p><h2>Зачем нужна выгрузка объявлений?</h2>
								<ul>
									<li>- Вы можете выгрузить ваши активные объявления на сайте в виде файла.</li>
									<li>- Вы можете отредактировать, к примеру, цены, и загрузить измененные объявления через интерфейс массовой загрузки.</li>
									<li>- Бесплатно вы можете выгрузить не более <?=$free_limit?> объявлений в одном файле</li>
								</ul>
							</p>	
							<table class="table table-hover table-condensed" style="width:100%">
								<tr>
									<th>Категория</th>
									<th>Формат</th>
									<th>Количество объявлений</th>
									<th>Ссылка</th>
								</tr>
							
							<? foreach($categories as $name => $category): ?>
								<tr>
									<td><?=$category?></td>
									<td>xls</td>
									<td><a href="#">(?)</a></td>
									<td><a target="_blank" href="/user/objectunload_file/<?=$name?>">скачать</a></td>
								</tr>
							<? endforeach; ?>
							<ul>
							</table>
						<? else: ?>
							Для доступа к разделу, нужно Ваше согласие <a href="/user/objectload">здесь</a>
						<? endif; ?>
					</div>
				</div>
				<div class="clear"></div>
				<br />
				
			</section>
		</div>	   		  
	</section>
</div><!--end content winner-->