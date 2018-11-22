<?=$father?>
        <!-- header section end-->
<div class="second-menu">
        <div class="tpl-left-nav tpl-left-nav-hover">
            <div class="tpl-left-nav-title">
                特别特
            </div>
            <div class="tpl-left-nav-list">
                <ul class="tpl-left-nav-menu">
                    <!-- <li class="tpl-left-nav-item">
                        <a href="https://h5.youzan.com/v2/feature/in9l5Rfcbt" target="_blank" class="nav-link">
                            <i class="am-icon-book"></i>
                            <span>使用教程</span>
                        </a>
                    </li> -->
                    <li class="tpl-left-nav-item">
                        <a href="/qwttbta/qrcodes_m" class="nav-link <?=isActive('qrcodes_m')?>">
                            <i class="am-icon-cog"></i>
                            <span>会员管理</span>
                        </a>
                    </li>
                    <!-- <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list <?=isOpened(array('qrcodes','qrcodes_m'))?>">
                            <i class="am-icon-user"></i>
                            <span>用户明细</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="/qwttbta/qrcodes" class="<?=isActive('qrcodes')?>">
                                    <span>待审核用户</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwttbta/qrcodes_m" class="<?=isActive('qrcodes_m')?>">
                                    <span>已审核用户</span>
                                </a>
                            </li>
                        </ul>
                    </li> -->
                </ul>
            </div>
        </div>
        </div>
        <?=$content?>
        </div>
</section>
    <script src="/qwt/assets/js/amazeui.min.js"></script>
    <script src="/qwt/assets/js/app.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){
        $('#appbox').addClass('opened');
        $('#app_tbt').addClass('active');
        $('.top-bar').addClass('lefter');
        $('#appbox .am-icon-angle-right').addClass('tpl-left-nav-more-ico-rotate');
    })
    </script>

</body>
</html>
