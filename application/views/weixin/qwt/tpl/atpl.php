<?php
$action = Request::instance()->action;
function isActive($need) {
  //当前页
  $action = Request::instance()->action;
  if ($action == $need) return 'active';

  if (is_array($need)) foreach ($need as $v) {
    if ($action == $v) return 'active';
  }
}
?>
<?php
$action = Request::instance()->action;
function isOpened($need) {
  //当前页
  $action = Request::instance()->action;
  if ($action == $need) return 'opened';

  if (is_array($need)) foreach ($need as $v) {
    if ($action == $v) return 'opened';
  }
}
?>
<?php
function convert($a){
   switch ($a) {
     case 1:
       echo "qwthbba/home";
       break;
     case 2:
       echo "qwtwdba/home";
       break;
     case 3:
       echo "qwtwfba/home";
        break;
     case 4:
       echo "qwtrwba/home";
        break;
    case 5:
       echo "qwtkmia/tplmsg";
        break;
    case 6:
       echo "qwtgla/text";
        break;
    case 7:
       echo "qwtqfxa/home";
        break;
    case 8:
       echo "qwtfxba/home";
        break;
    case 9:
       echo "qwtdlda/home";
        break;
    case 10:
       echo "qwtyyba/home";
        break;
    case 11:
       echo "qwtwzba/information";
        break;
    case 12:
       echo "qwtxxba/home";
        break;
    case 13:
       echo "qwtdkaa/home";
        break;
    case 14:
       echo "qwthbya/home";
        break;
    case 15:
       echo "qwtywma/home";
        break;
    case 16:
       echo "qwtyyxa/home";
        break;
    case 17:
       echo "qwtyyhba/items";
        break;
    case 18:
       echo "qwttbta/qrcodes_m";
        break;
    case 20:
       echo "qwtzdfa/msgs";
        break;
    case 22:
       echo "qwtmnba/groups";
        break;
    case 23:
       echo "qwthfca/qrcodes";
        break;
    case 24:
       echo "qwtkjba/home";
        break;
    case 25:
       echo "qwtrwda/home";
        break;
    case 26:
       echo "qwtmbba/home";
        break;
    case 27:
       echo "qwtxdba/home";
        break;
     default:
       echo "";
       break;
   }
}
?>
<?php
function convert2($a){
   switch ($a) {
     case 1:
       echo "hbb";
       break;
     case 2:
       echo "wdb";
       break;
     case 3:
       echo "wfb";
        break;
     case 4:
       echo "rwb";
        break;
    case 5:
       echo "kmi";
        break;
    case 6:
       echo "gl";
        break;
    case 7:
       echo "qfx";
        break;
    case 8:
       echo "fxb";
        break;
    case 9:
       echo "dld";
        break;
    case 10:
       echo "yyb";
        break;
    case 11:
       echo "wzb";
        break;
    case 12:
       echo "xxb";
        break;
    case 13:
       echo "dka";
        break;
    case 14:
       echo "hby";
        break;
    case 15:
       echo "ywm";
        break;
    case 16:
       echo "yyx";
        break;
    case 17:
       echo "yyhb";
        break;
    case 18:
       echo "tbt";
        break;
    case 19:
       echo "ydd";
        break;
    case 20:
       echo "zdf";
        break;
    case 22:
       echo "mnb";
        break;
    case 23:
       echo "hfc";
        break;
    case 24:
       echo "kjb";
        break;
    case 25:
       echo "rwd";
        break;
    case 26:
       echo "mbb";
        break;
    case 27:
       echo "xdb";
        break;
     default:
       echo "";
       break;
   }
}
?>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?=$title?>|神码浮云</title>
    <meta name="description" content="神码浮云">
    <meta name="keywords" content="index">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
  <meta name="description" content="">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="icon" type="image/png" href="/qwt/assets/i/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="/qwt/assets/i/app-icon72x72@2x.png">
    <meta name="apple-mobile-web-app-title" content="Amaze UI" />
    <link rel="stylesheet" href="/qwt/assets/css/amazeui.min.css" />
    <link rel="stylesheet" href="/qwt/assets/css/admin.css">
    <link rel="stylesheet" href="/qwt/assets/css/app.css">
    <link rel="stylesheet" href="/qwt/assets/css/sweetalert.css">
    <script src="/qwt/assets/js/echarts.min.js"></script>
    <script src="/qwt/assets/js/sweetalert.min.js"></script>
    <script src="/qwt/assets/js/jquery.min.js"></script>
    <script src="/qwt/assets/js/iscroll.js"></script>
