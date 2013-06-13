<?php if ($count > 0) : ?>
<div class="bottom-bl hide-cont moderator_comments">
	<div class="msg-bl">
		<p class="comments-count">Сообщения от модератора: <?=$count?></p>

		<span class="comments_block">
		<article class="msg">
			<a href=""><span class="date"><?=date("d.m.Y H:i", strtotime($comment->createdOn))?></span></a>
			<p><?=$comment->text;?></p>
		</article>

		<?php if ($count > 1) : ?>
			<a href="#" onClick="load_moderator_comments(<?=$comment->object_id?>, this);return false;">Показать все</a>
		<?php endif; ?>
		</span>

	</div>
</div>
<?php endif; ?>
