<style>
.score1 {color:green;}
.score0 {color:red;}
</style>

    <!-- TableList -->
    <section id="cd-table">
        <div class="cd-table-container">
          <div class="cd-table-wrapper">
            <ul>
                <li class="cd-table-column">
                <div class="scoreid">
                    <b>ID</b>
                </div>
                <div class="type">
                    <b><?=$config['score'] ?>来源</b>
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
                    <?=str_replace("积分", $config['score'], $score->getTypeName($score->type));?>
                </div>
                <div class="<?=$score->score > 0 ? 'score score1' : 'score score0'?>">
                    <?=$score->score?>
                </div>
                <div class="update">
                    <?=date('Y-m-d', $score->lastupdate)?>
                </div>
                </li>
<?php endforeach?>

            </ul>
            </div> <!-- cd-table-wrapper -->
        </div> <!-- cd-table-container -->
    </section> <!-- cd-table -->