</head>
<style type="text/css">

            @media only screen and (max-width: 500px){
                .htext{
                    font-size: 22px;
                }
            }
            .mcontent{
                padding:10px;
                text-align: center;
            }
            .message_box{
                position: fixed;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                display: -webkit-box;
                -webkit-box-pack: center;
                -webkit-box-align: center;
                z-index: 1002;}
            .message_box.mask{
                background-color: rgba(0, 0, 0, .7);
                -webkit-animation: fadeIn .6s ease;
            }
            .message_box > div{
                padding-top: 25px;
                height:auto;


                top:150px;
                width: 500px;

                background-color: #ffffff;
                border-radius: 7px;
                font-size: 16px;
                -webkit-animation: fadeIn1 .6s ease;
            }
            .message_box .btns{
              border-radius: 0 0 7px 7px;
                display: -webkit-box;
                line-height: 40px;
                border-top:1px solid rgb(203, 203, 203);
                background-color:#00a65a;
                text-align:center;
                padding-top:10px;
                padding-bottom:10px;
            }
            .message_box .ok{
                color:white;
                background-color:#008749;
                width:100px;
                margin-left:200px;
                border-radius:10px;
                display:inline-block;
            }
            .opened + ul {
                display: block;
            }
            .barrage{
                position: absolute;
                top:10px;
                left:17%;
                width:73%;
                color:#EE7600;
                display: inline-block;
            }
            .top-bar{
              margin-left: 160px;
              margin-top: 20px;
              padding: 10px;
              border: 1px solid #dedede;
              background: #bd0404;
              font-size: 14px;
              color: #fff;
              margin-right: 20px;
              font-weight: bold;
            }
            #app_ydd{
              display: none;
            }
            .lefter{
              margin-left: 300px;
            }
            .testver{
                  margin-left: 300px;
                  margin-right: 20px;
                  border-radius: 10px;
                  padding: 10px 20px;
                  background-color: #ff7070;
                  color: #fff;
                  font-weight: bold;
            }
            .testver a{
                background-color: #37b6c3;
                padding: 5px;
                border-radius: 5px;
                margin-left: 10px;
                color: #fff;
            }
.swaltable{
    text-align: center;
    border-radius: 5px;
    border: 1px solid green;
}
.swaltable thead tr th{
    text-align: center;
    padding: 5px 10px;
    font-weight: bold;
    white-space: nowrap;
    border-left: 1px solid #efefef;
}
.swaltable thead tr th:first-child{
    border-left:0;
}
.swaltable tbody tr td{
    padding: 5px 10px;
    border-top: 1px solid #efefef;
    /*white-space: nowrap;*/
    border-left: 1px solid #efefef;
}
.swaltable tbody tr td:first-child{
    border-left:0;
}
.testicon{
  white-space: nowrap;
  border-radius: 1px;
  padding: 1px 2px;
  border: 1px solid #ff7070;
}
</style>
<body data-type="index">
<section>
<?php if($barrage):?>
  <div class="barrage">
      <marquee style="font-size:18px;" behavior="alternate" bgcolor="" direction="right" scrolldelay="200"><?=$barrage?></marquee>
  </div>
