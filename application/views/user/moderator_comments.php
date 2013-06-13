<?php foreach ($comments as $comment) : ?>
    <article class="msg">
        <a href=""><span class="date"><?=date("d.m.Y H:i", strtotime($comment->createdOn))?></span></a>
        <p><?=$comment->text;?></p>
    </article>
<?php endforeach; ?>

