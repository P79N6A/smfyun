<style>
.score1 {color:green;}
.score0 {color:red;}
#cd-table{
    margin-top: 0;
}
.cd-table-wrapper{

    background-color: #fff;
    padding: 10px 20px;
    border-top: 1px solid #dedede;
    border-bottom: 1px solid #dedede;
}
.event{
    padding: 5px 0;
    border-bottom: 1px solid #e5e5e5;
}
.typeandtime{
    display: inline-block;
}
.type{
    font-size: 18px;
    color: #666666;
}
.time{
    font-size: 12px;
    color: #bbbbbb;
}
.score{

    display: inline-block;
    float: right;
    font-size: 18px;
    font-weight: bold;
}
.score0{
    color: #33bb33;
}
.score1{
    color: #ff9900;
}
.nomore{
    text-align: center;
    font-size: 14px;
    color: #888888;
}
</style>

    <!-- TableList -->
    <section id="cd-table">
        <div class="cd-table-container">
          <div class="cd-table-wrapper">
<?php
$id = count($scores)+1;
foreach ($scores as $score):
$id--;
?>
            <div class="event">
                <div class="typeandtime">
                    <div class="type"><?=$score->getTypeName($score->type)?></div>
                    <div class="time"><?=date('Y-m-d', $score->lastupdate)?></div>
                </div>
                <div class="<?=$score->score > 0 ? 'score score1' : 'score score0'?>">
                    <?=$score->score?>
                </div>
            </div>

<?php endforeach?>
<div class="nomore">没有更多了</div>
            <!--<ul>
                <li class="cd-table-column">
                <div class="scoreid">
                    <b>ID</b>
                </div>
                <div class="type">
                    <b><?=$scorename ?>来源</b>
                </div>
                <div class="score">
                    <b>数量</b>
                </div>
                <div class="update">
                <b>增加时间</b>
                </div>
                </li>

<?php
$id = count($scores)+1;
foreach ($scores as $score):
$id--;
?>
                <li class="cd-table-column">
                <div class="scoreid">
                    <?=$id?>
                </div>
                <div class="type">
                    <?=str_replace("积分", $scorename, $score->getTypeName($score->type));?>
                </div>
                <div class="<?=$score->score > 0 ? 'score score1' : 'score score0'?>">
                    <?=$score->score?>
                </div>
                <div class="update">
                    <?=date('Y-m-d', $score->lastupdate)?>
                </div>
                </li>
<?php endforeach?>

            </ul> -->
            </div> <!-- cd-table-wrapper -->
        </div> <!-- cd-table-container -->
    </section> <!-- cd-table -->

    <script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
    <script type="text/javascript">
    var h= $('.typeandtime').height();
    $('.score').css('height',h);
    $('.score').css('line-height',h+'px');
    $('.nomore').css('height',h);
    $('.nomore').css('line-height',h+'px');
        </script>