<?php endif?>
    <header class="am-topbar-inverse first-menu">
    <div class="user-avator"><img src="/qwt/assets/img/logo.png"></div>
        <div class="tpl-left-nav-hover">
            <div class="tpl-left-nav-list">
                <ul class="tpl-left-nav-menu">
                    <li class="tpl-left-nav-item tpl-first-menu">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list <?=isOpened(array('userinfo', 'order','rebuy'))?>">
                            <i class="am-icon-user"></i>
                            <span>会员中心</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="/qwta/userinfo" class="<?=isActive('userinfo')?>">
                                    <span>账户信息</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwta/order" class="<?=isActive('order')?>">
                                    <span>订购记录</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwta/rebuy" class="<?=isActive('rebuy')?>">
                                    <span>续费信息</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="tpl-left-nav-item tpl-first-menu">
                        <a href="/qwta/products" class="nav-link <?=isActive(array('products', 'product'))?>">
                            <i class="am-icon-shopping-cart"></i>
                            <span>购买应用</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item tpl-first-menu">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list <?=isOpened(array('oauth','yzoauth', 'wxpay'))?>">
                            <i class="am-icon-link"></i>
                            <span>绑定我们</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="/qwta/oauth" class="<?=isActive('oauth')?>">
                                    <span>微信一键授权</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwta/yzoauth" class="<?=isActive('yzoauth')?>">
                                    <span>有赞一键授权</span>
                                </a>
                            </li>
                            <li>
                                <a href="/qwta/wxpay" class="<?=isActive('wxpay')?>">
                                    <span>微信支付</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="tpl-left-nav-item tpl-first-menu">
                        <a id="appbox" href="javascript:;" class="nav-link tpl-left-nav-link-list <?=isOpened('switch')?>">
                            <i class="am-icon-th-large"></i>
                            <span>应用中心</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="/qwta/switch" class="<?=isActive('switch')?>">
                                    <span>应用开关</span>
                                </a>
                            </li>
                <?php foreach ($todo['hasbuy'] as $hasbuy): ?>
                            <li class="<?=isActive('<?=convert2($hasbuy->iid)?>')?>">
                                <a id="app_<?=convert2($hasbuy->iid)?>" href="http://<?=$_SERVER['HTTP_HOST']?>/<?=convert($hasbuy->iid)?>" style="padding-right:30px;">
                                    <span><?=$hasbuy->item->name?></span>
                                <?php
                                $app = $hasbuy->item->alias;
                                $check = Model::factory('select_experience')->fenzai($bid,$app);
                                if ($check == false):?>
                                <!-- <span class='testicon' style="color:#ff7070;font-size:8px">试用版</span> -->
                                <img src="/qwt/images/testicon.png" style="height:30px;position:absolute;">
                              <?php endif?>
                                </a>
                            </li>
                        <?php endforeach ?>
                        </ul>
                    </li>
                    <li class="tpl-left-nav-item tpl-first-menu">
                        <a href="/qwta/diy" class="nav-link <?=isActive('diy')?>">
                            <i class="am-icon-list"></i>
                            <span>自定义菜单</span>
                        </a>
                    </li>
           <!--      <?php  if ($_SESSION['qwta']['admin'] >1):?>
                    <li class="tpl-left-nav-item tpl-first-menu">
                        <a href="/qwta/logins" class="nav-link <?=isActive('logins')?>">
                            <i class="am-icon-credit-card"></i>
                            <span>账号管理</span>
                        </a>
                    </li>
                <?php endif?> -->
                <?php  if ($_SESSION['qwta']['dlflag'] >=1&&$_SESSION['qwta']['admin'] >=1):?>
                    <li class="tpl-left-nav-item tpl-first-menu">
                        <a href="/qwtwdla/logins" class="nav-link <?=isActive('logins')?>">
                            <i class="am-icon-credit-card"></i>
                            <span>管理后台</span>
                        </a>
                    </li>
                <?php endif?>
                <?php  if ($_SESSION['qwta']['dlflag'] >=1&&$_SESSION['qwta']['admin'] ==0):?>
                    <li class="tpl-left-nav-item tpl-first-menu">
                        <a href="/qwtwdla/setgoods" class="nav-link <?=isActive('setgoods')?>">
                            <i class="am-icon-credit-card"></i>
                            <span>管理后台</span>
                        </a>
                    </li>
                <?php endif?>
                    <li class="tpl-left-nav-item tpl-first-menu">
                        <a href="http://<?=$_SERVER['HTTP_HOST']?>/qwta/login" class="nav-link">
                            <i class="am-icon-home"></i>
                            <span>进入主页</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item tpl-first-menu" id="kf">
                        <a class="nav-link">
                            <i class="am-icon-user"></i>
                            <span>联系客服</span>
                        </a>
                    </li>
                    <!-- <li class="tpl-left-nav-item tpl-first-menu">
                        <a onclick="yztop()" class="nav-link">
                            <i class="am-icon-list-ol"></i>
                            <span>有赞风云榜</span>
                        </a>
                    </li> -->
                    <li class="tpl-left-nav-item tpl-first-menu">
                        <a href="/qwta/layout" class="nav-link">
                            <i class="am-icon-sign-out"></i>
                            <span>退出登录</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item tpl-first-menu">
                        <a class="nav-link" href="javascript:;" id="admin-fullscreen">
                            <i class="am-icon-arrows-alt"></i>
                            <span class="admin-fullText">开启全屏</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    <!-- <div class="user-name">
                    <a href="http://<?=$_SERVER['HTTP_HOST']?>/qwta/login" target="_blank">
                            <i class="am-icon-home"></i><span>进入主页</span></a></div>
    <div class="user-name" style="margin-top:10px;">
                    <a href="/qwta/layout">
                            <i class="am-icon-sign-out"></i><span>退出登录</span></a></div>
    <div class="user-name kf" style="margin-top:10px;">
                    <a>
                            <i class="am-icon-user"></i><span>联系客服</span></a></div> -->
    </header>
    <!-- <div class="top-bar">
    公告：2017年1月17日之前购买的用户，需要使用有赞优惠券相关的功能，请在绑定我们-有赞一键授权，选择重新授权有赞，我方已对有赞优惠券相关的功能做了升级。
    </div> -->
    <div class="main-content">
        <?=$content?>
    </div>
