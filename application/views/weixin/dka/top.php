<?php
$ranktitle = "第 {$result['rank']} 名";
?>

<div id="rankpage">
<section id="ranking">
<span id="ranking_title"><?=$ranktitle?></span>

<section id="ranking_list">

<?php
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

</section>
</section>
</div>
