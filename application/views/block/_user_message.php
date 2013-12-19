<?php if ($user_text_enable === 1 and !empty($user_text) and !isset($_COOKIE['disable_message'])) : ?>
			<div class="user-message-block fn-user-message-block">
				<div class="user-message-text">
					<p><?=$user_text?></p>
					<span class="button white fn-disable-user-message">Закрыть и не показывать больше</span>
				</div>
			</div>
<?php endif; ?>