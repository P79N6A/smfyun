<?=$father?>
        <!-- header section end-->
<div class="second-menu">
        <div class="tpl-left-nav tpl-left-nav-hover">
            <div class="tpl-left-nav-title">
                砍价宝
            </div>
            <div class="tpl-left-nav-list">
                <ul class="tpl-left-nav-menu">
                    <li class="tpl-left-nav-item">
                        <a href="https://h5.youzan.com/v2/feature/dkCp9cU0GL" target="_blank" class="nav-link">
                            <i class="am-icon-book"></i>
                            <span>使用教程</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwtkjba/home" class="nav-link <?=isActive('home')?>">
                            <i class="am-icon-cog"></i>
                            <span>店铺信息设置</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list <?=isOpened(array('items', 'kanlink','order_cut','cutlist'))?>">
                            <i class="am-icon-cog"></i>
                            <span>砍价管理</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="/qwtkjba/items" class="<?=isActive('items')?>">
                                    <span>砍价商品管理</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwtkjba/kanlink" class="<?=isActive('kanlink')?>">
                                    <span>砍价活动链接</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwtkjba/order_cut" class="<?=isActive('order_cut')?>">
                                    <span>发起的砍价</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwtkjba/orders" class="nav-link <?=isActive('orders')?>">
                            <i class="am-icon-list-ol"></i>
                            <span>订单管理</span>
                        </a>
                    </li>
                    <!-- <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list <?=isOpened(array('order_cut', 'order_pay'))?>">
                            <i class="am-icon-bar-chart"></i>
                            <span>订单管理</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="/qwtkjba/order_cut" class="<?=isActive('order_cut')?>">
                                    <span>发起的砍价</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwtkjba/order_pay" class="<?=isActive('order_pay')?>">
                                    <span>已付款的订单</span>
                                </a>
                            </li>
                        </ul>
                    </li> -->
                    <li class="tpl-left-nav-item">
                        <a href="/qwtkjba/qrcodes" class="nav-link <?=isActive('qrcodes')?>">
                            <i class="am-icon-user"></i>
                            <span>用户管理</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwtkjba/stats_totle" class="nav-link <?=isActive('stats_totle')?>">
                            <i class="am-icon-bar-chart"></i>
                            <span>数据统计</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        </div>
<?php
$app = 'kjb';
$check = Model::factory('select_experience')->fenzai($bid,$app);
if ($check==false){
    $item = ORM::factory('qwt_item')->where('alias','=',$app)->find();
    $iid = $item->id;
    $timelimit = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',$iid)->find()->expiretime;
    $buyhref = 'http://'.$_SERVER['HTTP_HOST'].'/qwta/product/'.$iid;
    $testsku = ORM::factory('qwt_sku')->where('bid','=',0)->where('iid','=',$iid)->where('tryout','=',1)->find();
    $desc = $testsku->testab;
    //kjb专属
    $eventnum = ORM::factory('qwt_kjbevent')->where('bid','=',$bid)->count_all();
    $cutnum = ORM::factory('qwt_kjbcut')->where('bid','=',$bid)->count_all();
    $leftnum = 50 - $eventnum - $cutnum;
    $desc = '您还可以发起'.$leftnum.'次砍价，';
}
if ($check==false):?>
?>
<div class="testver">您正在使用试用版<i class="information am-icon-question-circle-o"></i>，将于<?=date('Y-m-d H:i:s',$timelimit)?>到期，<?=$desc?>建议您购买正式版<a href="<?=$buyhref?>">立即购买</a></div>
<script type="text/javascript">
    $('.information').click(function(){
        swal({
            title: "<?=$item->name?>试用版",
            text: "<table class='swaltable'><thead><tr><th>应用规格</th><th>价格</th><th>使用期限</th><th>功能区别</th></tr></thead><tbody><tr><td><?=$item->name?>试用版</td><td>免费</td><td>3天</td><td><?=$testsku->testab?>功能与正式版一致</td></tr><tr><td><?=$item->name?>正式版</td><td><?=$testsku->fullsku?></td><td>按应用规格</td><td><?=$testsku->fullab?></td></tr></tbody></table>",
            // imageUrl: window.imgsrc,
            // imageSize: "200x200",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "我知道了",
            closeOnConfirm: true,
            closeOnCancel: true,
            html: true,
        })
    })
</script>
<?php endif?>
        <?=$content?>


    </div>
    <!-- main content end-->
</section>
    <script src="/qwt/assets/js/amazeui.min.js"></script>
    <script src="/qwt/assets/js/app.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){
        $('#appbox').addClass('opened');
        $('#app_kjb').addClass('active');
        $('.top-bar').addClass('lefter');
        $('#appbox .am-icon-angle-right').addClass('tpl-left-nav-more-ico-rotate');
    })
    </script>

</body>
</html>
