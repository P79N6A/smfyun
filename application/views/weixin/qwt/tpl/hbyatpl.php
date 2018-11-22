<?=$father?>
        <!-- header section end-->
<div class="second-menu">
        <div class="tpl-left-nav tpl-left-nav-hover">
            <div class="tpl-left-nav-title">
                红包雨
            </div>
            <div class="tpl-left-nav-list">
                <ul class="tpl-left-nav-menu">
                    <li class="tpl-left-nav-item">
                        <a href="https://h5.youzan.com/v2/feature/PKd73bwQke" target="_blank" class="nav-link">
                            <i class="am-icon-book"></i>
                            <span>使用教程</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwthbya/home" class="nav-link <?=isActive('home')?>">
                            <i class="am-icon-cog"></i>
                            <span>基础设置</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwthbya/rules" class="nav-link <?=isActive('rules')?>">
                            <i class="am-icon-cog"></i>
                            <span>营销规则</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwthbya/hbsend" class="nav-link <?=isActive('hbsend')?>">
                            <i class="am-icon-cog"></i>
                            <span>门店管理及投放</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwthbya/account" class="nav-link <?=isActive('account')?>">
                            <i class="am-icon-cog"></i>
                            <span>红包充值</span>
                        </a>
                    </li>
                    <!-- <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list <?=isOpened(array('account', 'payment','hbsend'))?>">
                            <i class="am-icon-bar-chart"></i>
                            <span>红包充值及投放</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="/qwthbya/account" class="<?=isActive(array('account', 'payment'))?>">
                                    <span>红包充值</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwthbya/hbsend" class="<?=isActive('hbsend')?>">
                                    <span>投放管理</span>
                                </a>
                            </li>
                        </ul>
                    </li> -->
                    <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list <?=isOpened(array('getdata', 'qrcodes'))?>">
                            <i class="am-icon-bar-chart"></i>
                            <span>数据统计</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="/qwthbya/getdata" class="<?=isActive('getdata')?>">
                                    <span>概况</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwthbya/qrcodes" class="<?=isActive('qrcodes')?>">
                                    <span>用户扫码明细</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwthbya/hbmct" class="<?=isActive('hbmct')?>">
                                    <span>红包码生成记录</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        </div>
<?php
$app = 'hby';
$check = Model::factory('select_experience')->fenzai($bid,$app);
if ($check==false){
    $item = ORM::factory('qwt_item')->where('alias','=',$app)->find();
    $iid = $item->id;
    $timelimit = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',$iid)->find()->expiretime;
    $buyhref = 'http://'.$_SERVER['HTTP_HOST'].'/qwta/product/'.$iid;
    $testsku = ORM::factory('qwt_sku')->where('bid','=',0)->where('iid','=',$iid)->where('tryout','=',1)->find();
    $desc = $testsku->testab;
    //红包雨专属
    $numused = ORM::factory('qwt_hbyorder')->where('bid','=',$bid)->count_all();
    $numsum = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',14)->find()->hbnum;
    $numleft = $numsum - $numused;
    $desc = '您最多还可以生成'.$numleft.'个红包码，';
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
        $('#app_hby').addClass('active');
        $('.top-bar').addClass('lefter');
        $('#appbox .am-icon-angle-right').addClass('tpl-left-nav-more-ico-rotate');
    })
    </script>

</body>
</html>
