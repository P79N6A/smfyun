<?=$father?>
        <!-- header section end-->
<div class="second-menu">
        <div class="tpl-left-nav tpl-left-nav-hover">
            <div class="tpl-left-nav-title">
                一物一码
            </div>
            <div class="tpl-left-nav-list">
                <ul class="tpl-left-nav-menu">
                    <li class="tpl-left-nav-item">
                        <a href="https://www.baidu.com" target="_blank" class="nav-link">
                            <i class="am-icon-book"></i>
                            <span>使用教程</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="home" class="nav-link <?=isActive('home')?>">
                            <i class="am-icon-cog"></i>
                            <span>个性化设置</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="marketing" class="nav-link <?=isActive('marketing')?>">
                            <i class="am-icon-cog"></i>
                            <span>营销模块</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="account" class="nav-link <?=isActive('account')?>">
                            <i class="am-icon-cog"></i>
                            <span>红包充值</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list <?=isOpened(array('setgoods','good','code_totle'))?>">
                            <i class="am-icon-bar-chart"></i>
                            <span>商品管理及红包码下载</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="setgood" class="<?=isActive('setgoods')?>">
                                    <span>有赞商品管理及红包码下载</span>
                                </a>
                            </li>
                            <li>
                                <a href="good" class="<?=isActive('good')?>">
                                    <span>其他商品管理及红包码下载</span>
                                </a>
                            </li>
                            <li>
                                <a href="code_totle" class="<?=isActive('code_totle')?>">
                                    <span>红包码数据统计</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="sendrecord" class="nav-link <?=isActive('sendrecord')?>">
                            <i class="am-icon-cog"></i>
                            <span>发送记录</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="qrcodes" class="nav-link <?=isActive('qrcodes')?>">
                            <i class="am-icon-cog"></i>
                            <span>扫码记录</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list <?=isOpened(array('getdata', 'item_totle'))?>">
                            <i class="am-icon-bar-chart"></i>
                            <span>数据统计</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                            <a href="getdata" class="<?=isActive('getdata')?>">
                                <span>概况</span>
                            </a>
                            </li>
                            <li>
                            <a href="item_totle" class="<?=isActive('item_totle')?>">
                                <span>扫码统计</span>
                            </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        </div>
        <?=$content?>


    </div>
    <!-- main content end-->
</section>
    <script src="/qwt/assets/js/amazeui.min.js"></script>
    <script src="/qwt/assets/js/app.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){
        $('#appbox').addClass('opened');
        $('#app_ywm').addClass('active');
        $('.top-bar').addClass('lefter');
        $('#appbox .am-icon-angle-right').addClass('tpl-left-nav-more-ico-rotate');
    })
    </script>

</body>
</html>
