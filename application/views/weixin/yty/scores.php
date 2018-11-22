<style>
.score1 {color:green;}
.score0 {color:red;}
</style>

<div class="js-feed block-feed block top-0 bottom-0 border-0">
<link rel="stylesheet" type="text/css" href="/yty/css/loaders.min.css"/>
<link rel="stylesheet" type="text/css" href="/yty/css/loading.css"/>
<link rel="stylesheet" type="text/css" href="/yty/css/base.css"/>
<link rel="stylesheet" type="text/css" href="/yty/css/style.css"/>
<script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
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
    $title = $score->trade->title ? mb_substr($score->trade->title, 0, 22).'...' : $score->getTypeName($score->type);

    //订单明细链接
    $href = '';
    if ($score->tid) {
        $href = " href='/yty/order/{$score->tid}' ";
    }
?>

<div class="js-list">
    <a <?=$href?> class="block-item">
        <p class="line-height-30">
            <span><?=$title?></span>
            <span class="right-block pull-right<?=$score->score > 0 ? ' score1' : ' score0'?>"><?=$score2?></span>
        </p>
        <p class="font-size-14 c-gray-dark" style="line-height: 14px;"><?=date('Y-m-d H:i:s', $score->lastupdate)?></p>
    </a>
</div>

<?php endforeach?>

</div>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    <footer class="page-footer fixed-footer">
        <ul style="height: 0px;">
            <li>
                <a href="/yty/home">
                    <img src="/yty/images/footer002.png"/>
                    <p style="font-size: 14px;line-height:14px;">个人中心</p>
                </a>
            </li>

            <li >
                <a href="<?='http://'.$_SERVER["HTTP_HOST"].'/yty/storefuop/'.$bid?>">
                    <img src="/yty/images/footer004.png"/>
                    <p style="font-size: 14px;line-height:14px;">推荐商品</p>
                </a>
            </li>
        </ul>
    </footer>
