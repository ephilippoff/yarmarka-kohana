<?php 
$cookie_condition = isset($_COOKIE['disable_message']) ? $_COOKIE['disable_message'] != md5($user_text) : true;

if ($user_text_enable === 1 and !empty($user_text) and $cookie_condition) : ?>
			<script>
 				var disable_message_key = "<?=md5($user_text)?>";
			</script>			
			<div class="user-message-block fn-user-message-block">
				<div class="user-message-text">
					<p><?=$user_text?></p>
					<span class="button white fn-disable-user-message">Закрыть и не показывать больше</span>
				</div>
			</div>
<?php endif; ?>