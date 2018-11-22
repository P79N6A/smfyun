<?=$father?>
        <!-- header section end-->
<div class="second-menu">
        <div class="tpl-left-nav tpl-left-nav-hover">
            <div class="tpl-left-nav-title">
                充值拼团
            </div>
            <div class="tpl-left-nav-list">
                <ul class="tpl-left-nav-menu">
                    <li class="tpl-left-nav-item">
                        <a href="/qwthfca/qrcodes" class="nav-link <?=isActive('qrcodes')?>">
                            <i class="am-icon-user"></i>
                            <span>用户管理</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwthfca/groups" class="nav-link <?=isActive('groups')?>">
                            <i class="am-icon-users"></i>
                            <span>成团管理</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwthfca/items" class="nav-link <?=isActive('items')?>">
                            <i class="am-icon-th-large"></i>
                            <span>商品管理</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a class="nav-link tpl-left-nav-link-list <?=isOpened(array('group_orders', 'orders'))?>">
                            <i class="am-icon-list-ol"></i>
                            <span>订单管理</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="/qwthfca/group_orders" class="<?=isActive('group_orders')?>">
                                    <span>已成团订单</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwthfca/orders" class="<?=isActive('orders')?>">
                                    <span>未成团订单</span>
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
        $('#app_hfc').addClass('active');
        $('.top-bar').addClass('lefter');
        $('#appbox .am-icon-angle-right').addClass('tpl-left-nav-more-ico-rotate');
    })
    </script>

</body>
</html>
