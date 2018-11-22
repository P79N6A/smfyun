<?php
$ranktitle = "第 {$result['rank']} 名";
if(!$result['rank']){
    $ranktitle = "今日未获得积分哟";
}
?>
<?if(Session::instance()->get('scb')["bid"]==582):?>
<style>
	.btn1, .btn2{
	    width: 30%;
	    height: 40px;
	    border: 1px solid #888;
	    border-radius: 3px;
	    background-color: #efeff4;
	    margin-top: 30px;
	    font-size: 14px;
	    color: #888;
	    margin-bottom: 20px;
	}
	.btn1{
		float:left;
		margin-left: 11%;
	}
	.btn2{
		float:right;
		margin-right: 11%;
	}
</style>
<form action="" method="post">
    <input class="btn1" type="submit" value="全部排行">
    <input class="btn2" type="submit" name='rank[today]' value="今日排行">
</form>
<br clear="all">
<?endif?>
<div id="rankpage">
<section id="ranking">
<span id="ranking_title"><?=$ranktitle?></span>

<section id="ranking_list">

<?php
if(isset($users)):
    foreach ($users as $user):
$rank++;
?>
        <section class="box">
          <section class="col_1"<?=$rank<=3 ? ' title="1"' : ''?>><?=$rank?></section>
          <section class="col_2"><img src="<?=$user['headimgurl']?>" /></section>
          <section class="col_3"><?=$user['nickname']?></section>
          <section class="col_4"><?=$user['score']?></section>
        </section>
<?php endforeach?>
<?php endif?>
</section>
</section>
</div>
