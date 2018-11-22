<?=$father?>
        <!-- header section end-->
<div class="second-menu">
        <div class="tpl-left-nav tpl-left-nav-hover">
            <div class="tpl-left-nav-title">
                管理后台
            </div>
            <div class="tpl-left-nav-list">
                <ul class="tpl-left-nav-menu">
                <?php  if ($_SESSION['qwta']['admin'] >=1):?>
                    <li class="tpl-left-nav-item">
                        <a href="/qwtwdla/logins" class="nav-link <?=isActive('logins')?>">
                            <i class="am-icon-cog"></i>
                            <span>用户管理</span>
                        </a>
                    </li>
                 <?php endif?>
                  <?php  if ($_SESSION['qwta']['admin'] ==0):?>
                     <li class="tpl-left-nav-item">
                        <a href="/qwtwdla/setgoods" class="nav-link <?=isActive('setgoods')?>">
                            <i class="am-icon-shopping-cart"></i>
                            <span>代理应用清单</span>
                        </a>
                    </li>
                      <li class="tpl-left-nav-item">
                        <a href="/qwtwdla/qrcode" class="nav-link <?=isActive('qrcode')?>">
                            <i class="am-icon-shopping-cart"></i>
                            <span>客户管理</span>
                        </a>
                    </li>
                    <?php endif?>
                     <li class="tpl-left-nav-item">
                        <a href="/qwtwdla/ddorder" class="nav-link <?=isActive('ddorder')?>">
                            <i class="am-icon-shopping-cart"></i>
                            <span><?=$_SESSION['qwta']['admin'] ==0?'销售记录':'订单管理'?></span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwtwdla/calculates" class="nav-link <?=isActive('calculates')?>">
                            <i class="am-icon-money"></i>
                            <span><?=$_SESSION['qwta']['admin'] ==0?'对账单':'结算管理'?></span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/qwtwdla/history_scores" class="nav-link <?=isActive('history_scores')?>">
                            <i class="am-icon-users"></i>
                            <span>结算记录</span>
                        </a>
                    </li>
                <?php  if ($_SESSION['qwta']['admin'] >=1):?>
                    <li class="tpl-left-nav-item">
                        <a href="/qwtwdla/hb_pay_list" class="nav-link <?=isActive('hb_pay_list')?>">
                            <i class="am-icon-list"></i>
                            <span>红包充值记录（红包雨）</span>
                        </a>
                    </li>
                 <?php endif?>
                </ul>
            </div>
        </div>
        </div>
        <?=$content?>
        </div>
</section>
    <script src="/qwt/assets/js/amazeui.min.js"></script>
    <script src="/qwt/assets/js/app.js"></script>
</body>
</html>
