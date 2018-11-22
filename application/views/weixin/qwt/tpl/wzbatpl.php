<?=$father?>
        <!-- header section end-->
<div class="second-menu">
        <div class="tpl-left-nav tpl-left-nav-hover">
            <div class="tpl-left-nav-title">
                神马云直播
            </div>
            <div class="tpl-left-nav-list">
                <ul class="tpl-left-nav-menu">
                    <li class="tpl-left-nav-item">
                        <a href="https://h5.youzan.com/v2/feature/gNUiFa8rPj" target="_blank" class="nav-link">
                            <i class="am-icon-book"></i>
                            <span>使用教程</span>
                        </a>
                    </li>

                    <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list <?=isOpened(array('information', 'flowcenter','buymentrecord'))?>">
                            <i class="am-icon-gift"></i>
                            <span>会员中心</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="/qwtwzba/information" class="<?=isActive('information')?>">
                                    <i class="am-icon-angle-right"></i>
                                    <span>基本信息</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwtwzba/flowcenter" class="<?=isActive('flowcenter')?>">
                                    <i class="am-icon-angle-right"></i>
                                    <span>直播流量中心</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwtwzba/buymentrecord" class="<?=isActive('buymentrecord')?>">
                                    <i class="am-icon-angle-right"></i>
                                    <span>购买记录</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwtwzba/home" class="nav-link <?=isActive('home')?>">
                            <i class="am-icon-cog"></i>
                            <span>基础设置</span>
                        </a>
                    </li>
                     <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list <?=isOpened(array('setgoods', 'other_setgood'))?>">
                            <i class="am-icon-gift"></i>
                            <span>直播商品管理</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="/qwtwzba/setgoods1" class="<?=isActive('setgoods1')?>">
                                    <i class="am-icon-angle-right"></i>
                                    <span>有赞商品管理</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwtwzba/other_setgood" class="<?=isActive('other_setgood')?>">
                                    <i class="am-icon-angle-right"></i>
                                    <span>其他商品管理</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list <?=isOpened(array('marketing', 'lottery'))?>">
                            <i class="am-icon-gift"></i>
                            <span>营销模块</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="/qwtwzba/marketing" class="<?=isActive('marketing')?>">
                                    <i class="am-icon-angle-right"></i>
                                    <span>首次进入直播间送优惠券</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwtwzba/lottery" class="<?=isActive('lottery')?>">
                                    <i class="am-icon-angle-right"></i>
                                    <span>幸运抽奖轮盘</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="tpl-left-nav-item">
                        <a href="/qwtwzba/qrcodes" class="nav-link <?=isActive('qrcodes')?>">
                            <i class="am-icon-users"></i>
                            <span>用户管理</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwtwzba/analyze" class="nav-link <?=isActive('analyze')?>">
                            <i class="am-icon-bar-chart"></i>
                            <span>直播分析</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list <?=isOpened(array('download', 'download_ios'))?>">
                            <i class="am-icon-square"></i>
                            <span>商户端APP下载</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="/qwtwzba/download" class="<?=isActive('download')?>">
                                    <i class="am-icon-angle-right"></i>
                                    <span>安卓端APK下载</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwtwzba/download_ios" class="<?=isActive('download_ios')?>">
                                    <i class="am-icon-angle-right"></i>
                                    <span>IOS端应用下载</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        </div>
<?php
$app = 'wzb';
$check = Model::factory('select_experience')->fenzai($bid,$app);
if ($check==false){
    $item = ORM::factory('qwt_item')->where('alias','=',$app)->find();
    $iid = $item->id;
    $timelimit = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',$iid)->find()->expiretime;
    $buyhref = 'http://'.$_SERVER['HTTP_HOST'].'/qwta/product/'.$iid;
    $testsku = ORM::factory('qwt_sku')->where('bid','=',0)->where('iid','=',$iid)->where('tryout','=',1)->find();
    $desc = $testsku->testab;
    //云直播专属
        $sql = DB::query(Database::SELECT,"SELECT sum(data) as CT FROM qwt_wzblives where bid=$bid ");
        $num = $sql->execute()->as_array();
        $use =  $num[0]['CT'];
        $all = ORM::factory('qwt_login')->where('id','=',$bid)->find()->stream_data;
        $numleft = number_format($all-number_format($use/(1024*1024*1024),2),2);
        $desc = '还剩余'.$numleft.'G流量，';
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
        $('.top-bar').addClass('lefter');
        $('#app_wzb').addClass('active');
        $('#appbox .am-icon-angle-right').addClass('tpl-left-nav-more-ico-rotate');
    })
    </script>

</body>
</html>
