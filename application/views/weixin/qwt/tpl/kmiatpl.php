</html>
<?=$father?>
        <!-- header section end-->
<div class="second-menu">
        <div class="tpl-left-nav tpl-left-nav-hover">
            <div class="tpl-left-nav-title">
                自动发卡工具
            </div>
            <div class="tpl-left-nav-list">
                <ul class="tpl-left-nav-menu">
                    <li class="tpl-left-nav-item">
                        <a href="https://h5.youzan.com/v2/feature/kUaNSA9asY" target="_blank" class="nav-link">
                            <i class="am-icon-book"></i>
                            <span>使用教程</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwtkmia/tplmsg" class="nav-link <?=isActive('tplmsg')?>">
                            <i class="am-icon-cog"></i>
                            <span>模板消息设置</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list <?=isOpened(array('prizes','recycle','kmi'))?>">
                            <i class="am-icon-send"></i>
                            <span>卡密管理</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="/qwtkmia/kmi" class="<?=isActive(array('kmi'))?>">
                                    <span>添加卡密</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwtkmia/prizes" class="<?=isActive(array('prizes'))?>">
                                    <span>卡密管理</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwtkmia/recycle" class="<?=isActive('recycle')?>">
                                    <span>卡密回收站</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                     <li class="tpl-left-nav-item">
                        <a href="/qwtkmia/items1" class="nav-link <?=isActive('items1')?>">
                            <i class="am-icon-cog"></i>
                            <span>商品管理</span>
                        </a>
                    </li>
                     <li class="tpl-left-nav-item">
                        <a href="/qwtkmia/orders" class="nav-link <?=isActive('orders')?>">
                            <i class="am-icon-cog"></i>
                            <span>发送记录</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        </div>
<?php
$app = 'kmi';
$check = Model::factory('select_experience')->fenzai($bid,$app);
if ($check==false){
    $item = ORM::factory('qwt_item')->where('alias','=',$app)->find();
    $iid = $item->id;
    $timelimit = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',$iid)->find()->expiretime;
    $buyhref = 'http://'.$_SERVER['HTTP_HOST'].'/qwta/product/'.$iid;
    $testsku = ORM::factory('qwt_sku')->where('bid','=',0)->where('iid','=',$iid)->where('tryout','=',1)->find();
    $desc = $testsku->testab;
    //卡密专属
        $numleft = Model::factory('select_experience')->selectnum($bid,$app);
        $desc = '您最多还可以上传'.$numleft.'个卡密，';
}
if ($check==false):?>
?>
<div class="testver">您正在使用试用版<i class="information am-icon-question-circle-o"></i>，将于<?=date('Y-m-d H:i:s',$timelimit)?>到期，<?=$desc?>建议您购买正式版<a href="<?=$buyhref?>">立即购买</a></div>
<script type="text/javascript">
    $('.information').click(function(){
        swal({
            title: "<?=$item->name?>试用版",
            text: "<table class='swaltable'><thead><tr><th>应用规格</th><th>价格</th><th>使用天数</th><th>功能区别</th></tr></thead><tbody><tr><td><?=$item->name?>试用版</td><td>免费</td><td>3天</td><td><?=$testsku->testab?>功能与正式版一致</td></tr><tr><td><?=$item->name?>正式版</td><td><?=$testsku->fullsku?></td><td>按应用规格</td><td><?=$testsku->fullab?></td></tr></tbody></table>",
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
        $('#app_kmi').addClass('active');
        $('.top-bar').addClass('lefter');
        $('#appbox .am-icon-angle-right').addClass('tpl-left-nav-more-ico-rotate');
    })
    </script>

</body>
</html>
