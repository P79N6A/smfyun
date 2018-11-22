<?=$father?>
        <!-- header section end-->
<div class="second-menu">
        <div class="tpl-left-nav tpl-left-nav-hover">
            <div class="tpl-left-nav-title">
                全员分销
            </div>
            <div class="tpl-left-nav-list">
                <ul class="tpl-left-nav-menu">
                    <li class="tpl-left-nav-item">
                        <a href="https://h5.youzan.com/v2/feature/t8AicRSkFX" target="_blank" class="nav-link">
                            <i class="am-icon-book"></i>
                            <span>使用教程</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwtqfxa/home" class="nav-link <?=isActive('home')?>">
                            <i class="am-icon-cog"></i>
                            <span>个性化设置</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwtqfxa/setgoods" class="nav-link <?=isActive('setgoods')?>">
                            <i class="am-icon-shopping-cart"></i>
                            <span>商品管理</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list <?=isOpened(array('qrcodes', 'qrcodes_m','skus','group'))?>">
                            <i class="am-icon-user" style="margin-right:0"></i>
                            <span>分销商设置</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right" style="float:right;"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="/qwtqfxa/qrcodes" class="<?=isActive('qrcodes')?>">
                                    <span>分销商审核</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwtqfxa/qrcodes_m" class="<?=isActive('qrcodes_m')?>">
                                    <span>分销商管理</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwtqfxa/skus" class="<?=isActive('skus')?>">
                                    <span>分销商等级管理</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwtqfxa/group" class="<?=isActive('group')?>">
                                    <span>分销商分组管理</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list <?=isOpened(array('stats_totle','history_trades','history_withdrawals','stats_goods'))?>">
                            <i class="am-icon-bar-chart"></i>
                            <span>数据统计</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="/qwtqfxa/stats_totle" class="<?=isActive('stats_totle')?>">
                                    <span>概况</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwtqfxa/history_trades" class="<?=isActive('history_trades')?>">
                                    <span>订单记录</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwtqfxa/history_withdrawals" class="<?=isActive('history_withdrawals')?>">
                                    <span>提现记录</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwtqfxa/stats_goods" class="<?=isActive('stats_goods')?>">
                                    <span>商品统计</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        </div>
<?php
$app = 'qfx';
$check = Model::factory('select_experience')->fenzai($bid,$app);
if ($check==false){
    $item = ORM::factory('qwt_item')->where('alias','=',$app)->find();
    $iid = $item->id;
    $timelimit = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',$iid)->find()->expiretime;
    $buyhref = 'http://'.$_SERVER['HTTP_HOST'].'/qwta/product/'.$iid;
    $testsku = ORM::factory('qwt_sku')->where('bid','=',0)->where('iid','=',$iid)->where('tryout','=',1)->find();
    $desc = $testsku->testab;
    if ($app=='wfb'||$app=='wdb'||$app=='rwb'||$app=='dka'||$app=='qfx'||$app=='fxb') {
        $numleft = Model::factory('select_experience')->selectnum($bid,$app);
        $desc = '您最多还可以生成'.$numleft.'张海报，';
    }
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
</section>
    <script src="/qwt/assets/js/amazeui.min.js"></script>
    <script src="/qwt/assets/js/app.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){
        $('#appbox').addClass('opened');
        $('.top-bar').addClass('lefter');
        $('#app_qfx').addClass('active');
        $('#appbox .am-icon-angle-right').addClass('tpl-left-nav-more-ico-rotate');
    })
    </script>


</body>
</html>
