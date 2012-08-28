<?php 
foreach ($unanswered as $conversation) {
?>
    <span class="call" onclick="window.location.href = 'http://www.omghelp.net/conversations/accept/<?=$conversation["id"];?>'">
        <div class="user">
            <?php echo substr($conversation["userName"], 0, 24); ?>
            <br>
            <span class="category"><b><?=$conversation["category"] ?></b></span>
        </div>
    <div class="age"><span class="time"><?=$conversation["age"] ?></span><br><br><span class="answer">Answer</span></div>
        <br style="clear:both">
    </span>
    <br>
<?php
}
?>
