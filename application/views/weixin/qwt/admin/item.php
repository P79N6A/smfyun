<style type="text/css">
    .left{
        float: left;
        padding-left: 10px;
    }
    .right{
        float: right;
        padding-right: 10px;
    }
    .img{
        display: inline-block;
        width: 100%;
        height: 200px;
    }
    .top{
        display: inline-block;
        width: 100%;
        height: 33px;
        color: #77D2B1;
        font-weight: bold;
        padding-left: 10px;
    }
    .tpl-table-images-content-i .fadeimg{
        position: absolute;
        top: 0;
        opacity: 0;
    }
    .tpl-table-images-content-i:hover .fadeimg{
        opacity: 1;
    }
    .tpl-table-images-content-i .fadeimg, .tpl-table-images-content-i:hover .fadeimg{
  -webkit-transition: all 0.5s ease;
  -moz-transition: all 0.5s ease;
  -ms-transition: all 0.5s ease;
  -o-transition: all 0.5s ease;
  transition: all 0.5s ease;
    }
    .am-nav-tabs>li{
        border-bottom: 4px solid #fff;
    }
    .important{
        border-bottom: 4px solid #fff;
    }
    .am-nav-tabs>li.am-active>a{
        border-bottom: 4px solid #fff !important;
    }
</style>
<section>

  <div class="tpl-page-container tpl-page-header-fixed" style="margin-left:0;">
    <div class="tpl-content-wrapper">
      <div class="tpl-content-page-title">
                购买应用
            </div>
      <ol class="am-breadcrumb">
        <li><a href="#" class="am-icon-home am-active">购买应用</a></li>
      </ol>
      <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
        <div class="tpl-portlet">
        <section class="panel">
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <ul class="am-nav am-nav-tabs" style="top:0;left:0;border-bottom: 1px solid #eef1f5;position: inherit;">
              <li class="am-active"><a class="important" data-type="app">全部应用</a></li>
              <li><a class="important" data-type="xf">公众号吸粉应用</a></li>
              <li><a class="important" data-type="fx">公众号分销应用</a></li>
              <li><a class="important" data-type="hd">公众号互动应用</a></li>
              <li><a class="important" data-type="cx">公众号促销应用</a></li>
              <li><a class="important" data-type="zdfk">自动发卡工具</a></li>
              <li><a class="important" data-type="xcxyl">小程序引流工具</a></li>
              <li><a class="important" data-type="mdyl">门店引流</a></li>
              <li><a class="important" data-type="live">神码云直播</a></li>
              <li><a class="important" data-type="dpm">数据大屏幕</a></li>
              <li><a class="important" data-type="H5">H5小游戏</a></li>
              <li><a class="important" data-type="xcx">小程序应用</a></li>
              <li><a class="important" data-type="dzkf">定制开发服务</a></li>
                            </ul>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active">
                                    <div id="wrapperA" class="wrapper">
                            <form class="am-form" name="ordersform" method="get">
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="tpl-table-images">
                        <?php foreach ($result['system'] as $product):?>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app <?=$product->classify?>">
                                <?php
                                $exist=ORM::factory('qwt_sku')->where('bid','=',$fubid)->where('iid','=',$iid)->where('show1','=',1)->id;
                                if($exist){
                                    $price=ORM::factory('qwt_sku')->where('bid','=',$fubid)->where('iid','=',$product->id)->where('show1','=',1)->find()->price;
                                }else{
                                    $price=ORM::factory('qwt_sku')->where('bid','=',0)->where('iid','=',$product->id)->where('show1','=',1)->find()->price;
                                }
                                ?>
                                <?php
                                $buy = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',$product->id)->where('status','=',1)->find();
                                ?>
                                <div class="tpl-table-images-content">
                                    <?php if($buy->id):?>
                                    <div class="tpl-table-images-content-i-time"><?=$buy->expiretime<=time()?'<span style="color:red">已过期</span>':'已订购'?>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp有效期至：<?=date('Y-m-d',$buy->expiretime)?>
                                    </div>
                                <?php else:?>
                                    <div class="tpl-table-images-content-i-time">未订购</div>
                                    <?php endif?>
                                    <div class="tpl-i-title">
                                        <?=$product->name?>
                                    </div>
                                    <a href="/qwta/product/<?=$product->id?>" class="tpl-table-images-content-i">
                                        <img src="<?=$product->picurl?>" alt="">
                                    </a>
                                    <div class="tpl-table-images-content-block">
                                        <div class="tpl-i-font" style="font-weight:bold;color:#ff4400;">￥<?=$price?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach?>
                        <!--小游戏开始-->
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app H5">
                                <div class="tpl-table-images-content">
                                    <div class="tpl-table-images-content-i-time">晨光卖萌货"国民萌值大比拼"</div>
                                    <a class="tpl-table-images-content-i">
                                        <img src="../qwt/images/H5/mmh.jpg" alt="">
                                        <img class="fadeimg" src="../qwt/images/erweima/mmh.jpeg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app H5">
                                <div class="tpl-table-images-content">
                                    <div class="tpl-table-images-content-i-time">良品铺子"吃货众生相"</div>
                                    <a class="tpl-table-images-content-i">
                                        <img src="../qwt/images/H5/chihuo.jpg" alt="">
                                        <img class="fadeimg" src="../qwt/images/erweima/chihuo.jpeg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app H5">
                                <div class="tpl-table-images-content">
                                    <div class="tpl-table-images-content-i-time">大转盘</div>
                                    <a class="tpl-table-images-content-i">
                                        <img src="../qwt/images/H5/dzp.jpg" alt="">
                                        <img class="fadeimg" src="../qwt/images/erweima/dzp.jpeg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app H5">
                                <div class="tpl-table-images-content">
                                    <div class="tpl-table-images-content-i-time">晨光文具“摇一摇开运灵签”</div>
                                    <a class="tpl-table-images-content-i">
                                        <img src="../qwt/images/H5/yaoyy.jpg" alt="">
                                        <img class="fadeimg" src="../qwt/images/erweima/yaoyy.jpeg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app H5">
                                <div class="tpl-table-images-content">
                                    <div class="tpl-table-images-content-i-time">良品铺子"有料好声音"</div>
                                    <a class="tpl-table-images-content-i">
                                        <img src="../qwt/images/H5/hsy.jpg" alt="">
                                        <img class="fadeimg" src="../qwt/images/erweima/hsy.jpeg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app H5">
                                <div class="tpl-table-images-content">
                                    <div class="tpl-table-images-content-i-time">纯亭"测一测你离王子有多远"</div>
                                    <a class="tpl-table-images-content-i">
                                        <img src="../qwt/images/H5/cyc.jpg" alt="">
                                        <img class="fadeimg" src="../qwt/images/erweima/cyc.jpeg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app H5">
                                <div class="tpl-table-images-content">
                                    <div class="tpl-table-images-content-i-time">有赞微商城"吃货大作战"</div>
                                    <a class="tpl-table-images-content-i">
                                        <img src="../qwt/images/H5/youzan.jpg" alt="">
                                        <img class="fadeimg" src="../qwt/images/erweima/youzan.jpeg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app H5">
                                <div class="tpl-table-images-content">
                                    <div class="tpl-table-images-content-i-time">男人袜"找妹子"</div>
                                    <a class="tpl-table-images-content-i">
                                        <img src="../qwt/images/H5/nrw.jpg" alt="">
                                        <img class="fadeimg" src="../qwt/images/erweima/nrw.jpeg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app H5">
                                <div class="tpl-table-images-content">
                                    <div class="tpl-table-images-content-i-time">宁波万象城"老虎机小游戏"</div>
                                    <a class="tpl-table-images-content-i">
                                        <img src="../qwt/images/H5/lhj.jpg" alt="">
                                        <img class="fadeimg" src="../qwt/images/erweima/lhj.jpeg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app H5">
                                <div class="tpl-table-images-content">
                                    <div class="tpl-table-images-content-i-time">弘信宝"端午节语音贺卡"</div>
                                    <a class="tpl-table-images-content-i">
                                        <img src="../qwt/images/H5/hxb.jpg" alt="">
                                        <img class="fadeimg" src="../qwt/images/erweima/xhb.jpeg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app H5">
                                <div class="tpl-table-images-content">
                                    <div class="tpl-table-images-content-i-time">中华小曲库调用中...</div>
                                    <a class="tpl-table-images-content-i">
                                        <img src="../qwt/images/H5/roseonly.jpg" alt="">
                                        <img class="fadeimg" src="../qwt/images/erweima/roseonly.jpeg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app H5">
                                <div class="tpl-table-images-content">
                                    <div class="tpl-table-images-content-i-time">三里屯要出大事了...</div>
                                    <a class="tpl-table-images-content-i">
                                        <img src="../qwt/images/H5/zzy.jpg" alt="">
                                        <img class="fadeimg" src="../qwt/images/erweima/zzy.jpeg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app H5">
                                <div class="tpl-table-images-content">
                                    <div class="tpl-table-images-content-i-time">来，我给大家唱了一首求佛...</div>
                                    <a class="tpl-table-images-content-i">
                                        <img src="../qwt/images/H5/qf.jpg" alt="">
                                        <img class="fadeimg" src="../qwt/images/erweima/qf.jpeg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app H5">
                                <div class="tpl-table-images-content">
                                    <div class="tpl-table-images-content-i-time">最炫的民族风...</div>
                                    <a class="tpl-table-images-content-i">
                                        <img src="../qwt/images/H5/zx.jpg" alt="">
                                        <img class="fadeimg" src="../qwt/images/erweima/zx.jpeg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app H5">
                                <div class="tpl-table-images-content">
                                    <div class="tpl-table-images-content-i-time">吴酒</div>
                                    <a class="tpl-table-images-content-i">
                                        <img src="../qwt/images/H5/wj.jpg" alt="">
                                        <img class="fadeimg" src="../qwt/images/erweima/wj.jpeg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app H5">
                                <div class="tpl-table-images-content">
                                    <div class="tpl-table-images-content-i-time">戏球名茶</div>
                                    <a class="tpl-table-images-content-i">
                                        <img src="../qwt/images/H5/xiq.jpg" alt="">
                                        <img class="fadeimg" src="../qwt/images/erweima/xiq.png" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app H5">
                                <div class="tpl-table-images-content">
                                    <div class="tpl-table-images-content-i-time">范冰冰的未接来电</div>
                                    <a class="tpl-table-images-content-i">
                                        <img src="../qwt/images/H5/phone.jpg" alt="">
                                        <img class="fadeimg" src="../qwt/images/erweima/phone.jpeg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4 app xcx">
                                <div class="tpl-table-images-content">
                                    <div class="tpl-table-images-content-i-time">尺简（微信小程序定制）</div>
                                    <a class="tpl-table-images-content-i">
                                        <img src="../qwt/images/H5/chijian.jpeg" alt="">
                                        <img class="fadeimg" src="../qwt/images/erweima/chijian.jpg" alt="">
                                    </a>
                                </div>
                            </div>
                        <!--小游戏结束-->
                            <div class="am-u-lg-12">
                                <div class="am-cf">

                                    <div class="am-fr">
                                        <ul class="am-pagination tpl-pagination">
                                        <?=$pages?>
                                        </ul>
                                    </div>
                                </div>
                                <hr>
                            </div>

                        </div>

                    </div>
                </div>
                    </section>
                    <!-- <section class="panel">
                        <header class="panel-heading">
                           插件类
                        </header>
                        <div class="panel-body">
                            <div id="gallery" class="media-gal">
                        <?php foreach ($result['plug'] as $product):?>
                                <div class=" images documents item " >
                                <?php
                                $exist=ORM::factory('qwt_sku')->where('bid','=',$fubid)->where('iid','=',$iid)->where('show1','=',1)->id;
                                if($exist){
                                    $price=ORM::factory('qwt_sku')->where('bid','=',$fubid)->where('iid','=',$product->id)->where('show1','=',1)->find()->price;
                                }else{
                                    $price=ORM::factory('qwt_sku')->where('bid','=',0)->where('iid','=',$product->id)->where('show1','=',1)->find()->price;
                                }
                                ?>
                                    <a href="/qwta/product/<?=$product->id?>">
                                        <img src="<?=$product->picurl?>" alt="" />
                                    <p class="left"><?=$product->name?></p><p class="right"><?=$price?></p>
                                    </a>
                                </div>
                        <?php endforeach?>
                            </div>
                        </div>
                    </section> -->
                </div>
            </div>
        </div>
        </div>
        </div>
        </section>
    <script src="/qwt/assets/js/amazeui.min.js"></script>
    <script src="/qwt/assets/js/app.js"></script>
    <script type="text/javascript">
  $(document).ready(function(){
    var a = $('.tpl-table-images-content-i img').width();
    $('.tpl-table-images-content-i img').css('height',a+'px');
})
  $('.tpl-index-tabs .am-nav-tabs li a').click(function(){
    $('.tpl-index-tabs .am-nav-tabs li').removeClass('am-active');
    $(this).parent().addClass('am-active');
    $('.app').hide();
    var type = $(this).data('type');
    $('.'+type).show();
})
    </script>
