<style>
.score1 {color:green;}
.score0 {color:red;}
</style>

<div class="js-feed block-feed block top-0 bottom-0 border-0">

<?php
$id = count($scores)+1;

if ($id == 1):
?>

<div class="js-list">
    <a class="block-item">
        <p class="line-height-30">
            <div style="text-align:center">没有记录</div>
        </p>
    </a>
</div>

<?php
endif;

foreach ($scores as $score):
    $id--;
    $score2 = $score->score > 0 ? "+{$score->score}" : "{$score->score}";
?>
<div class="js-list">
    <a  class="block-item">
        <p class="line-height-30">
            <span><?=str_replace("积分", $config['scorename'], $score->getTypeName($score->type));?></span>
            <span class="right-block pull-right<?=$score->score > 0 ? ' score1' : ' score0'?>"><?=$score2?></span>
        </p>
        <p class="font-size-14 c-gray-dark" style="line-height: 14px;"><?=date('Y-m-d H:i:s', $score->lastupdate)?></p>
    </a>
</div>

<?php endforeach?>

</div>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
