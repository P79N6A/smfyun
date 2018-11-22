<?=$father?>
        <!-- header section end-->
<div class="second-menu">
        <div class="tpl-left-nav tpl-left-nav-hover">
            <div class="tpl-left-nav-title">
                蒙牛数据开发
            </div>
            <div class="tpl-left-nav-list">
                <ul class="tpl-left-nav-menu">
                    <li class="tpl-left-nav-item">
                        <a href="/qwtmnba/groups" class="nav-link <?=isActive('groups')?>">
                            <i class="am-icon-users"></i>
                            <span>代理等级</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwtmnba/qrcodes" class="nav-link <?=isActive('qrcodes')?>">
                            <i class="am-icon-user"></i>
                            <span>代理列表</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwtmnba/types" class="nav-link <?=isActive('types')?>">
                            <i class="am-icon-th-large"></i>
                            <span>问题分类</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwtmnba/faqs" class="nav-link <?=isActive('faqs')?>">
                            <i class="am-icon-list-ol"></i>
                            <span>问题列表</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwtmnba/csv" class="nav-link <?=isActive('csv')?>">
                            <i class="am-icon-upload"></i>
                            <span>导入</span>
                        </a>
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
        $('#app_mnb').addClass('active');
        $('.top-bar').addClass('lefter');
        $('#appbox .am-icon-angle-right').addClass('tpl-left-nav-more-ico-rotate');
    })
    </script>

</body>
</html>
