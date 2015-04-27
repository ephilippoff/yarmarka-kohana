<script type="text/javascript">
	$(document).ready(function(){
		$('.fn_delete_verified_contact').live('click', function(){
			if (confirm('Объявления с этим контактом будут сняты с публикации. Хотите удалить контакт?')) {
				var obj = this;
				$.post('/ajax/delete_user_contact', {contact_id:$(this).data('id')}, function(json){
					if (json.code == 200) {
						$(obj).parents('.contact').remove();
					}
				}, 'json');
			}
		});		
	})
</script>

<div class="winner cabinet contacts page-addobj">
	<section class="main-cont">
		<div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет</h1></div>
		<div class="fl100 shadow-top z1 persomal_room">
			<?=View::factory('user/_left_menu')?>
			<section class="p_room-inner">
				<header><span class="title">Управление контактами</span></header>
				<div class="p_cont secure-bl myinfo">
					<div class="fl100 pb15 pt15">
						<div class="smallcont">В этом разделе Вы можете увидеть все контакты, привязанные к вашему аккаунту. Также здесь можно удалять контакты. В этом случае происходит их отвязка от вашего аккаунта. Кроме того, все объявления, в которых были указаны удаленные контакты, снимаются с публикации. Удаленный контакт можно заново привязать к своему аккаунту на странице подачи объявления.</div>
					</div>
					<div class="fl100 pb15 pt15">
						<?php if (!$user_contacts) : ?>
							<div class="smallcont ta-c fs18">
								<b>У Вас нет привязанных контактов</b>
							</div>
						<?php else : ?>
							<?php foreach ($user_contacts as $contact) : ?>												
								<div class="smallcont contact">
									<div class="labelcont">
										<label><span><?=$contact->name?></span></label>
									</div>
									<div class="fieldscont">										
										<div class="">
											<div class="inp-cont">
												<div class="cont-info">
													<a class="usercontact contact-input" data-id="<?=$contact->id?>"><?=$contact->get_contact_value()?></a>
													<span class="like_link ml10 fn_delete_verified_contact" data-id="<?=$contact->id?>">Удалить</span>
												</div>
											</div>
										</div>
									</div>									
								</div>																		
							<?php endforeach; ?>
						<?php endif;?>
					</div>
				</div>
			</section>
		</div>	   
		  
	</section>
</div><!--end content winner-->
