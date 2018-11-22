<style>
.left2 {
    overflow: hidden;
    width: 100%;
}
</style>

<ul class="cd-gallery">

<?php foreach ($tasks as $task):?>

        <li>
        <a href="/rwb/task/<?=$task->id?>">
            <div class="cd-single-item">
                <ul class="cd-slider-wrapper">

                </ul>
            </div> <!-- .cd-single-item -->

                <div class="order-info">
                    <ul>
                    <li class="left2"><b>活动名称：</b><?=$task->name?></li>
                    <li class="left" style="width:100%"><b>起始时间：</b><?=date('Y/m/d H:i',$task->begintime)?></li>
                    <li class="left" style="width:100%"><b>截止时间：</b><?=date('Y/m/d H:i',$task->endtime)?></li>
                    </ul>
                </div>
                <div class="cd-customization">

                    <?php if($task->begintime>time()):?>
                        <button type="submit" style="background: #AFAAAA;border: 1px solid;" class="go-use">
                            未开始
                        </button>
                    <?php endif?>
                    <?php if($task->endtime>time()&&time()>$task->begintime):?>
                        <button type="submit" class="go-use">
                            正在进行中
                        </button>
                    <?php endif?>
                    <?php if(time()>$task->endtime):?>
                        <button type="submit" style="background: #AFAAAA;border: 1px solid;" class="go-use">
                            已结束
                        </button>
                    <?php endif?>
                </div> <!-- .cd-customization -->
        </a>
        </li>
<?php endforeach?>

</ul>
