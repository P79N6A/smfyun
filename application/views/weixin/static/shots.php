
<style>
#weibo {
    width: 100%;
}

.box {
    background: #EFEFEF;
    margin: 10px 0;
    padding: 8px;
}

.box img.b {
    clear: both;
    padding-bottom: 5px;
    border-bottom: 1px dashed #CCC;
}
</style>

<div data-role="page">

	<div data-role="header">
		<h1>微博晒图选登</h1>
	</div>

<?php
$shots = ORM::factory('shot')
        ->where('mid', '<>', '')
        ->where('pic', '<>', '')
        ->where('show', '=', 1)
        ->order_by(DB::expr('RAND()'))
        ->limit(100)
        ->find_all();
?>

	<div data-role="content" id="content">

		<p><b>以下图片均来源于微博晒单，点击图片可以查看原网页</b></p>

		<div id="weibo">
		    <?php
		    foreach ($shots as $shot):
		        //$file = "/_img/shots/{$shot->mid}.jpg";
                $file = $shot->pic;
		    ?>
		    <div class="box">
		        <a href="http://weibo.com/<?=$shot->uid?>/<?=$shot->mid?>" target="_blank">
		        	<img class="b" width="100%" src="<?=$file?>" alt="" />
		        </a>
		        <p><img src="http://tp1.sinaimg.cn/<?=$shot->uid?>/30/40003367387/1" alt="" />
		        <?=$shot->text?></p>
		    </div>
		    <?php endforeach?>
		</div>

	</div>

</div>