</section>
    <script type="text/javascript">
            $(document).on('click','#kf',function(){
                console.log(2);
                $('body').append('<div class=\'message_box mask\'>'+
                                          '<div>'+
                                          '<div class=\'mcontent\'>'+
                                           '<div style=\'margin-top:5px;text-align:left;\'>1.工作时间：周一到周五9:30-18:30；<br>2.客服电话：15926434187；<br>3.客服微信：扫码添加微信，方便快速沟通问题（优先回复）。</div>'+
                                           '<img style=\'width:200px\' src=\'/qwt/images/qr.jpg\'>'+
                                          '</div>'+
                                          '<div class=\'btns\'><span  class=\'ok\'>确定</span></div>'+
                                          '</div>'+
                                          '</div>');
            })

                $(document).on('click', '.ok', function() {
                               $(".message_box").fadeOut();
                          });
        $(document).ready(function(){
            $('.tpl-left-nav-item').children('.active').children('.am-icon-angle-right').addClass('tpl-left-nav-more-ico-rotate');
            $('.tpl-left-nav-item').children('.opened').children('.am-icon-angle-right').addClass('tpl-left-nav-more-ico-rotate');
        });
        function yztop(){
            swal({
                title: "有赞风云榜",
                text: "使用微信扫码查看",
                imageUrl: "/qwt/images/yztop.png",
                imageSize: "200x200",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "我知道了",
                closeOnConfirm: true,
                closeOnCancel: true,
                  });
        };
    </script>

</body>

</html>
