
<!doctype html>
<html><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1.0 user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <title>兑换商城</title>
    <link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/wdy/front/css/ui.css">
    <link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/wdy/front/css/style.css"><!--
    <link rel="stylesheet" type="text/css" href="../fxb/prize/css/didi.css"> -->
    <!-- <link rel="stylesheet" type="text/css" href="../fxb/prize/css/index.css"> -->
    <link rel="stylesheet" type="text/css" href="../fxb/prize/css/swiper.css">
  <style>
.text_menu
{
  font-size: 15px;
}
.mod>.header {
    height: 2.4rem;
    padding: 0;
    position: relative;
    text-align: center;
    color: #666;
    border-bottom: 1px solid #e5e5e5;
    border-top: 1px solid #e5e5e5;
    margin-bottom: 10px;
    background-color: #fff;
}
.mod>.header:before {
    content: '';
    border-bottom: 1px solid #e5e5e5;
    position: absolute;
    left: 0;
    top: 50%;
    width: 100%;
    height: 0;
    z-index: 0;
    -webkit-transform: translate3d(0,-2px,0);
    transform: translate3d(0,-2px,0);
}
.mod>.header .txt {
    position: relative;
    display: inline-block;
    padding: 0 .5rem;
    line-height: 2.4rem;
    background-color: #f0f0f0;
}
.goods{
    float: left;
    border-right: 1px solid #e5e5e5;
    border-bottom: 1px solid #e5e5e5;
    text-align: center;
    background-color: #fff;
}
.media{
    padding: 10px;
    display: inline-flex;
    justify-content:center;
    align-items:center;
}
.media img{
    max-height: 100%;
    max-width: 100%;
}
.info span{
    display: block;
    text-align: left;
}
.name{
    color: #666666;
    font-size: 18px;
    padding: 0 0 10px 20px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.price{
    color: #ff4806;
    font-size: 16px;
    padding: 0 0 10px 20px;
}
#copyright{
    display: inline-block;
    width: 100%;
}
.bd{
    border-top: 1px solid #e5e5e5;
}
/*.menu_list{
    border-top: 1px solid #dedede;
    border-bottom: 1px solid #dedede;
}*/
</style>

</head>
<body class="page-index" data-page="index" ontouchstart="">
    <div class="view js-page-view">
        <section id="mods">
         <div class="mod">
            <div class="bd clearfix">
<?php if(!is_array($items)):?>
    <center><div>请先添加奖品</div><center>
<?php else:?>
<?php
foreach ($items as $item):
    $item = (object)$item;
    //判断限购
    $limit = ORM::factory('qwt_wfborder')->where('qid', '=', $user2['id'])->where('iid', '=', $item->id)->count_all();
?>
                <div class="goods js-click">
                <a href="/qwtwfb/item/<?=$item->id?>">
                    <div class="media">
                        <img src="http://cdn.jfb.smfyun.com/qwtwfb/images/item/<?=$item->id?>.v<?=$item->lastupdate?>0.jpg" alt="<?=$item->name?>">
                    </div>
                    <div class="info">
                        <span class="name">
                            <?=$item->name?>
                        </span>
                        <span class="price">
                            <b><?=$item->score?></b><?=$scorename?>
                        </span>
                    </div>
                    </a>
                </div>
            <?php endforeach?>
        <?php endif?>
            </div>
        </div>
    </section>
    </div>
</body>

    <script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
    <script type="text/javascript">
    var w= $('.bd').width();
    var h= $('.info').height();
    $('.goods').css('width',w*0.5-1);
    $('.goods').css('height',w*0.4+h);
    $('.media').css('width',w*0.4-20);
    $('.media').css('height',w*0.4-20);
    var a= $('.desc').height();
    var b= $('.number').height();
    $('.circle').css('width',a+b+40);
    $('.circle').css('height',a+b+20);
    $('.circle').css('border-radius',(a+b+40)/2);
        </script>
</html>
